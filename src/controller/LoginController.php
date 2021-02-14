<?php

    namespace app\controller;

    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\CSRF;
    use app\helper\Errors;
    use app\model\AccountManager;
    use app\Auth\Authorization;

    class LoginController {

        /**
         * ログイン画面を表示
         * 
         * ログアウト時のみリクエストを許可
         */
        public static function show() {

            $logger = new \app\helper\Log();
            $logger->info("START LoginController@show");

            // エラーメッセージの取得
            $errors = new Errors();
            $errors->create();

            Authorization::checkAuth(false);

            $logger->info("END");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/login.php");
        }

        public static function login() {

            $logger = new \app\helper\Log();
            $logger->info("START LoginController@login");

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                $logger->info("invalid csrf token");
                $logger->info("END redirect to /");
                Helper::redirectTo("/");
            }

            // パラメータ取得
            $mail = filter_input(INPUT_POST, "mail");
            $password = filter_input(INPUT_POST, "password");

            // ログイン試行処理
            $manager = new AccountManager();
            $manager->attemptLogin($mail, $password);

            // 処理完了メッセージをセット
            Helper::setFlashMessage(MSG_LOGIN_SUCCESS);

            $logger->info("END redirect to /.");
            return Helper::redirectTo("/");
        }
    }
?>