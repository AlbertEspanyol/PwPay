<?php

declare(strict_types=1);

namespace ProjWeb2\PRACTICA\Controller;

use DateTime;
use Exception;
use Psr\Container\ContainerInterface;
use ProjWeb2\PRACTICA\Model\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class PostUserController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(Request $request, Response $response): Response
    {
        if(empty($_POST)){
            exit;
        }

        try {
            $data = $request->getParsedBody();

            $errors = $this->isValid($data['email'], $data['password'], $data['birthday']);

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo nl2br("<p class='error'>" . $error . "</p>" . "\n");
                }
                exit(1);
            }

            // TODO - Validate data before instantiating the user
            $user = new User(
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['birthday'],
                new DateTime(),
                new DateTime()
            );

            $this->container->get('user_repository')->save($user);

        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        return $response->withStatus(201);
    }

    private function isValid(?string $email, ?string $password, ?string $birthday): array
    {
        $errors = [];

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = sprintf('The email is not valid');
        }

        if (strlen($password) < 6 || !preg_match("/[a-z]/i",$password) || !preg_match("/[0-9]/",$password)) {
            $errors[] = sprintf('The password is not valid');
        }

        $date = date_parse($birthday);

        if($date['year'] > date("Y") || $date['month'] > 12 || !$this->checkDays($date['day'], $date['month'], $date['year'])){
            $errors[] = sprintf('The date is not valid');
        }
        return $errors;
    }

    private function checkDays(?int $day, ?int $month, ?int $year): bool {
        if($month < 8 && $month % 2 == 0 && $day > 30){
            return false;
        } else if ($month >= 8 && $month % 2 == 0 && $day > 31){
            return false;
        }

        if($month = 2){
            if((($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0))) && $month = 2) {
                if ($day > 29) return false;
            } else{
                if ($day > 28) return false;
            }
        }

        return true;
    }
}