<?php

namespace App\Services;

/**
 * Web Push sin dependencias externas (RFC 8292 VAPID + RFC 8291 aes128gcm)
 * usando sólo openssl y streams (el servidor no tiene ext-curl).
 */
class WebPushService
{
    private string $publicKey;
    private string $privatePem;
    private string $subject;

    public function __construct()
    {
        $this->publicKey = (string) (env('VAPID_PUBLIC_KEY') ?: '');
        $this->privatePem = base64_decode((string) (env('VAPID_PRIVATE_KEY_B64') ?: ''), true) ?: '';
        $this->subject = (string) (env('VAPID_SUBJECT') ?: 'mailto:admin@dsg.pe');
    }

    public function publicKey(): string
    {
        return $this->publicKey;
    }

    public function isConfigured(): bool
    {
        return $this->publicKey !== '' && $this->privatePem !== '';
    }

    /** Envía payload JSON a todas las suscripciones de los usuarios dados. */
    public function enviarAUsuarios(array $userIds, string $titulo, string $mensaje, string $url = '', string $tag = 'dsg'): void
    {
        if (! $this->isConfigured() || $userIds === []) {
            return;
        }
        $userIds = array_values(array_unique(array_map('intval', $userIds)));
        $subs = (new \App\Models\PushSubscriptionModel())->deUsuarios($userIds);
        if (! $subs) {
            return;
        }

        $payload = json_encode([
            'title' => $titulo,
            'body'  => $mensaje,
            'url'   => $url,
            'tag'   => $tag,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $modelo = new \App\Models\PushSubscriptionModel();
        foreach ($subs as $sub) {
            $codigo = $this->enviarAEndpoint((string) $sub['endpoint'], (string) $sub['p256dh'], (string) $sub['auth'], $payload);
            if (in_array($codigo, [404, 410], true)) {
                $modelo->delete((int) $sub['id']);
                continue;
            }
            if ($codigo > 0) {
                $modelo->update((int) $sub['id'], ['last_seen' => date('Y-m-d H:i:s')]);
            }
        }
    }

    /** @return int código HTTP (0 = error de red) */
    public function enviarAEndpoint(string $endpoint, string $p256dh, string $auth, string $payload): int
    {
        $cifrado = $this->cifrar($payload, $p256dh, $auth);
        if ($cifrado === null) {
            return 0;
        }

        $parts = parse_url($endpoint);
        if (! is_array($parts) || ! isset($parts['host'])) {
            return 0;
        }
        $origen = ($parts['scheme'] ?? 'https') . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');

        $jwt = $this->vapidJwt($origen, time() + 12 * 3600);
        if ($jwt === '') {
            return 0;
        }

        $cabeceras = 'Authorization: vapid t=' . $jwt . ', k=' . $this->publicKey . "\r\n"
            . "Content-Encoding: aes128gcm\r\n"
            . 'TTL: 86400' . "\r\n"
            . "Content-Type: application/octet-stream\r\n"
            . 'Content-Length: ' . strlen($cifrado) . "\r\n";

        $ctx = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => $cabeceras,
                'content'       => $cifrado,
                'timeout'       => 10,
                'ignore_errors' => true,
            ],
        ]);

        $http_response_header = [];
        @file_get_contents($endpoint, false, $ctx);
        $codigo = 0;
        foreach ($http_response_header as $h) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) {
                $codigo = (int) $m[1];
            }
        }
        return $codigo;
    }

    // ── Cifrado aes128gcm (RFC 8291) ─────────────────────────────

    private function cifrar(string $texto, string $p256dhB64, string $authB64): ?string
    {
        $clientePub = $this->b64urlDecode($p256dhB64);
        $auth = $this->b64urlDecode($authB64);
        if (strlen($clientePub) !== 65 || $clientePub[0] !== "\x04" || strlen($auth) !== 16) {
            return null;
        }

        $local = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        if (! $local) {
            return null;
        }
        $det = openssl_pkey_get_details($local);
        $localPub = "\x04" . $det['ec']['x'] . $det['ec']['y'];

        $claveCliente = openssl_pkey_get_public($this->puntoAPem($clientePub));
        if (! $claveCliente) {
            return null;
        }
        $compartida = $this->ecdh($local, $claveCliente);
        if ($compartida === null) {
            return null;
        }

        $prkKey = $this->hkdfExtract($auth, $compartida);
        $ikm = $this->hkdfExpand($prkKey, "WebPush: info\0" . $localPub . $clientePub . "\x01", 32);

        $salt = random_bytes(16);
        $prkSalt = $this->hkdfExtract($salt, $ikm);
        $cek = $this->hkdfExpand($prkSalt, "Content-Encoding: aes128gcm\0", 16);
        $nonce = $this->hkdfExpand($prkSalt, "Content-Encoding: nonce\0", 12);

        $cifrado = openssl_encrypt($texto . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag);
        if ($cifrado === false) {
            return null;
        }

        // salt(16) | rs(4)=4096 | idlen(1) | keyid(localPub) | ciphertext+tag
        return $salt . pack('N', 4096) . chr(strlen($localPub)) . $localPub . $cifrado . $tag;
    }

    private function hkdfExtract(string $salt, string $ikm): string
    {
        return hash_hmac('sha256', $ikm, $salt, true);
    }

    /**
     * ECDH P-256 vía CLI de openssl (openssl_pkey_derive está roto para EC
     * en este build de PHP 8.3 + OpenSSL 3.0).
     */
    private function ecdh(\OpenSSLAsymmetricKey $local, \OpenSSLAsymmetricKey $cliente): ?string
    {
        if (! openssl_pkey_export($local, $localPem)) {
            return null;
        }
        $det = openssl_pkey_get_details($cliente);
        if (! is_array($det) || ($det['type'] ?? -1) !== OPENSSL_KEYTYPE_EC) {
            return null;
        }
        $pt = "\x04" . $det['ec']['x'] . $det['ec']['y'];
        $clientePem = $this->puntoAPem($pt);

        $dir = WRITEPATH . 'tmp';
        if (! is_dir($dir)) {
            @mkdir($dir, 0700, true);
        }
        $fPriv = $dir . '/ecdh_' . bin2hex(random_bytes(8)) . '.pem';
        $fPub  = $dir . '/ecdh_' . bin2hex(random_bytes(8)) . '.pem';
        if (@file_put_contents($fPriv, $localPem) === false
            || @file_put_contents($fPub, $clientePem) === false) {
            @unlink($fPriv);
            @unlink($fPub);
            return null;
        }

        // Binario: shell_exec conserva los 32 bytes del secreto compartido
        $bin = shell_exec(
            'openssl pkeyutl -derive -inkey ' . escapeshellarg($fPriv)
            . ' -peerkey ' . escapeshellarg($fPub) . ' 2>/dev/null'
        );
        @unlink($fPriv);
        @unlink($fPub);

        if (! is_string($bin) || strlen($bin) < 32) {
            return null;
        }
        return substr($bin, 0, 32);
    }

    private function hkdfExpand(string $prk, string $info, int $len): string
    {
        $salida = '';
        $t = '';
        $i = 1;
        while (strlen($salida) < $len) {
            $t = hash_hmac('sha256', $t . $info . chr($i), $prk, true);
            $salida .= $t;
            $i++;
        }
        return substr($salida, 0, $len);
    }

    // ── VAPID ES256 (RFC 8292) ────────────────────────────────────

    private function vapidJwt(string $origen, int $exp): string
    {
        $cab = $this->b64urlEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $cla = $this->b64urlEncode(json_encode(['aud' => $origen, 'exp' => $exp, 'sub' => $this->subject]));
        $entrada = $cab . '.' . $cla;

        $clave = openssl_pkey_get_private($this->privatePem);
        if (! $clave) {
            return '';
        }
        if (! openssl_sign($entrada, $der, $clave, OPENSSL_ALGO_SHA256)) {
            return '';
        }
        return $entrada . '.' . $this->b64urlEncode($this->derAraw($der));
    }

    /** Firma ECDSA DER → r||s de 32 bytes cada uno. */
    private function derAraw(string $der): string
    {
        $pos = 0;
        $long = strlen($der);
        if ($long < 8 || ord($der[0]) !== 0x30) {
            return str_repeat("\x00", 64);
        }
        $pos = 2;
        if (ord($der[1]) & 0x80) {
            $pos = 2 + (ord($der[1]) & 0x7f);
        }
        $r = $this->derEntero($der, $pos);
        $s = $this->derEntero($der, $pos);
        return $r . $s;
    }

    private function derEntero(string $der, int &$pos): string
    {
        if ($pos + 2 > strlen($der) || ord($der[$pos]) !== 0x02) {
            return str_repeat("\x00", 32);
        }
        $pos++;
        $largo = ord($der[$pos]);
        $pos++;
        $val = substr($der, $pos, $largo);
        $pos += $largo;
        $val = ltrim($val, "\x00");
        if ($val === '') {
            $val = "\x00";
        }
        return str_pad($val, 32, "\x00", STR_PAD_LEFT);
    }

    // ── Utilidades ────────────────────────────────────────────────

    private function puntoAPem(string $punto): string
    {
        $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200') . $punto;
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----";
    }

    private function b64urlEncode(string $datos): string
    {
        return rtrim(strtr(base64_encode($datos), '+/', '-_'), '=');
    }

    private function b64urlDecode(string $datos): string
    {
        return base64_decode(strtr($datos, '-_', '+/') . str_repeat('=', (4 - strlen($datos) % 4) % 4), true) ?: '';
    }
}
