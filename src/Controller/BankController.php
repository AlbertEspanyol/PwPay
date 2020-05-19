<?php

namespace ProjWeb2\PRACTICA\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Iban\Validation\Validator;
use Iban\Validation\Iban;

final class BankController {
    private float $money;
    private string $moneyErr;
    private string $ibanErr;
    private ContainerInterface $container;
    private string $iban;
    private string $owner;

    public function __construct(ContainerInterface $container)
    {
        $this->money = 0;
        $this->container = $container;
        $this->ibanErr = 'xd';
        $this->moneyErr = 'xd';
        $this->owner = '';
        $this->iban = '';
    }

    public function submitBank(Request $request, Response $response): Response
    {
        if(!empty($_POST)){
            $id = $_SESSION['user_id'];
            $this->owner = $_POST['owner_name'];
            $iban = new Iban($_POST['iban']);

            $this->iban = $iban->format(Iban::FORMAT_ELECTRONIC);

            $validator = new Validator();

            $checkExists = $this->container->get('user_repository')->getInfoById('iban', $id);

            if (!$validator->validate($iban)) {
                $this->ibanErr = 'The iban is not correct';
            } else{
                if($checkExists != 'Unknown'){
                    $this->ibanErr = 'The iban is already registered';
                } else {
                    $this->container->get('user_repository')->updateBankAndOwner($id,$this->iban, $this->owner);
                    $this->container->get('user_repository')->updateModifyDate($id);
                }
            }
        }

        return $this->showBankForm($request, $response);
    }

    public function addMoney(Request $request, Response $response): Response
    {
        if(!empty($_POST)){
            $id = $_SESSION['user_id'];
            $this->money = $_POST['money'];

            if(!filter_var($this->money, FILTER_VALIDATE_FLOAT)){
                $this->moneyErr = 'Input is wrong';
            } else {
                $this->container->get('user_repository')->updateMoney($id,floatval($this->money));
                $this->container->get('user_repository')->updateModifyDate($id);
            }
        }
        return $this->showBankForm($request, $response);
    }

    public function showBankForm(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];

        $checkEx = $this->container->get('user_repository')->getInfoById('iban', $id);

        return $this->container->get('view')->render(
            $response,
            'bankForm.twig',
            [
                'isLoad'=> !($checkEx == 'Unknown'),
                'ibanErr'=> $this->ibanErr,
                'iban'=>$checkEx,
                'owner_name'=>$this->owner
            ]
        );
    }

}
