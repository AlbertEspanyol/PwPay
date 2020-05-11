<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Model;

interface UserRepository
{
    public function save(User $user): void;
    public function getId(?string $email): int;
    public function getInfoById(string $tipo, int $id);
    public function getInfo(?string $tipo): array;
    public function checkActiveWToken(?string $token): bool;
    public function getPass(?string $email): string;
    public function checkActiveWEmail(?string $mail): bool;
    public function updatePass(int $id, string $newPass): void;
    public function updatePfp(int $id, string $path): void;
    public function activateUser(?string $token): void;
    public function updateModifyDate(int $id) :void;
    public function updateBankAndOwner(int $id, string $iban, string $owner) :void;
}