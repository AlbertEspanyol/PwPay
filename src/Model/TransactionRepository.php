<?php
declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Model;

interface TransactionRepository
{
    public function addTransaction(Transaction $trans): void;
    public function getTrans(int $id,bool $limit): array;
    public function updateStatus(int $id): void;
}