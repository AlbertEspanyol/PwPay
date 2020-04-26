<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Model;

use DateTime;

final class User
{
    private int $id;
    private string $email;
    private string $password;
    private string $birthday;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private float $money;
    private int $active;
    private string $token;

    /**
     *
     * @param string $email
     * @param string $password
     * @param string $birthday
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     * @param float $money
     * @param int $active
     * @param string $token
     */

    public function __construct(
        string $email,
        string $password,
        string $birthday,
        DateTime $createdAt,
        DateTime $updatedAt,
        float $money,
        int $active,
        string $token
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->birthday = $birthday;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->money = $money;
        $this->active = $active;
        $this->token = $token;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function money(): float
    {
        return $this->money;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function birthday(): string
    {
        return $this->birthday;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}