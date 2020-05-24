<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Model\Transaction;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Iban\Validation\Validator;
use Iban\Validation\Iban;
use ProjWeb2\PRACTICA\Utils\ValidationTools;
use DateTime;

final class BankController {
    private float $money;
    private string $moneyErr;
    private string $ibanErr;
    private ValidationTools $vt;
    private ContainerInterface $container;
    private string $iban;
    private string $owner;

    public function __construct(ContainerInterface $container)
    {
        $this->money = 0;
        $this->container = $container;
        $this->vt = new ValidationTools();
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
            $cash = $_POST['money'];

            $this->moneyErr = $this->vt->checkMoney($cash);

            if($this->moneyErr == 'xd'){
                $this->money = floatval($cash);

                $trans = new Transaction(
                    null,
                    $id,
                    $id,
                    $this->money,
                    'in',
                    'in',
                    new DateTime()
                    );

                $trans->setStatus('accepted');

                $this->container->get('transaction_repository')->addTransaction($trans);
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
                'owner_name'=>$this->owner,
                'moneyErr' => $this->moneyErr
            ]
        );
    }
}
