<?php

use \ProjWeb2\PRACTICA\Controller\HomeController;
use \ProjWeb2\PRACTICA\Controller\VisitsController;
use \ProjWeb2\PRACTICA\Controller\CookieMonsterController;
use \ProjWeb2\PRACTICA\Controller\FlashController;
use \ProjWeb2\PRACTICA\Middleware\StartSessionMiddleware;
use \ProjWeb2\PRACTICA\Controller\PostUserController;
use \ProjWeb2\PRACTICA\Controller\SignUpController;


$app->add(StartSessionMiddleware::class);

$app->get(
    '/',
    HomeController::class . ":showHomePage"
)->setName('home');

$app->get(
    '/sign-up',
    SignUpController::class . ":showSignUp"
)->setName('sign-up');

$app->post(
    '/sign-up',
    PostUserController::class . ":create"
)->setName('sign-up');

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

