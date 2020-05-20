<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Iban\Validation\Validator;
use Iban\Validation\Iban;

final class TransactionController {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function showTransactions(Request $request, Response $response): Response{
        //S'agafa la id del user
        $id = $_SESSION['user_id'];

    }
}
