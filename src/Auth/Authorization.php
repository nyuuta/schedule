<?php

    namespace app\Auth;

    use \app\helper\Session;
    use \app\Exception\ExceptionHandler;
    use \app\Exception\AuthorizationException;
    
    class Authorization {

        // 要求されたログイン状態と現在のユーザのログイン状態を比較
        public static function checkAuth($requirement) {

            $redirectURI = ($requirement === true) ? "/login" : "/" ;

            $loginState = ! empty(Session::get("userID"));

            if ( $loginState !== $requirement ) {
                // ExceptionHandler::handle(new AuthorizationException());
                throw (new AuthorizationException("authorization doesn't match.", 301))
                ->setRedirectURI($redirectURI);
            }
        }

        public static function isLogin() {
            return ( ! empty(Session::get("userID")) );
        }
    }

?>