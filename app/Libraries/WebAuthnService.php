<?php

namespace App\Libraries;

use Cose\Algorithm\Manager;
use Cose\Algorithm\Signature\ECDSA\ES256;
use Cose\Algorithm\Signature\RSA\RS256;
use Config\App;
use ParagonIE\ConstantTime\Base64UrlSafe;
use RuntimeException;
use Symfony\Component\Uid\Uuid;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AttestationStatement\PackedAttestationStatementSupport;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorDataLoader;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\CollectedClientData;
use Webauthn\CredentialRecord;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\TrustPath\EmptyTrustPath;

/**
 * Verificacion WebAuthn (huella/Face ID) del lado del servidor con
 * web-auth/webauthn-lib. La llave publica COSE de la credencial se guarda en
 * la tabla `webauthn_credentials` para poder validar la firma de cada marca.
 */
class WebAuthnService
{
    private const TIMEOUT = 60000;

    private ?AttestationObjectLoader $attestationLoader = null;

    // ── base64url ────────────────────────────────────────────────
    public static function b64e(string $binary): string
    {
        return Base64UrlSafe::encodeUnpadded($binary);
    }

    public static function b64d(string $encoded): string
    {
        if ($encoded === '') {
            return '';
        }

        $value = rtrim($encoded, '=');

        try {
            return Base64UrlSafe::decodeNoPadding($value);
        } catch (\Throwable $e) {
            // Algunos navegadores envian base64 estandar en lugar de base64url.
            try {
                return \ParagonIE\ConstantTime\Base64::decodeNoPadding($value);
            } catch (\Throwable $e2) {
                throw new RuntimeException('b64d invalido [' . substr($encoded, 0, 16) . '] len=' . strlen($encoded));
            }
        }
    }

    // ── origen / rp id ───────────────────────────────────────────
    /** Host (sin puerto) usado como Relying Party ID. */
    public function host(): string
    {
        $raw = $this->httpHost();
        if (preg_match('/^\[([^\]]+)\]/', $raw, $m) === 1) {
            return $m[1];
        }

        return (string) preg_replace('/:\d+$/', '', $raw);
    }

    /** Origen completo (esquema://host[:puerto]) del que llega la peticion. */
    public function origin(): string
    {
        return $this->https() . '://' . $this->httpHost();
    }

    /** Origenes aceptados: el de la peticion actual + el de app.baseURL. */
    public function allowedOrigins(): array
    {
        $origins = array_filter([$this->origin(), $this->baseOrigin()]);

        return array_values(array_unique($origins));
    }

    private function httpHost(): string
    {
        $raw = strtolower((string) (service('request')->getServer('HTTP_HOST') ?? ''));
        if ($raw === '') {
            $raw = strtolower($this->baseHost());
        }
        if (str_contains($raw, '@') === true) {
            $raw = (string) substr(strrchr($raw, '@'), 1);
        }

        return $raw;
    }

    private function https(): string
    {
        $https = strtolower((string) (service('request')->getServer('HTTPS') ?? ''));
        $scheme = strtolower((string) (service('request')->getServer('REQUEST_SCHEME') ?? ''));

        return ($https !== '' && $https !== 'off') || $scheme === 'https' ? 'https' : 'http';
    }

    private function baseURL(): string
    {
        return (string) config(App::class)->baseURL;
    }

    private function baseOrigin(): string
    {
        $parts = parse_url($this->baseURL());
        if (! is_array($parts) || ! isset($parts['host'])) {
            return '';
        }

        return ($parts['scheme'] ?? 'http') . '://' . strtolower($parts['host'])
            . (isset($parts['port']) ? ':' . $parts['port'] : '');
    }

    private function baseHost(): string
    {
        $parts = parse_url($this->baseURL());

        return (string) ($parts['host'] ?? 'localhost');
    }

    // ── handle del usuario (identificador estable, 32 bytes) ─────
    public function userHandle(int $userId): string
    {
        return hash('sha256', 'webauthn-user:' . $userId, true);
    }

    public function userHandleB64(int $userId): string
    {
        return self::b64e($this->userHandle($userId));
    }

    // ── constructores de opciones ────────────────────────────────
    public function creationOptions(string $challengeB64, int $userId, string $name, string $email): PublicKeyCredentialCreationOptions
    {
        $rp = PublicKeyCredentialRpEntity::create('', $this->host());
        $user = PublicKeyCredentialUserEntity::create(
            $email !== '' ? $email : $name,
            $this->userHandle($userId),
            $name !== '' ? $name : $email
        );

        return new PublicKeyCredentialCreationOptions(
            $rp,
            $user,
            self::b64d($challengeB64),
            [
                PublicKeyCredentialParameters::createPk(-7),
                PublicKeyCredentialParameters::createPk(-257),
            ],
            AuthenticatorSelectionCriteria::create('platform', 'required', 'required'),
            'none',
            [],
            self::TIMEOUT
        );
    }

    /**
     * @param string[] $transports
     */
    public function requestOptions(string $challengeB64, string $credentialIdB64, array $transports = []): PublicKeyCredentialRequestOptions
    {
        $descriptor = PublicKeyCredentialDescriptor::create(
            PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
            self::b64d($credentialIdB64),
            $transports
        );

        return new PublicKeyCredentialRequestOptions(
            self::b64d($challengeB64),
            $this->host(),
            [$descriptor],
            'required',
            self::TIMEOUT
        );
    }

    // ── registro (attestation) ───────────────────────────────────
    public function verifyAttestation(string $challengeB64, PublicKeyCredentialCreationOptions $options, array $post): CredentialRecord
    {
        $attestationB64 = (string) ($post['attestationObject'] ?? '');
        if ($attestationB64 === '') {
            throw new RuntimeException('No se recibio la respuesta del lector biometrico.');
        }

        $clientData     = $this->clientData((string) ($post['clientDataJSON'] ?? ''));
        $attestationObj = $this->attestationLoader()->load($attestationB64);

        $transports = json_decode((string) ($post['transports'] ?? '[]'), true);
        if (! is_array($transports)) {
            $transports = [];
        }
        $transports = array_values(array_filter($transports, 'is_string'));

        $response   = AuthenticatorAttestationResponse::create($clientData, $attestationObj, $transports);
        $validator  = AuthenticatorAttestationResponseValidator::create($this->ceremonyFactory()->creationCeremony());
        $credential = $validator->check($response, $options, $this->host());

        $sentId = (string) ($post['credential_id'] ?? '');
        if ($sentId !== '' && ! hash_equals(self::b64e($credential->publicKeyCredentialId), $sentId)) {
            throw new RuntimeException('La credencial recibida no coincide con la validada.');
        }

        return $credential;
    }

    // ── comprobacion (assertion) ─────────────────────────────────
    public function verifyAssertion(CredentialRecord $record, string $challengeB64, array $post): CredentialRecord
    {
        $sentId = (string) ($post['credential_id'] ?? '');
        if ($sentId !== '' && ! hash_equals(self::b64e($record->publicKeyCredentialId), $sentId)) {
            throw new RuntimeException('La credencial recibida no esta registrada.');
        }

        $fields = $this->decodeFields($post, ['authenticatorData', 'signature', 'userHandle']);
        if ($fields['authenticatorData'] === '' || $fields['signature'] === '') {
            throw new RuntimeException('Faltan datos de la verificacion.');
        }

        $clientData        = $this->clientData((string) ($post['clientDataJSON'] ?? ''));
        $authenticatorData = AuthenticatorDataLoader::create()->load($fields['authenticatorData']);
        $userHandle        = $fields['userHandle'] !== '' ? $fields['userHandle'] : null;

        $response   = AuthenticatorAssertionResponse::create($clientData, $authenticatorData, $fields['signature'], $userHandle);
        $options    = $this->requestOptions($challengeB64, self::b64e($record->publicKeyCredentialId), $record->transports);
        $validator  = AuthenticatorAssertionResponseValidator::create($this->ceremonyFactory()->requestCeremony());

        return $validator->check($record, $response, $options, $this->host(), $record->userHandle);
    }

    // ── persistencia ─────────────────────────────────────────────
    /** @return array<string, mixed> fila para `webauthn_credentials` */
    public function toRow(CredentialRecord $record, int $userId): array
    {
        return [
            'user_id'          => $userId,
            'credential_id'    => self::b64e($record->publicKeyCredentialId),
            'public_key'       => self::b64e($record->credentialPublicKey),
            'counter'          => $record->counter,
            'user_handle'      => self::b64e($record->userHandle),
            'aaguid'           => $record->aaguid->toRfc4122(),
            'attestation_type' => $record->attestationType,
            'transports'       => implode(',', array_values($record->transports)),
            'backup_eligible'  => $record->backupEligible === null ? null : (int) $record->backupEligible,
            'backup_status'    => $record->backupStatus === null ? null : (int) $record->backupStatus,
            'uv_initialized'   => $record->uvInitialized === null ? null : (int) $record->uvInitialized,
            'created_at'       => date('Y-m-d H:i:s'),
        ];
    }

    /** @param array<string, mixed> $row */
    public function fromRow(array $row): CredentialRecord
    {
        $aaguid = (string) ($row['aaguid'] ?? '');
        $transports = array_values(array_filter(explode(',', (string) ($row['transports'] ?? '')), 'strlen'));
        $eligible = $row['backup_eligible'] ?? null;
        $backed   = $row['backup_status'] ?? null;
        $uv       = $row['uv_initialized'] ?? null;

        return CredentialRecord::create(
            self::b64d((string) $row['credential_id']),
            PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
            $transports,
            (string) ($row['attestation_type'] ?? 'none'),
            EmptyTrustPath::create(),
            $aaguid !== '' ? Uuid::fromString($aaguid) : Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            self::b64d((string) $row['public_key']),
            self::b64d((string) $row['user_handle']),
            (int) ($row['counter'] ?? 0),
            null,
            $eligible === null ? null : (bool) $eligible,
            $backed === null ? null : (bool) $backed,
            $uv === null ? null : (bool) $uv
        );
    }

    // ── internos ─────────────────────────────────────────────────
    /** @return array<int, string> bytes decodificados */
    private function decodeFields(array $post, array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            $raw = (string) ($post[$key] ?? '');
            if ($raw === '') {
                $out[$key] = '';
                continue;
            }
            try {
                $out[$key] = self::b64d($raw);
            } catch (\Throwable $e) {
                throw new RuntimeException('Dato invalido en "' . $key . '": ' . substr($raw, 0, 24));
            }
        }

        return $out;
    }

    private function clientData(string $clientDataB64): CollectedClientData
    {
        if ($clientDataB64 === '') {
            throw new RuntimeException('No se recibio clientDataJSON.');
        }

        $json = self::b64d($clientDataB64);
        $data = json_decode($json, true);
        if (! is_array($data)) {
            throw new RuntimeException('clientDataJSON invalido.');
        }

        try {
            return CollectedClientData::create($json, $data);
        } catch (\Throwable $e) {
            throw new RuntimeException('clientData invalido: ' . $e->getMessage());
        }
    }

    private function ceremonyFactory(): CeremonyStepManagerFactory
    {
        $factory = new CeremonyStepManagerFactory();
        $factory->setAllowedOrigins($this->allowedOrigins(), false);

        return $factory;
    }

    private function attestationLoader(): AttestationObjectLoader
    {
        if ($this->attestationLoader === null) {
            $algorithms = Manager::create()->add(ES256::create(), RS256::create());
            $this->attestationLoader = AttestationObjectLoader::create(
                AttestationStatementSupportManager::create([
                    new NoneAttestationStatementSupport(),
                    PackedAttestationStatementSupport::create($algorithms),
                ])
            );
        }

        return $this->attestationLoader;
    }
}
