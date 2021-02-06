<?php

    namespace app\Validation;

    use Exception;
    use app\Exception\ValidationException;
    use app\Validation\ValidationRule;
    
    class Validation {

        public function validate($parameters) {

            $errors = [];

            foreach($parameters as $key => $value ) {

                // リクエストパラメータが各バリデーションクラスで設定したrulesで定義されていない場合は例外
                if (!array_key_exists($key, $this->rules)) {
                    throw new Exception("Request parameters don't match to rules.", 500);
                }

                // ルールの取得
                $ruleStrings = explode(",", $this->rules[$key]);

                foreach($ruleStrings as $rule) {
                    $method = explode(":", $rule)[0];
                    $args = array_slice(explode(":", $rule), 1);

                    if (!is_callable(array("app\Validation\ValidationRule", $method)) ) {
                        throw new Exception("Validation rule method doesn't exist.", 500);
                    }
                    $error = ValidationRule::$method($parameters, $key, $args) ?? "";
                    if ($error !== "") {
                        $errors[$key] = $errors[$key] ?? "" . "\n" . $error;
                    }
                }
            }

            if ( count($errors) > 0 ) {
                throw (new ValidationException("validation errors.", 301))
                    ->setParams($parameters)
                    ->setErrors($errors);
            }
        }
    }

?>