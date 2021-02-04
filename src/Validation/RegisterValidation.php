<?php

    namespace app\Validation;

    use app\Validation\Validation;
    
    class RegisterValidation extends Validation {

        protected $paramKeys = [
            "password"
        ];

        protected $errors = [
            "password" => MSG_INVALID_PASSWORD
        ];
    }

?>