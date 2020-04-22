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

    /**
     *
     * @param string $email
     * @param string $password
     * @param string $birthday
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     */

    public function __construct(
        string $email,
        string $password,
        string $birthday,
        DateTime $createdAt,
        DateTime $updatedAt
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->birthday = $birthday;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
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