<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Repository;

use PDO;
use ProjWeb2\PRACTICA\Model\Transaction;
use ProjWeb2\PRACTICA\Model\TransactionRepository;

use DateTime;

final class MYSQLTransactionRepository implements TransactionRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function addTransaction(Transaction $trans): void
    {
        $query = <<<'QUERY'
        INSERT INTO transactions(source_user, dest_user, money, tipo, motiu, data_actual, status)
        VALUES(:source_user, :dest_user, :money, :tipo, :motiu, :data, :status);
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $source_user = $trans->getSourceUser();
        $dest_user = $trans->getDestUser();
        $money = $trans->getMoney();
        $tipo = $trans->getTipo();
        $motiu = $trans->getMotiu();
        $data = $trans->getData()->format(self::DATE_FORMAT);
        $status = $trans->getStatus();


        $statement->bindParam('source_user', $source_user, PDO::PARAM_STR);
        $statement->bindParam('dest_user', $dest_user, PDO::PARAM_STR);
        $statement->bindParam('money', $money, PDO::PARAM_STR);
        $statement->bindParam('tipo', $tipo, PDO::PARAM_STR);
        $statement->bindParam('motiu', $motiu, PDO::PARAM_STR);
        $statement->bindParam('data', $data, PDO::PARAM_STR);
        $statement->bindParam('status', $status, PDO::PARAM_STR);


        $statement->execute();
    }

    public function getTrans(int $id, bool $limit): array
    {
        if ($limit) {
            $query = <<<'QUERY'
         SELECT * FROM transactions WHERE (source_user = :id OR dest_user = :id) AND status LIKE 'accepted' ORDER BY data_actual DESC LIMIT 5;
QUERY;
        } else {
            $query = <<<'QUERY'
         SELECT * FROM transactions WHERE (source_user = :id OR dest_user = :id) AND status LIKE 'accepted' ORDER BY data_actual DESC;
QUERY;
        }
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('limit', $limit, PDO::PARAM_STR);

        $statement->execute();

        $bbdd_array = $statement->fetchAll();
        $tss = [];

        if(!empty($bbdd_array)){
            for($i = 0; $i < sizeof($bbdd_array); $i++){
                $field = $bbdd_array[$i];
                $tss[$i] = new Transaction(
                    intval($field['id']),
                    intval($field['source_user']),
                    intval($field['dest_user']),
                    floatval($field['money']),
                    $field['tipo'],
                    $field['motiu'],
                    DateTime::createFromFormat(self::DATE_FORMAT,$field['data_actual'])
                );
            }
        }

        return $tss;

    }

    public function getRequests(int $id): array
    {
        $query = <<<'QUERY'
         SELECT * FROM transactions WHERE dest_user = :id AND dest_user NOT LIKE source_user AND status LIKE 'pending';
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $bbdd_array = $statement->fetchAll();
        $tss = [];

        if(!empty($bbdd_array)){
            for($i = 0; $i < sizeof($bbdd_array); $i++){
                $field = $bbdd_array[$i];
                $tss[$i] = new Transaction(
                    intval($field['id']),
                    intval($field['source_user']),
                    intval($field['dest_user']),
                    floatval($field['money']),
                    $field['tipo'],
                    $field['motiu'],
                    DateTime::createFromFormat(self::DATE_FORMAT,$field['data_actual'])
                );
            }
        }

        return $tss;
    }

    public function getTransById(int $id): Transaction
    {
        $query = <<<'QUERY'
         SELECT * FROM transactions WHERE id = :id;
QUERY;

        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $arr = $statement->fetch();

        $trans = new Transaction(intval($arr['id']), intval($arr['source_user']), intval($arr['dest_user']), floatval($arr['money']), $arr['tipo'], $arr['motiu'], DateTime::createFromFormat(self::DATE_FORMAT,$arr['data_actual']));

        $trans->setStatus($arr['status']);

        return $trans;
    }

    public function updateStatus(int $id, string $status): void
    {
        $query = <<<'QUERY'
         UPDATE transactions SET status = :status WHERE id = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('status', $status, PDO::PARAM_STR);
        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
    }

}

