<?php

namespace App\Model;

use Exception;

class ErrorResponse
{
    private int $code;
    private string $message;

    public function __construct(Exception $exception)
    {
        $this->code = $exception->getCode();
        $this->message = $exception->getMessage();
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
