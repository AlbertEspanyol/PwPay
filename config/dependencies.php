<?php

use DI\Container;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use ProjWeb2\PRACTICA\Repository\MySQLUserRepository;
use ProjWeb2\PRACTICA\Repository\MYSQLTransactionRepository;
use ProjWeb2\PRACTICA\Repository\PDOSingleton;
use Psr\Container\ContainerInterface;

$container = new Container();

$container->set(
    'view',
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set('user_repository', function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set('transaction_repository', function (ContainerInterface $container) {
    return new MYSQLTransactionRepository($container->get('db'));
});