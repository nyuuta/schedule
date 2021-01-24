<?php

    namespace app\controller;

    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\CSRF;

    class LogoutController {

        public static function logout() {

            $logger = new \app\helper\Log();
            $logger->info("START LogoutController@logout");

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");
            if ( ! CSRF::validate($token) ) {
                $logger->info("invalid csrf token");
                $logger->info("END redirect to /");
                Helper::redirectTo("/");
            }

            // セッション情報を破棄
            Session::destroy();
            $logger->info("END OK redirect to /");
            Helper::redirectTo("/");
        }
    }
?>