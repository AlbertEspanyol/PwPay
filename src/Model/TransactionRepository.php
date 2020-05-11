<?php
declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Model;

interface TransactionRepository
{
    public function addTransaction(Transaction $trans): void;
    public function getLatest5Trans(int $id): array;
    public function updateStatus(int $id): void;
}