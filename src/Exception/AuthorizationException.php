<?php

    namespace app\Exception;

    use Exception;
    
    class AuthorizationException extends Exception {

        public function __construct() {
            $this->message = "permission denied.";
            $this->redirectTo = "/";
        }

        public function getRedirectTo() {
            return $this->redirectTo;
        }
    }

?>