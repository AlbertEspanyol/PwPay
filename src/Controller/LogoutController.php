<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LogoutController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function __invoke(Request $request, Response $response): Response
    {
        unset($_SESSION['user_id']);
        return $this->container->get('view')->render(
        $response,
        'signForm.twig',
        [
            'isReg' => false
        ]
    );
    }
}
