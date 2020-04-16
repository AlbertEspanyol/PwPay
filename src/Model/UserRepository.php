<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Model;

interface UserRepository
{
    public function save(User $user): void;
}