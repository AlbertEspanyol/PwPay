<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Model;

use DateTime;

final class Transaction{
    private int $id;
    private int $source_user;
    private int $dest_user;
    private float $money;
    private string $tipo;
    private string $motiu;
    private DateTime $data;

    /**
     * Transaction constructor.
     * @param int $id
     * @param int $source_user
     * @param int $dest_user
     * @param float $money
     * @param string $tipo
     * @param string $motiu
     * @param DateTime $data
     */
    public function __construct(int $id, int $source_user, int $dest_user, float $money, string $tipo, string $motiu, DateTime $data)
    {
        $this->id = $id;
        $this->source_user = $source_user;
        $this->dest_user = $dest_user;
        $this->money = $money;
        $this->tipo = $tipo;
        $this->motiu = $motiu;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getSourceUser(): int
    {
        return $this->source_user;
    }

    /**
     * @param int $source_user
     */
    public function setSourceUser(int $source_user): void
    {
        $this->source_user = $source_user;
    }

    /**
     * @return int
     */
    public function getDestUser(): int
    {
        return $this->dest_user;
    }

    /**
     * @param int $dest_user
     */
    public function setDestUser(int $dest_user): void
    {
        $this->dest_user = $dest_user;
    }

    /**
     * @return float
     */
    public function getMoney(): float
    {
        return $this->money;
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money): void
    {
        $this->money = $money;
    }

    /**
     * @return string
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return string
     */
    public function getMotiu(): string
    {
        return $this->motiu;
    }

    /**
     * @param string $motiu
     */
    public function setMotiu(string $motiu): void
    {
        $this->motiu = $motiu;
    }

    /**
     * @return DateTime
     */
    public function getData(): DateTime
    {
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData(DateTime $data): void
    {
        $this->data = $data;
    }


}
