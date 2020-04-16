<?php

use \ProjWeb2\PRACTICA\Controller\HomeController;
use \ProjWeb2\PRACTICA\Controller\VisitsController;
use \ProjWeb2\PRACTICA\Controller\CookieMonsterController;
use \ProjWeb2\PRACTICA\Controller\FlashController;
use \ProjWeb2\PRACTICA\Middleware\StartSessionMiddleware;
use \ProjWeb2\PRACTICA\Controller\PostUserController;


$app->add(StartSessionMiddleware::class);

$app->get(
    '/',
    HomeController::class . ":showHomePage"
)->setName('home');

$app->get(
    '/visits',
    VisitsController::class . ":showVisits"
)->setName('visits');

$app->get(
    '/cookies',
    CookieMonsterController::class . ":showAdvice"
)->setName('cookies');

$app->get(
    '/flash',
    FlashController::class . ":addMessage"
)->setName('flash');

$app->post(
    '/users',
    PostUserController::class . ":create"
)->setName('create_user');