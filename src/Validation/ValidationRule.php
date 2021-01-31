<?php

    namespace app\Validation;

    class ValidationRule {

        public static function mail($mail) {

            $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";

            return !!preg_match($pattern, $mail);
        }
    }

?>