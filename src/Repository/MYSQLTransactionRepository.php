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
        INSERT INTO transactions(source_user, dest_user, money, tipo, motiu, data_actual)
        VALUES(:source_user, :dest_user, :money, :tipo, :motiu, :data);
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $source_user = $trans->getSourceUser();
        $dest_user = $trans->getDestUser();
        $money = $trans->getMoney();
        $tipo = $trans->getTipo();
        $motiu = $trans->getMotiu();
        $data = $trans->getData()->format(self::DATE_FORMAT);;

        $statement->bindParam('source_user', $source_user, PDO::PARAM_STR);
        $statement->bindParam('dest_user', $dest_user, PDO::PARAM_STR);
        $statement->bindParam('money', $money, PDO::PARAM_STR);
        $statement->bindParam('tipo', $tipo, PDO::PARAM_STR);
        $statement->bindParam('motiu', $motiu, PDO::PARAM_STR);
        $statement->bindParam('data', $data, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getLatest5Trans(int $id): array
    {
        $query = <<<'QUERY'
         SELECT * FROM transactions WHERE source_user = :id OR dest_user = :id ORDER BY data_actual DESC LIMIT 5;
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

    public function updateStatus(int $id): void
    {
        // TODO: Implement updateStatus() method.
    }

    public function getAllTrans(int $id) : array{
        $query = <<<'QUERY'
         SELECT * FROM transactions WHERE source_user = :id OR dest_user = :id;
QUERY;
        $pdo = $this->database->connection();

        $statement = $pdo->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $transactions = array();
        while ($row = $statement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            array_push($transactions,$row);
        }

        echo json_encode($transactions);

        return $transactions;
    }
}

