<?php

    namespace app\Validation;

    use app\Validation\Validation;
    
    class PreRegisterValidation extends Validation {

        protected $paramKeys = [
            "mail"
        ];

        protected $errors = [
            "mail" => MSG_INVALID_MAIL
        ];
    }

?>