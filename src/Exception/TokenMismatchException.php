<?php

    namespace app\Exception;

    use Exception;
    
    class TokenMismatchException extends Exception {

        public function __construct() {
            $this->message = "access token mismatch.";
            $this->redirectTo = "/";
        }

        public function getRedirectTo() {
            return $this->redirectTo;
        }
    }

?>