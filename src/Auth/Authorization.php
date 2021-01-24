<?php

    namespace app\Auth;

    use \app\helper\Session;
    use \app\Exception\ExceptionHandler;
    use \app\Exception\AuthorizationException;
    
    class Authorization {

        public static function checkAuth($requirement) {

            $loginState = ! empty(Session::get("userID"));

            if ( $loginState !== $requirement ) {
                ExceptionHandler::handle(new AuthorizationException());
            }
        }

        public static function isLogin() {
            return ( ! empty(Session::get("userID")) );
        }
    }

?>