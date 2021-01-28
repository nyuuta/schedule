<?php

    namespace app\Exception;

    use \app\helper\Log;
    use \app\helper\Helper;
    use \app\helper\Session;

    use \app\Exception\ValidationException;

    class ExceptionHandler {

        public static function handle($e) {

            $logger = new Log();
            $logger->error($e->getMessage());
            Helper::redirectTo($e->getRedirectTo());
        }

        public function handler($e) {

            // echo(get_class($e));
            // echo($e->getMessage());
            // echo($e->getLine());
            // echo($e->getTraceAsString());
            if ($e instanceof ValidationException) {
                // ログ出力

                // 入力値保持
                foreach( $e->getParams() as $key => $value) {
                    Session::set($key, $value);
                }

                // メッセージ保持
                foreach( $e->getErrors() as $key => $value) {
                    Session::set("message", $value);
                }

                // 直前ページへリダイレクト
                Helper::redirect($_SERVER["HTTP_REFERER"]);
            } else {
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }

        }
    }

?>