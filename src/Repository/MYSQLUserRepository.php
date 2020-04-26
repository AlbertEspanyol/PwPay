<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Repository;

use PDO;
use ProjWeb2\PRACTICA\Model\User;
use ProjWeb2\PRACTICA\Model\UserRepository;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO user(email, password, birthday, created_at, updated_at, money, active, token)
        VALUES(:email, :password, :birthday, :created_at, :updated_at, :money, :active, :token)
QUERY;

        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $birthday = $user->birthday();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $money = $user->money();
        $token = $user->getToken();
        $active = $user->getActive();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('money', $money, PDO::PARAM_STR);
        $statement->bindParam('active', $active, PDO::PARAM_STR);
        $statement->bindParam('token', $token, PDO::PARAM_STR);


        $statement->execute();
    }

    public function getInfo(?string $tipo): array{

        $pdo = $this->database->connection();

        $statement = $pdo->prepare("SELECT " . $tipo . " FROM user;");

        $statement->execute();

        $st = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

        return $st;
    }

    public function checkActiveWToken(?string $token): bool
    {
        $query = <<<'QUERY'
         SELECT active FROM user WHERE token = :token;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);

        $statement->execute();

        if($statement->fetch()['0'] == 0){
            return false;
        }

        return true;
    }

    public function checkActiveWEmail(?string $mail): bool
    {
        $query = <<<'QUERY'
         SELECT active FROM user WHERE email = :email;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('email', $mail, PDO::PARAM_STR);

        $statement->execute();

        if($statement->fetch()['0'] == 0){
            return false;
        }

        return true;
    }


    public function activateUser(?string $token): void{

        $query = <<<'QUERY'
         UPDATE user SET active = 1, money = money + 20 WHERE token = :token;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);

        $statement->execute();
    }
}