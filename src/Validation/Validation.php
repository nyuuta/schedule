<?php

    namespace app\Validation;

    use Exception;
    use app\Exception\ValidationException;
    use app\Validation\ValidationRule;
    
    class Validation {

        public function validate($parameters) {

            foreach( $this->paramKeys as $key ) {

                // リクエストパラメータが存在しない場合は例外
                if ( ! array_key_exists($key, $parameters) ) {
                    throw new Exception("Request parameters don't exist.", 500);
                }

                // バリデーションルールが存在しない場合は例外
                if ( ! is_callable(array("app\Validation\ValidationRule", $key)) ) {
                    throw new Exception("Validation rules don't exist.", 500);
                }

                $success = ValidationRule::$key($parameters[$key]);
                if ( $success === false ) {
                    throw (new ValidationException(implode("\n", $this->errors), 301))
                        ->setParams($parameters)
                        ->setErrors($this->errors);
                }

                return ;
            }
        }
    }

?>