<?php

    namespace app\Exception;

    use Exception;
    use app\helper\Session;
    
    class PreRegisterFailException extends Exception {

        public function __construct() {
            $this->message = "pre-register fail.";
            $this->redirectTo = "/pre-register";
        }

        public function getRedirectTo() {
            return $this->redirectTo;
        }
    }

?>