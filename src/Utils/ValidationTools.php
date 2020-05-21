<?php

namespace ProjWeb2\PRACTICA\Utils;


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
        if($month = 2){
            if((($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0))) && $month = 2) {
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
                if (date('m') - $month < 0)
                {
                    return false;
                } else {
                    if (date('m') - $month == 0)
                    {
                        if (date('d') - $day < 0)
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
}