<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class HomeController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showHomePage(Request $request, Response $response): Response
    {
        $messages = $this->container->get('flash')->getMessages();

        $notifications = $messages['notifications'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'home.twig',
            [
                'notifications' => $notifications
            ]
        );
    }
}