<?php

    namespace app\controller;

    use app\helper\CSRF;
    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\DB;
    use app\model\PreUsers;
    use app\model\Users;

    use PDOException;

    class PreRegisterController {

        public static function show() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START PreRegisterController@show");

            $isLogin = Users::isLogin();

            if ($isLogin === true) {
                $logger->info("user isn't login");
                $logger->info("END NG redirect to /");
                Helper::redirectTo("/");
            }

            $message = Session::get("message");
            $mail = Session::get("mail");

            Session::unset("message");
            Session::unset("mail");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register.php");
            $logger->info("END OK");
        }

        public static function preRegister() {

            session_start();
    
            $logger = new \app\helper\Log();
            $logger->info("START PreRegisterController@preRegister");

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                $logger->info("invalid csrf token");
                $logger->info("END NG redirect to /");
                Helper::redirectTo("/");
            }

            // 妥当なメールアドレスが入力されていない場合はログイン画面へ戻る
            if ((!$mail = filter_input(INPUT_POST, "mail")) || (!self::validate($mail))) {
                Session::set("message", MSG_INVALID_MAIL);
                Session::set("mail", $mail);
                $logger->info("invalid parameter");
                $logger->info("END NG redirect to /pre-register");
                Helper::redirectTo("/pre-register");
            }

            $preUsersModel = new PreUsers();
            try {
                // 仮登録に成功した場合はメール送信後遷移
                $success = $preUsersModel->preRegister($mail);
                if ($success === true) {
                    $preUsersModel->sendTokenURLMail();
                    $logger->info("END OK redirect to /");
                    Helper::redirectTo("/");
                } else {
                    Session::set("message", MSG_REGISTERED_MAIL);
                    Session::set("mail", $mail);
                    $logger->info("mail is already registered");
                    $logger->info("END NG redirect to /pre-register");
                    Helper::redirectTo("/pre-register");
                }
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END NG 500 internal server error");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }
        }

        /**
         * メールアドレスのバリデーション
         */
        private static function validate($mail) {

            $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";

            return preg_match($pattern, $mail);
        }
    }
?>