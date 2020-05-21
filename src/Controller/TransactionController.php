<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use ProjWeb2\PRACTICA\Model\Transaction;

final class TransactionController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function showTransactions(Request $request, Response $response): Response{
        //S'agafa la id del user
        $id = $_SESSION['user_id'];

        $transactions = $this->container->get('transaction_repository')->getAllTrans($id);

        return $this->container->get('view')->render(
            $response,
            'transactions.twig',
            [
                'transactions' => $transactions[0]
                //'isLoad'=> !($checkEx == 'Unknown'),
                //'ibanErr'=> $this->ibanErr,
                //'iban'=>$this->iban,
                //'owner_name'=>$this->owner
            ]
        );
    }
}
