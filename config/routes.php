<?php

use \ProjWeb2\PRACTICA\Controller\HomeController;
use \ProjWeb2\PRACTICA\Controller\VisitsController;
use \ProjWeb2\PRACTICA\Controller\CookieMonsterController;
use \ProjWeb2\PRACTICA\Controller\FlashController;
use \ProjWeb2\PRACTICA\Middleware\StartSessionMiddleware;
use \ProjWeb2\PRACTICA\Controller\SignUpController;
use \ProjWeb2\PRACTICA\Controller\SignInController;

$app->get(
    '/',
    HomeController::class . ":showHomePage"
)->setName('home');

$app->get(
    '/sign-up',
    SignUpController::class . ":showSignUp"
)->setName('sign-up-view');

$app->post(
    '/sign-up',
    SignUpController::class . ":create"
)->setName('sign-up-post');

$app->get(
    '/activate',
    SignUpController::class . ":activate"
)->setName('activate');

$app->get(
    '/sign-in',
    SignInController::class . ":showSignIn"
)->setName('sign-in-view');

$app->post(
    '/sign-in',
    SignInController::class . ":logIn"
)->setName('sign-in-post')->add(StartSessionMiddleware::class);

$app->post(
    '/logout',
    SignInController::class . ":logIn"
)->setName('logout-post');

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

