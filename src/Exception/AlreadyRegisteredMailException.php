<?php

    namespace app\Exception;

    use Exception;
    use \app\helper\Session;
    
    class AlreadyRegisteredMailException extends Exception {

        public function __construct() {
            Session::set("message", MSG_REGISTERED_MAIL);
            Session::set("mail", $mail);
            $this->message = "This mail-address is already registered.";
            $this->redirectTo = "/pre-register";
        }

        public function getRedirectTo() {
            return $this->redirectTo;
        }
    }

?>