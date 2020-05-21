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

        $tss = $this->container->get('transaction_repository')->getTrans($id,false);
        $email = $this->container->get('user_repository')->getInfoById('email', $id);

        //Array que guarda els mails que ens interessa mostrar a partir de les ids
        $names = [];

        //Array que guarda les source_id i les dest_id de la taula transactions
        $srcs = [];
        $dsts = [];

        if(!empty($tss)){
            for($i = 0; $i < sizeof($tss); $i++){
                $xd = $tss[$i];

                //Es fiquen les ids que toquen
                $srcs[$i] = $xd->getSourceUser();
                $dsts[$i] = $xd->getDestUser();


                $src = $xd->getSourceUser();
                $dst = $xd->getDestUser();

                //Si l'usuari ha estat el que ha efectuat la transaccio s'ha d'agafar el mail del destinatari per a mostrar-lo
                if($src == $id) $src = $xd->getDestUser();
                if($src == $id) {
                    if($dst == $id){
                        $src = "Me";
                    } else {
                        $src = $xd->getDestUser();
                    }
                }
                if($src != "Me") {
                    $names[$i] = $this->container->get('user_repository')->getInfoById('email', $src);
                } else {
                    $names[$i] = $src;
                }
            }
        }

        return $this->container->get('view')->render(
            $response,
            'tss.twig',
            [
                'mail' => $email,
                'id'=>$id,
                'tss'=>$tss,
                'names'=>$names,
                'src'=>$srcs,
                'dst'=>$dsts
            ]
        );
    }
}
