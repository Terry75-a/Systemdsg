<?php

namespace App\Services\Shared;

class ServiceResult
{
    protected bool $success;
    protected string $message;
    protected int $status;
    protected ?array $extra;

    public function __construct(
        bool $success,
        string $message,
        int $status = 200,
        ?array $extra = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->status = $status;
        $this->extra = $extra;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
        ] + ($this->extra ?? []);
    }
}