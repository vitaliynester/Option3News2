<?php

namespace App\Model\User;

class SignUpResponse
{
    private string $token;
    private int $userId;

    public function __construct(int $userId, string $token)
    {
        $this->userId = $userId;
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
