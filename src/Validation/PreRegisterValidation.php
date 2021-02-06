<?php

    namespace app\Validation;

    use app\Validation\Validation;
    
    class PreRegisterValidation extends Validation {

        // 想定しているリクエストパラメータのキーと適用するバリデーションルール
        protected $rules = [
            "mail" => "required,mail"
        ];
    }

?>