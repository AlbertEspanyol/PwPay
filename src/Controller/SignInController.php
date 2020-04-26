<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Utils\ValidationTools;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTime;
use Exception;
use ProjWeb2\PRACTICA\Model\User;

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
            }

            if($errors[0] == "xd" && !$this->container->get('user_repository')->checkActiveWEmail($data['email'])){
                $errors[0] = "User isn't active";
            }

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
