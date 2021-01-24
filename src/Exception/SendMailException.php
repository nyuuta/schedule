<?php

    namespace app\Exception;

    use Exception;
    
    class SendMailException extends Exception {

        public function __construct() {
            $this->message = "Send mail failed.";
            $this->redirectTo = "/";
        }

        public function getRedirectTo() {
            return $this->redirectTo;
        }
    }

?>