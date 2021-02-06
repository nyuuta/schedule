<?php

    namespace app\Validation;

    use app\Validation\Validation;
    
    class RegisterValidation extends Validation {

        // 想定しているリクエストパラメータのキーと適用するバリデーションルール
        protected $rules = [
            "password" => "required,password",
            "password-confirm" => "required,match:password"
        ];
    }

?>