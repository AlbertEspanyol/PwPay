<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Repository;

use PDO;
use ProjWeb2\PRACTICA\Model\Transaction;
use ProjWeb2\PRACTICA\Model\User;
use ProjWeb2\PRACTICA\Model\UserRepository;

use DateTime;

/***********************
 * El DAO
 *******************/
final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    //Es guarda l'usuari
    public function save(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO user(email, password, birthday, created_at, updated_at, money, active, token)
        VALUES(:email, :password, :birthday, :created_at, :updated_at, :money, :active, :token)
QUERY;

        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $email = $user->getEmail();
        $password = $user->getPassword();
        $birthday = $user->getBirthday();
        $createdAt = $user->getCreatedAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->getUpdatedAt()->format(self::DATE_FORMAT);
        $money = $user->getMoney();
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

    public function getId(?string $email): int{

        $query = <<<'QUERY'
         SELECT id FROM user WHERE email = :email;
QUERY;

        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        return (int)$statement->fetch()['0'];
    }

    public function getInfoById(string $tipo, int $id) {

        $pdo = $this->database->connection();

        $statement = $pdo->prepare("SELECT " . $tipo . " FROM user WHERE id = " . $id . ";");

        $statement->execute();

        return $statement->fetch()['0']??'Unknown';
    }

    //Agafa info generica d'usuari
    public function getInfo(?string $tipo): array{

        $pdo = $this->database->connection();

        $statement = $pdo->prepare("SELECT " . $tipo . " FROM user;");

        $statement->execute();

        //fetchAll = agafa totes les files i retorna un array d'arrays d'aquestes
        $st = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

        return $st;
    }

    //Comprova si el token esta actiu
    public function checkActiveWToken(?string $token): bool
    {
        $query = <<<'QUERY'
         SELECT active FROM user WHERE token = :token;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);

        $statement->execute();

        //S'extreu la fila que correspon a si es actiu(1) o no(0) i es comprova
        if($statement->fetch()['0'] == 0){
            return false;
        }

        return true;
    }

    public function getPass(?string $email): string
    {
        $query = <<<'QUERY'
         SELECT password FROM user WHERE email = :email;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC)['password'];
    }

    //Comprova si l'usuari esta actiu
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

    public function updatePass(int $id, string $newPass): void
    {
        $query = <<<'QUERY'
         UPDATE user SET password = :newPass  WHERE id = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('newPass', $newPass, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updatePfp(int $id, string $path): void{
        $query = <<<'QUERY'
         UPDATE user SET pfp_path = :path  WHERE id = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('path', $path, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
    }

    //Activa l'usuari amb un update
    public function activateUser(?string $token): void{

        $query = <<<'QUERY'
         UPDATE user SET active = 1, money = money + 20 WHERE token = :token;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateModifyDate(int $id) :void{
        $query = <<<'QUERY'
         UPDATE user SET updated_at = :date_now WHERE id = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $date = new DateTime();

        $statement->bindValue('date_now', $date->format(self::DATE_FORMAT), PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateBankAndOwner(int $id, string $iban, string $owner) :void{
        $query = <<<'QUERY'
         UPDATE user SET bank_owner = :owner, iban = :iban WHERE id = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('owner', $owner, PDO::PARAM_STR);
        $statement->bindParam('iban', $iban, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateMoney(int $id, float $cash) :void{
        $query = <<<'QUERY'
         UPDATE user SET money = money + :cash WHERE id = :id;
QUERY;
        $query2 = <<<'QUERY'
        INSERT INTO transactions(source_user, dest_user, money, tipo, motiu, data_actual,)
        VALUES(:email, :password, :birthday, :created_at, :updated_at, :money, :active, :token)
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);
        $statement2 = $pdo->prepare($query2);

        $date = new DateTime();
        $tipo = "ingres propi";

        $statement->bindParam('cash', $cash, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement2->bindValue('date_now', $date->format(self::DATE_FORMAT), PDO::PARAM_STR);
        $statement2->bindParam('cash', $cash, PDO::PARAM_STR);
        $statement2->bindParam('id', $id, PDO::PARAM_STR);
        $statement2->bindParam('tipo', $tipo, PDO::PARAM_STR);

        $statement->execute();
        $statement2->execute();
    }

    public function getTransactions(int $id) :void{
        $query = <<<'QUERY'
         SELECT user WHERE source_user = :id OR dest_user = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $transactions = new Transaction[];
        $i = 0;
        while($row = mysql_fetch_assoc($query)){
            $transactions[$i]->tipo = $row;
            $i++;
        }

        echo json_encode($transactions);
    }
}