<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Utils\ValidationTools;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTime;
use Exception;
use ProjWeb2\PRACTICA\Model\User;

final class SignUpController
{
    private ValidationTools $vt;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->vt = new ValidationTools();
    }

    public function create(Request $request, Response $response): Response
    {
        if(empty($_POST)){
            exit;
        }

        try {
            $data = $request->getParsedBody();

            $errors = $this->vt->isValid($data['email'], $data['password'], $data['birthday']);

            $allmails = $this->container->get('user_repository')->getInfo("email");

            foreach ($allmails as $a){
                if($a == $data['email']){
                    $errors[0] = "This user already exists";
                }
            }

            if ($errors[0] != "xd" || $errors[1] != "xd" || $errors[2] != "xd") {
                return $this->container->get('view')->render(
                    $response,
                    'signForm.twig',
                    [
                        'isReg' => true,
                        'emailErr' => $errors[0],
                        'passErr' => $errors[1],
                        'birthErr' => $errors[2],
                        'email' => $data['email'],
                        'pass' => $data['password'],
                        'birth' => $data['birthday']
                    ]
                );
            }

            $alltok = $this->container->get('user_repository')->getInfo('token');
            $flag = false;

            $dat = new DateTime();
            $td = date_parse($dat->format('Y-m-d H:i:s'));
            $token = $td['second'] . $td['minute'] . $td['hour'] . $td['day'] . $td['month'] . $td['year'];

            while (!$flag && !empty($alltok)){
                $dat = new DateTime();
                $td = date_parse($dat->format('Y-m-d H:i:s'));
                $token = $td['second'] . $td['minute'] . $td['hour'] . $td['day'] . $td['month'] . $td['year'];

                foreach ($alltok as $a){
                    if($token != $a){
                        $flag = true;
                    }
                }
            }

            $xd = 0;

            $user = new User(
                $data['email'] ?? '',
                md5($data['password']) ?? '',
                $data['birthday'] ?? '',
                new DateTime(),
                new DateTime(),
                0,
                $xd,
                $token
            );

            $this->container->get('user_repository')->save($user);

            //print phpinfo();
            mail($data['email'], 'Activate your account', "klk men activate con esto flow: slimapp.test/activate?token=" . $token);

        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        return $this->container->get('view')->render(
            $response,
            'activation.twig',
            [
                'done' => false,
                'icon' => 'fas fa-envelope',
                'titleMsg' => '',
                'subttlMsg' => 'Activa el compte accedint a aquesta adreça: slimapp.test/activate?token=' . $token . "\n (No hem estat capaços de fer funcionar la funció mail())"
            ]
        );
    }

    public function activate(Request $request, Response $response): Response
    {
        $token = $request->getQueryParams();
        $token = $token['token'];

        if(!$this->container->get('user_repository')->checkActiveWToken($token)){
            $this->container->get('user_repository')->activateUser($token);

            return $this->container->get('view')->render(
                $response,
                'activation.twig',
                [
                    'done' => true,
                    'icon' => 'fas fa-check',
                    'titleMsg' => '20€ de regal!',
                    'subttlMsg' => 'Gràcies per activar el compte'
                ]
            );
        }

        return $this->container->get('view')->render(
            $response,
            'activation.twig',
            [
                'done' => true,
                'icon' => 'fas fa-times',
                'titleMsg' => 'F',
                'subttlMsg' => 'Ja estas activat trapella'
            ]
        );
    }
/*
    private function isValid(?string $email, ?string $password, ?string $birthday): array
    {
        $errors = ["xd", "xd", "xd"];

        $popmail = explode('@', $email);

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL) || $popmail[1] !== "salle.url.edu") {
            $errors[0] = sprintf('The email is not valid');
        }

        if (strlen($password) < 6 || !preg_match("/[a-zA-Z]/",$password) || !preg_match("/[0-9]/",$password)) {
            $errors[1] = sprintf('The password is not valid');
        }

        $date = date_parse($birthday);

        if($date['year'] > date("Y") || $date['month'] > 12 || !$this->checkDays($date['day'], $date['month'], $date['year']) || strtotime($birthday) < 18 * 31536000){
            $errors[2] = sprintf('The date is not valid');
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

        if (date('Y') - $year < 18) {
            return false;
        } else if (date('m') - $month < 0) {
            return false;
        } else if (date('d') - $day < 0) {
            return false;
        }

        return true;
    }
*/
    public function showSignUp(Request $request, Response $response): Response
    {
        return $this->container->get('view')->render(
            $response,
            'signForm.twig',
            [
                'isReg' => true
            ]
        );
    }
}
