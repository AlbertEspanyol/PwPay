<?php

namespace ProjWeb2\PRACTICA\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

final class ValidationTools{

    //Funcio que comporva si les credencials son valides segons l'enunciat
    public function isValid(?string $email, ?string $password, ?string $birthday): array
    {
        //S'inicialitza l'array de errors a 3 caselles
        $errors = ["xd", "xd", "xd"];

        //S'obte nomes el domini del mail
        $popmail = explode('@', $email);

        //Si el string no s'asembla ni pa atras a un mail o si no es del domini 'salle.url.edu' li afegim un missatge d'error
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL) || $popmail[1] !== "salle.url.edu") {
            $errors[0] = sprintf('The email is not valid');
        }


        if($password != null) {
            //String regex que basicament especifica que la contra ha de contenir majuscules ([A-Z]) minuscules ([a-z]) i nomnbres ([0-9])
            $passpattern = '/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).*$/';

            //Si la contrasenya es mes petita de 6 caracters o si no compleix el regex anterior afegim error
            if (strlen($password) < 6 || !preg_match($passpattern, $password) /*|| !preg_match("/[0-9]/",$password || !preg_match('/[A-Z]/', $password))*/) {
                $errors[1] = sprintf('The password is not valid');
            }
        }
        //En cas de que no sigui null, es a dir, l'usuari s'estigui registrant, comporvem les dades
        if($birthday !== null){
            //Transformem la data en un string
            $date = date_parse($birthday);

            //Fer les comprovacions corresponents a una data valida i tambe per a que sigui major de 18 anys
            if($date['year'] > date("Y") || $date['month'] > 12 || !$this->checkDays($date['day'], $date['month'], $date['year']) /*|| strtotime($birthday) < 18 * 31536000*/){
                $errors[2] = sprintf('The date is not valid');
            }
        }

        return $errors;
    }

    //Funcio que comprova la validesa dels dies
    private function checkDays(?int $day, ?int $month, ?int $year): bool {
        //30 o 31
        if($month < 8 && $month % 2 == 0 && $day > 30){
            return false;
        } else if ($month >= 8 && $month % 2 == 0 && $day > 31){

            return false;
        }

        //Febrer
        if($month == 2){
            if((($year % 4 == 0) && (($year % 100 != 0)) || ($year % 400 == 0))) {
                if ($day > 29) return false;
            } else{
                if ($day > 28) return false;
            }
        }

        //Si es major de 18
        if (date('Y') - $year < 18) {
            return false;
        } else {
            if (date('Y') - $year == 18)
            {
                if ((date('m') - $month) < 0)
                {
                    return false;
                } else {
                    if ((date('m') - $month) == 0)
                    {
                        if ((date('d') - $day) < 0)
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public function checkMoney(?string $money): string {
        if(is_numeric($money)){
            $cash = floatval($money);
            if($cash < 0){
                return "The input can't be negative";
            }
        } else {
            return 'The input is not a number';
        }

        return 'xd';
    }

    public function sendTokenMail(string $dst_mail, string $token): bool{
        //S'envia el mail amb el link que conte el token slimapp.test/activate?token=xxxxxx (Grande Pando)
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = 4;
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
            $mail->addAddress('carlos.fl@students.salle.url.edu', 'John Doe');

            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Activate your PwPay account!';
            $mail->Body    = '<div>
                                <div class="section">
                                    <h3 class="title has has-text-centered">
                                       Welcome to PwPay! 
                                    </h3>
                                    <hr>
                                    <p class="subtitle">
                                        Click the link to activate your account:
                                    </p>
                                </div>
                                <div class="section">
                                <a>
                                    slimapp.test/activate?token=' . $token . '
                                </a>
                                </div>
                              </div>';
            $mail->AltBody = 'Click this link to activate your account: slimapp.test/activate?token=' . $token;

            //send the message, check for errors
            if (!$mail->send()) {
                return false;
            } else {
                return true;
                //Section 2: IMAP
                //Uncomment these to save your message in the 'Sent Mail' folder.
//                    if (save_mail($mail)) {
//                       echo "Message saved!";
//                    }
            }

        } catch (Exception $e) {
            return false;
        }
    }
}