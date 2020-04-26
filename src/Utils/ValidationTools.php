<?php

namespace ProjWeb2\PRACTICA\Utils;


final class ValidationTools{

    public function isValid(?string $email, ?string $password, ?string $birthday): array
    {
        $errors = ["xd", "xd", "xd"];

        $popmail = explode('@', $email);

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL) || $popmail[1] !== "salle.url.edu") {
            $errors[0] = sprintf('The email is not valid');
        }

        if (strlen($password) < 6 || !preg_match("/[a-zA-Z]/",$password) || !preg_match("/[0-9]/",$password)) {
            $errors[1] = sprintf('The password is not valid');
        }

        if($birthday !== null){
            $date = date_parse($birthday);

            if($date['year'] > date("Y") || $date['month'] > 12 || !$this->checkDays($date['day'], $date['month'], $date['year']) || strtotime($birthday) < 18 * 31536000){
                $errors[2] = sprintf('The date is not valid');
            }
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
}