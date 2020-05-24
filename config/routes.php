<?php

use \ProjWeb2\PRACTICA\Controller\HomeController;
use \ProjWeb2\PRACTICA\Controller\VisitsController;
use \ProjWeb2\PRACTICA\Controller\CookieMonsterController;
use \ProjWeb2\PRACTICA\Controller\FlashController;
use \ProjWeb2\PRACTICA\Middleware\RestrictedMiddleware;
use \ProjWeb2\PRACTICA\Controller\SignUpController;
use \ProjWeb2\PRACTICA\Controller\SignInController;
use \ProjWeb2\PRACTICA\Controller\LogoutController;
use \ProjWeb2\PRACTICA\Controller\ProfileController;
use \ProjWeb2\PRACTICA\Controller\ChangePassController;
use \ProjWeb2\PRACTICA\Controller\DashController;
use \ProjWeb2\PRACTICA\Controller\BankController;
use \ProjWeb2\PRACTICA\Controller\ErrorController;
use \ProjWeb2\PRACTICA\Controller\SendMoneyController;
use \ProjWeb2\PRACTICA\Controller\RequestsController;
use \ProjWeb2\PRACTICA\Controller\TransactionController;


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
)->setName('sign-in-post');

$app->post(
    '/logout',
    LogoutController::class
)->setName('logout-post');

$app->get(
    '/profile',
    ProfileController::class . ":showProfile"
)->setName('profile-show')->add(RestrictedMiddleware::class);

$app->post(
    '/profile',
    ProfileController::class . ":submitData"
)->setName('profile-submit')->add(RestrictedMiddleware::class);

$app->get(
    '/profile/security',
    ChangePassController::class . ":showPass"
)->setName('pass-change-show')->add(RestrictedMiddleware::class);

$app->post(
    '/profile/security',
    ChangePassController::class . ":applyChange"
)->setName('pass-change-apply')->add(RestrictedMiddleware::class);

$app->get(
    '/account/summary',
    DashController::class . ":showDash"
)->setName('dash-show')->add(RestrictedMiddleware::class);

$app->get(
    '/account/bank-account',
    BankController::class . ":showBankForm"
)->setName('bank-form-show')->add(RestrictedMiddleware::class);

$app->post(
    '/account/bank-account',
    BankController::class . ":submitBank"
)->setName('bank-form-submit')->add(RestrictedMiddleware::class);

$app->post(
    '/account/bank-account/load',
    BankController::class . ":addMoney"
)->setName('bank-account-load')->add(RestrictedMiddleware::class);

$app->get(
    '/account/bank-account/load',
    ErrorController::class . ":showError"
)->setName('bank-account-load')->add(RestrictedMiddleware::class);

$app->get(
    '/account/money/send',
    SendMoneyController::class . ":showSendForm"
)->setName('show-send')->add(RestrictedMiddleware::class);;

$app->post(
    '/account/money/send',
    SendMoneyController::class . ":send"
)->setName('send-money')->add(RestrictedMiddleware::class);

$app->get(
    '/account/money/requests',
    RequestsController::class . ":showRequests"
)->setName('show-reqs')->add(RestrictedMiddleware::class);

$app->post(
    '/account/money/requests',
    RequestsController::class . ":makeRequest"
)->setName('make-req')->add(RestrictedMiddleware::class);

$app->get(
    '/account/money/requests/pending',
    RequestsController::class . ":showPending"
)->setName('pending-reqs')->add(RestrictedMiddleware::class);

$app->get(
    '/account/money/requests/pending/{id}/accept',
    RequestsController::class . ":accept"
)->setName('accept-req')->add(RestrictedMiddleware::class);

$app->get(
    '/account/transactions',
    TransactionController::class . ":showTransactions"
)->setName('transactions')->add(RestrictedMiddleware::class);
