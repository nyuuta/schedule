<?php

    namespace app\Validation;

    class ValidationRule {

        public static function mail($mail) {

            $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";

            return !!preg_match($pattern, $mail);
        }

        public static function password($passwords) {


            list($password, $passwordConfirm) = $passwords;
            if ($password !== $passwordConfirm) {
                return false;
            }

            $pattern = "/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,32}$/i";
            return !!preg_match($pattern, $password);
        }
    }

?>