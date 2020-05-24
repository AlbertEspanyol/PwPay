<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ChangePassController
{
    private ContainerInterface $container;
    //Variable que 'sinicialitza aquí per a poder fer-la servir a totes les funcions
    private array $errors;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->errors = ['xd', 'xd', 'xd'];
    }

    public function applyChange(Request $request, Response $response): Response
    {
        $id = $_SESSION['user_id'];

        if(isset($_POST)){
            $old = $_POST['old_password'];
            $new = $_POST['new_password'];
            $conf = $_POST['confirm_password'];

            $actual = $this->container->get('user_repository')->getInfoById('password', $id);

            if(md5($old) != $actual){
                $this->errors[0] = 'The old password is wrong';
            }

            if($new != $conf){
                $this->errors[1] = 'Confirmation password does not match new password';
            }

            if($this->errors[0] == 'xd'&& $this->errors[1] == 'xd'){
                $this->errors[2] = 'ok';
                $this->container->get('user_repository')->updatePass($id, md5($new));
                $this->container->get('user_repository')->updateModifyDate($id);

                $this->container->get('flash')->addMessage('pass', 'Changed password successfully!');
                return $response->withHeader('Location', '/profile')->withStatus(302);
            }
        }

        return $this->showPass($request, $response);
    }

    public function showPass(Request $request, Response $response): Response
    {
        return $this->container->get('view')->render(
            $response,
            'changePass.twig',
            [
                'errorOld' => $this->errors[0],
                'errorConf' => $this->errors[1],
                'ok' => $this->errors[2]
            ]
        );
    }
}
