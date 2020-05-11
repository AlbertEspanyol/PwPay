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

        $path = $this->container->get('user_repository')->getInfoById('pfp_path', $id);
        if($path == 'Unknown') $path = "https://placehold.it/400x400"; else $path = '../' . $path;
        $iban = $this->container->get('user_repository')->getInfoById('iban', $id);
        $money = $this->container->get('user_repository')->getInfoById('money', $id);
        $tss = $this->container->get('transaction_repository')->getLatest5Trans($id);
        $email = $this->container->get('user_repository')->getInfoById('email', $id);

        $names = [];
        $srcs = [];
        $dsts = [];

        if(!empty($tss)){
            for($i = 0; $i < sizeof($tss); $i++){
                $xd = $tss[$i];
                $srcs[$i] = $xd->getSourceUser();
                $dsts[$i] = $xd->getDestUser();
                $src = $xd->getSourceUser();

                if($src == $id) $src = $xd->getDestUser();


                $names[$i] = $this->container->get('user_repository')->getInfoById('email', $src);
            }
        }


        if($iban == 'Unknown'){
            $iban = "You haven't linked your bank account yet";
        }

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
