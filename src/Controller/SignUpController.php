<?php

namespace ProjWeb2\PRACTICA\Controller;

use ProjWeb2\PRACTICA\Utils\ValidationTools;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DateTime;
use ProjWeb2\PRACTICA\Model\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

final class SignUpController
{
    //S'instancia la classe d'utilitats que ens servira per a validar les credencials
    private ValidationTools $vt;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->vt = new ValidationTools();
    }

    //Funcio que agafa les dades introduides per l'usuari, les comprova, crea i guarda l'usuari i en via un mail amb el token d'activacio
    public function create(Request $request, Response $response): Response
    {
        //Si l'usuari no ha escrit res al formulari no s'executa res mes
        if(empty($_POST)){
            exit;
        }

        try {
            //Agafaem les dades introduides per l'usuari al formulari
            $data = $request->getParsedBody();

            //Comprovem si les dades son correctes
            //La variable errors es un array de 3 caselles que corresponen als missatges d'error per l'email, la contrasenya i la data de naixement, respectivament
            $errors = $this->vt->isValid($data['email'], $data['password'], $data['birthday']);

            //Agafem tots els mails existents de la bbdd
            $allmails = $this->container->get('user_repository')->getInfo("email");

            //Els comparem amb l'introduit i si ja existaix ho marquem com a un error
            foreach ($allmails as $a){
                if($a == $data['email']){
                    $errors[0] = "This user already exists";
                }
            }

             //Com les caselles de la variable errors s'instancien amb el valor "xd", si arriben aqui amb aquest valor vol dir que no hi ha hagut cap error
            if ($errors[0] != "xd" || $errors[1] != "xd" || $errors[2] != "xd") {

                //Si hi ha algun error es mostra el mateix html pero omplenant les variables d'aquest amb els missatges d'error per a que es puguin mostrar
                // a mes de les dades previament introduides per l'usuari per a poder conservarles al refrescar la pagina
                //'isReg' = flag que especifica si la vista es de Registre(true) o de Login(false) (Canvien certs aspectes de la vista) (Fet aixi perque no es necessiten dos html casi identics)
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

            //Una vegada feta la comprovació es porcedeix a la funcionalitat del token
            //Primer s'agafen tots els ja existents de la bbdd
            $alltok = $this->container->get('user_repository')->getInfo('token');
            $flag = false;

            //Es genera el token que no es mes que la data actual amb hores, minuts i segons inclosos
            $dat = new DateTime();
            $td = date_parse($dat->format('Y-m-d H:i:s'));
            $token = $td['second'] . $td['minute'] . $td['hour'] . $td['day'] . $td['month'] . $td['year'];

            //Si aquest ja existeix se'n crea un de nou fins que estigui be
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

            //Variable perque si li fico directament el 0 no funcione
            $xd = 0;

            //Es genera un nou usuari amb totes les dades que li pertoquen
            $user = new User(
                $data['email'] ?? '',
                md5($data['password']) ?? '', //md5 encripta la contrasenya
                $data['birthday'] ?? '',
                new DateTime(),
                new DateTime(),
                0,
                $xd,
                $token
            );

            //Es guarda a la bbdd
            $this->container->get('user_repository')->save($user);

            //print phpinfo();
            //mail($data['email'], 'Activate your account', "slimapp.test/activate?token=" . $token);

            //S'envia el mail amb el link que conte el token slimapp.test/activate?token=xxxxxx (Grande Pando)
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->SMTPDebug = 4;
                // Enable verbose debug output
                $mail->isSMTP();
                $mail->Host       = '172.253.116.109';                    // Set the SMTP server to send through
                $mail->Port       = 587;
                $mail->SMTPAuth     = true;
                $mail->SMTPSecure   = 'tls';// TCP port to connect to
                $mail->SMTPAutoTLS = false;// Send using SMTP

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                //Recipients
                //Username to use for SMTP authentication - use full email address for gmail
                $mail->Username = 'pwpayalbert@gmail.com';

                //Password to use for SMTP authentication
                $mail->Password = 'muzaman898!!!';

                //Set who the message is to be sent from
                $mail->setFrom('pwpayalbert@gmail.com', 'First Last');

                //Set an alternative reply-to address
                $mail->addReplyTo('replyto@example.com', 'First Last');

                //Set who the message is to be sent to
                $mail->addAddress('albert.espanol@students.salle.url.edu', 'John Doe');

                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Here is the subject';
                $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                //send the message, check for errors
                if (!$mail->send()) {
                    echo 'Mailer Error: '. $mail->ErrorInfo;
                } else {
                    echo 'Message sent!';
                    //Section 2: IMAP
                    //Uncomment these to save your message in the 'Sent Mail' folder.
//                    if (save_mail($mail)) {
//                       echo "Message saved!";
//                    }
                }

            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        //Si no hi ha hagut cap exepcio es passa a la vista d'activacio per mail
        //'done' = flag si la activacio ha estat feta o no (canvia certs aspectes de la vista)
        //'icon' = la icona a mostrar
        //'titleMsg' = el missatge de titol
        //'subttlMsg' = el missatge de subtitol
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

    //Funcio encarregada de processar l'activacio del usuari una vegada ha accedit al link amb el token
    public function activate(Request $request, Response $response): Response
    {
        //S'agafen els parametres del link (?token=xxxxxxx)
        $token = $request->getQueryParams();
        $token = $token['token'];

        //Es comprova si aquest token ja esta activat o no
        if(!$this->container->get('user_repository')->checkActiveWToken($token)){
            //Si no ho esta, s'activa a la base de dades
            $this->container->get('user_repository')->activateUser($token);

            //Es mostra la pantalla d'activació amb exit
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

        //Si l'usuari ja esta activat es mostra la pantalla d'activacio amb exit pero amb un missatge comunicant l'error
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

    //Funcio que mostra la vista del registre
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
