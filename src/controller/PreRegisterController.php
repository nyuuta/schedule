<?php

    namespace app\controller;

    use app\helper\CSRF;
    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\DB;
    use app\model\PreUsers;
    use app\model\Users;

    use PDOException;
    use app\Auth\Authorization;

    class PreRegisterController {

        public static function show() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START PreRegisterController@show");

            Authorization::checkAuth(false);

            $logger->info("END");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register.php");
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

            $manager = new \app\model\AccountManager();
            $manager->preRegister($mail);

            $logger->info("END redirect to /.");
            return Helper::redirectTo("/");
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