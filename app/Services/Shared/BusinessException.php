<?php

namespace App\Services\Shared;

use CodeIgniter\HTTP\ResponseInterface;

class BusinessException extends \Exception
{
    protected int $statusCode;
    protected ?array $extra = null;

    public function __construct(string $message, int $statusCode = ResponseInterface::HTTP_BAD_REQUEST, ?array $extra = null)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->extra = $extra;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function toServiceResult(): ServiceResult
    {
        return new ServiceResult(
            success: false,
            message: $this->getMessage(),
            status: $this->statusCode,
            extra: $this->extra
        );
    }
}