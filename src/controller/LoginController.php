<?php

    namespace app\controller;

    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\DB;
    use app\helper\CSRF;
    use app\model\PreUsers;
    use app\model\Users;

    use PDOException;
    use app\Auth\Authorization;

    class LoginController {

        /**
         * ログイン画面を表示
         * 既にログイン済みの場合はトップへリダイレクト
         * 
         */
        public static function show() {

            $logger = new \app\helper\Log();
            $logger->info("START LoginController@show");

            Authorization::checkAuth(false);

            $message = Session::get("message");
            $mail = Session::get("mail");

            Session::set("message", "");
            Session::set("mail", "");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/login.php");
            $logger->info("END OK");
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

            // 妥当なメールアドレスとパスワードが入力されていない場合はログイン画面へ戻る
            if ((!$mail = filter_input(INPUT_POST, "mail")) || (!$password = filter_input(INPUT_POST, "password"))) {
                Session::set("message", MSG_LOGIN_FAIL);
                $logger->info("invalid parameter");
                $logger->info("END redirect to /login");
                Helper::redirectTo("/login");
            }

            $user = new Users();
            try {

                list($success, $type) = $user->login($mail, $password);

                if ($success === false) {
                    $message = ($type === "fail") ? MSG_LOGIN_FAIL : MSG_LOGIN_LOCK;
                    Session::set("message", $message);
                    Session::set("mail", $mail);
                    $logger->info("login fail");
                    $logger->info("END redirect to /login");
                    Helper::redirectTo("/login");
                } 
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END 500 internal server error");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }

            // ログイン情報を保持してリダイレクト
            Session::regenID();
            Session::set("userID", $user->getID());
            $logger->info("login success");
            $logger->info("END redirect to /login");
            Helper::redirectTo("/");
        }
    }
?>