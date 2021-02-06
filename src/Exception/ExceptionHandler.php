<?php

    namespace app\Exception;

    use \app\helper\Log;
    use \app\helper\Helper;
    use \app\helper\Session;
    
    use \app\Exception\ValidationException;
    use \app\Exception\OneTimeTokenException;

    class ExceptionHandler {

        public static function handle($e) {

            $logger = new Log();
            $logger->error($e->getMessage());
            Helper::redirectTo($e->getRedirectTo());
        }

        public function handler($e) {
            
            $logger = new Log();
            $logger->info($e->getMessage());
            $logger->info($e->getLine());

            if ($e instanceof ValidationException) {

                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                // 入力値保持
                foreach( $e->getParams() as $key => $value) {
                    Session::set($key, $value);
                }

                // メッセージ保持
                $_SESSION["errors"] = $e->getErrors();

                // 直前ページへリダイレクト
                Helper::redirect($_SERVER["HTTP_REFERER"]);
            } else if ($e instanceof OneTimeTokenException) {
                // メッセージ保持
                foreach( $e->getErrors() as $key => $value) {
                    Session::set("message", $value);
                }

                // 直前ページへリダイレクト
                Helper::redirectTo("/");
            }else {
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }

        }
    }

?>