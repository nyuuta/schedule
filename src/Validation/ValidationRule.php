<?php

    namespace app\Validation;

    class ValidationRule {

        public static function mail($parameters, $key, $args) {

            $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
            if (!!preg_match($pattern, $parameters[$key]) === false) {
                return MSG_INVALID_MAIL;
            }

            return ;
        }

        public static function required($parameters, $key, $args) {

            if (empty($parameters[$key])) {
                return MSG_REQUIRED;
            }

            return ;
        }

        public static function password($parameters, $key, $args) {

            $pattern = "/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,32}$/i";
            if (!!preg_match($pattern, $parameters[$key]) === false) {
                return MSG_INVALID_PASSWORD;
            }

            return ;
        }

        public static function match($parameters, $key, $args) {

            $targetKey = $args[0];

            if ($parameters[$key] !== $parameters[$targetKey]) {
                return MSG_INCORRECT_PASSWORD;
            }

            return ;
        }
    }

?>