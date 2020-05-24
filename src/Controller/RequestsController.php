<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Model\Transaction;
use ProjWeb2\PRACTICA\Utils\ValidationTools;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTime;

final class RequestsController{

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

    public function makeRequest($request, Response $response): Response
    {
        if(!empty($_POST))
        {
            $id = $_SESSION['user_id'];
            $money = $_POST['money2send'];
            $destMail = $_POST['destinatary'];

            $this->moneyErr = $this->vt->checkMoney($money);
            $money = floatval($money);

            $allmails = $this->container->get('user_repository')->getInfo('email');

            $flag = false;

            foreach ($allmails as $a){
                if($a == $destMail){
                    $flag = true;
                }
            }

            if($flag){
                if($this->container->get('user_repository')->getInfoById('email', $id) == $destMail){
                    $this->mailErr[0] = 'You cannot request money to yourself';
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
                                'request',
                                'klk',
                                new DateTime()
                            );

                            $this->container->get('transaction_repository')->addTransaction($trans);

                            $this->container->get('flash')->addMessage('notifications', 'Requested ' . $money . '€ to ' . $destMail . ' successfully!');

                            return $response->withHeader('Location', '/account/summary')->withStatus(302);
                        }
                    } else {
                        $this->mailErr[0] = 'This user is not active';
                    }
                }
            } else {
                $this->mailErr[0] = 'This user does not exist';
            }
        }

        return $this->showRequests($request, $response);
    }

    public function showRequests(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];

        $mail = $this->container->get('user_repository')->getInfoById('email', $id);

        $path = $this->container->get('user_repository')->getInfoById('pfp_path', $id);
        if($path == 'Unknown') {
            $path = "https://placehold.it/400x400";
        } else {
            $path = '../../' . $path;
        }

        return $this->container->get('view')->render(
            $response,
            'requestMoney.twig',
            [
                'mail' => $mail,
                'pfp'=>$path,
                'moneyErr' => $this->moneyErr,
                'mailErr' => $this->mailErr[0]
            ]
        );
    }

    public function showPending(Request $request, Response $response): Response
    {
        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];
        $reqs = $this->container->get('transaction_repository')->getRequests($id);

        $names = [];
        if(!empty($reqs)){
            for($i = 0; $i < sizeof($reqs); $i++){
                $xd = $reqs[$i];

                $src = $xd->getSourceUser();

                $names[$i] = $this->container->get('user_repository')->getInfoById('email', $src);
            }
        }

        $mail = $this->container->get('user_repository')->getInfoById('email', $id);

        $messages = $this->container->get('flash')->getMessages();

        $reqMsg = $messages['Reqs'] ?? [];
        $reqErrMsg = $messages['ReqsErr'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'pendingRequests.twig',
            [
                'reqs'=>$reqs,
                'mail'=>$mail,
                'names'=>$names,
                'msgs' =>$reqMsg,
                'errMsgs' =>$reqErrMsg
            ]
        );
    }

    public function accept (Request $request, Response $response, $args): Response
    {
        $trans = $this->container->get('transaction_repository')->getTransById($args['id']);

        if (empty($_SESSION['user_id'])) {
            return $response->withHeader('Location', '/sign-in')->withStatus(302);
        }

        $id = $_SESSION['user_id'];

        if($id == $trans->getDestUser()){
            $money = $this->container->get('user_repository')->getInfoById('money', $id);
            if($money > $trans->getMoney()){
                $this->container->get('transaction_repository')->updateStatus($args['id'], 'accepted');

                $this->container->get('user_repository')->updateMoney($id,-$trans->getMoney());
                $this->container->get('user_repository')->updateMoney($trans->getSourceUser(),$trans->getMoney());

                $this->container->get('flash')->addMessage('Reqs', "Request accepted succesfully!");
            } else {
                $this->container->get('flash')->addMessage('ReqsErr', "You don't have enough money to do that!");
            }
        } else {
            $this->container->get('flash')->addMessage('ReqsErr', "That request in not for you!");
        }

        return $response->withHeader('Location', '/account/money/requests/pending')->withStatus(302);
    }
}
