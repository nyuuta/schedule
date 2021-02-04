<?php

    namespace app\Exception;

    use Exception;
    
    class OneTimeTokenException extends Exception {

        private $errors;
        
        public function setErrors(array $errors) {
            $this->errors = $errors;
            return $this;
        }
        
        public function getErrors() {
            return $this->errors;
        }
    }

?>