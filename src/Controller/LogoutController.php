<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class LogoutController
{
    public function __invoke(Request $request, Response $response): Response
    {
        unset($_SESSION['user_id']);
        return $response;
    }
}
