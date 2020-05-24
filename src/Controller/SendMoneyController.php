<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Model\Transaction;
use ProjWeb2\PRACTICA\Utils\ValidationTools;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTime;

final class SendMoneyController
{
    private ContainerInterface $container;
    private string $moneyErr;
    private array $mailErr;
    private ValidationTools $vt;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->moneyErr = "xd";
        $this->mailErr = ['xd'];
        $this->vt = new ValidationTools();
    }

    public function send(Request $request, Response $response): Response
    {
        if(!empty($_POST)){
            $id = $_SESSION['user_id'];
            $money = $_POST['money2send'];
            $destMail = $_POST['destinatary'];

            $this->moneyErr = $this->vt->checkMoney($money);
            $money = floatval($money);

            if($this->moneyErr == 'xd' && $this->container->get('user_repository')->getInfoById('money', $id) < $money){
                $this->moneyErr = 'En breves toque anar a viure sota un pont';
            }

            $allmails = $this->container->get('user_repository')->getInfo('email');

            $flag = false;

            foreach ($allmails as $a){
                if($a == $destMail){
                    $flag = true;
                }
            }

            if($flag){
                if($this->container->get('user_repository')->getInfoById('email', $id) == $destMail){
                    $this->mailErr[0] = 'You cannot send money to yourself';
                } else {
                    if ($this->container->get('user_repository')->checkActiveWEmail($destMail)) {
                        $this->mailErr = $this->vt->isValid($destMail, null, null);
                        if ($this->mailErr[0] == 'xd' && $this->moneyErr == 'xd') {
                            $dstId = $this->container->get('user_repository')->getId($destMail);

                            $trans = new Transaction(
                                null,
                                $id,
                                $dstId,
                                $money,
                                'send',
                                'klk',
                                new DateTime()
                            );

                            $trans->setStatus('accepted');

                            $this->container->get('user_repository')->updateMoney($id, -$money);
                            $this->container->get('user_repository')->updateMoney($dstId, $money);

                            $this->container->get('transaction_repository')->addTransaction($trans);

                            $this->container->get('flash')->addMessage('notifications', 'Sent ' . $money . '€ to ' . $destMail . ' successfully!');

                            return $response->withHeader('Location', '/account/summary')->withStatus(302);
                        }
                    } else {
                        $this->mailErr[0] = 'This user is not active';
                    }
                }
            } else{
                $this->mailErr[0] = 'This user does not exist';
            }
        }
        return $this->showSendForm($request, $response);
    }

    public function showSendForm(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];

        $path = $this->container->get('user_repository')->getInfoById('pfp_path', $id);
        if($path == 'Unknown') {
            $path = "https://placehold.it/400x400";
        } else {
            $path = '../../' . $path;
        }

        return $this->container->get('view')->render(
            $response,
            'sendMoney.twig',
            [
                'pfp'=>$path,
                'moneyErr' => $this->moneyErr,
                'mailErr' => $this->mailErr[0]
            ]
        );
    }
}