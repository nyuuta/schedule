<?php

    namespace app\Exception;

    use Exception;
    
    class AuthorizationException extends Exception {

        // public function __construct() {
        //     $this->message = "permission denied.";
        //     $this->redirectTo = "/";
        // }

        // public function getRedirectTo() {
        //     return $this->redirectTo;
        // }

        private $redirectURI;

        public function setRedirectURI($uri) {
            $this->redirectURI = $uri;
            return $this;
        }
        
        public function getRedirectURI() {
            return $this->redirectURI;
        }
    }

?>