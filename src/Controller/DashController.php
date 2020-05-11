<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class DashController {

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showDash(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];

        //S'agafe el path i si existeix se li afegeix ../ al principi perque sino no funcione
        $path = $this->container->get('user_repository')->getInfoById('pfp_path', $id);
        if($path == 'Unknown') {
            $path = "https://placehold.it/400x400";
        } else {
            $path = '../' . $path;
        }

        //S'agafen les dades de la bbdd
        $iban = $this->container->get('user_repository')->getInfoById('iban', $id);
        $money = $this->container->get('user_repository')->getInfoById('money', $id);
        $tss = $this->container->get('transaction_repository')->getLatest5Trans($id);
        $email = $this->container->get('user_repository')->getInfoById('email', $id);

        //Array que guarda els mails que ens interessa mostrar a partir de les ids
        $names = [];
        //Array que guarda les source_id i les dest_id de la taula transactions perque si se li passe directament l'array tss al twig no funcione ves a saber perque
        $srcs = [];
        $dsts = [];

        if(!empty($tss)){
            for($i = 0; $i < sizeof($tss); $i++){
                $xd = $tss[$i];

                //Es fiquen les ids que toquen
                $srcs[$i] = $xd->getSourceUser();
                $dsts[$i] = $xd->getDestUser();


                $src = $xd->getSourceUser();

                //Si l'usuari ha estat el que ha efectuat la transaccio s'ha d'agafar el mail del destinatari per a mostrar-lo
                if($src == $id) $src = $xd->getDestUser();


                $names[$i] = $this->container->get('user_repository')->getInfoById('email', $src);
            }
        }


        if($iban == 'Unknown'){
            $iban = "You have not linked your bank account yet";
        }

        /*******
        * Per mostrar les transtaccions es segueix la següent logica
        * Si es una transaccio request el destinatari es qui perd els diners
        * Si es una transaccio send el que l'envia es qui perd els diners
        **********/
        return $this->container->get('view')->render(
            $response,
            'dash.twig',
            [
                'mail' => $email,
                'id'=>$id,
                'iban'=>$iban,
                'pfp'=>$path,
                'money'=>$money,
                'tss'=>$tss,
                'names'=>$names,
                'src'=>$srcs,
                'dst'=>$dsts
            ]
        );
    }
}
