<?php

    namespace app\Exception;

    use Exception;
    
    class ValidationException extends Exception {

        private $params;
        private $errors;

        public function setParams(array $params) {
            $this->params = $params;
            return $this;
        }
        
        public function getParams() {
            return $this->params;
        }
        
        public function setErrors(array $errors) {
            $this->errors = $errors;
            return $this;
        }
        
        public function getErrors() {
            return $this->errors;
        }
    }

?>