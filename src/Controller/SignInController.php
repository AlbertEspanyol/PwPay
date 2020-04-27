<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Utils\ValidationTools;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

final class SignInController
{
    private ValidationTools $vt;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->vt = new ValidationTools();
    }

    public function logIn(Request $request, Response $response): Response
    {
        if(empty($_POST)){
            exit;
        }

        try {
            $data = $request->getParsedBody();

            $errors = $this->vt->isValid($data['email'], $data['password'], null);

            $allmails = $this->container->get('user_repository')->getInfo('email');

            $flag = false;

            foreach ($allmails as $a){
                if($a == $data['email']){
                    $flag = true;
                 }
            }

            if(!$flag){
                $errors[0] = "This user does not exist";
            } else{
                $checkPass = $this->container->get('user_repository')->getPass($data['email']);

                if(md5($data['password']) != $checkPass){
                    $errors[1] = "The password is incorrect";
                }
            }

            if($errors[0] == "xd" && !$this->container->get('user_repository')->checkActiveWEmail($data['email'])){
                $errors[0] = "User isn't active";
            }

            //Si hi ha algún error es mostra la mateixa pantalla conservant les dades introduides
            if ($errors[0] != "xd" || $errors[1] != "xd" || $errors[2] != "xd") {
                return $this->container->get('view')->render(
                    $response,
                    'signForm.twig',
                    [
                        'isReg' => false,
                        'emailErr' => $errors[0],
                        'passErr' => $errors[1],
                        'birthErr' => $errors[2],
                        'email' => $data['email'],
                        'pass' => $data['password'],
                    ]
                );
            }

            //Si tot esta correcte es mostra la dashboard
            return $this->container->get('view')->render(
                $response,
                'dash.twig',
                []
            );

        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }
    }

    public function showSignIn(Request $request, Response $response): Response
    {
        return $this->container->get('view')->render(
            $response,
            'signForm.twig',
            [
                'isReg' => false
            ]
        );
    }
}
