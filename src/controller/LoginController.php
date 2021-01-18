<?php

    require_once "./DB.php";
    require_once "./src/helper/Log.php";
    require_once "./src/model/Users.php";
    require_once "./src/model/PreUsers.php";
    require_once "./src/helper/CSRF.php";
    require_once "./src/helper/Helper.php";
    require_once "./src/helper/Session.php";
    require_once "./src/helper/message.php";

    class LoginController {

        /**
         * ログイン画面を表示
         * 既にログイン済みの場合はトップへリダイレクト
         * 
         */
        public function show() {

            $isLogin = Users::isLogin();

            if ($isLogin === true) {
                Helper::redirectTo("/");
            }

            $message = Session::get("message");
            $mail = Session::get("mail");

            Session::set("message", "");
            Session::set("mail", "");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/login.php");
        }

        public function login() {

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                Helper::redirectTo("/server-error");
            }

            // 妥当なメールアドレスとパスワードが入力されていない場合はログイン画面へ戻る
            if ((!$mail = filter_input(INPUT_POST, "mail")) || (!$password = filter_input(INPUT_POST, "password"))) {
                Session::set("message", MSG_LOGIN_FAIL);
                Helper::redirectTo("/login");
            }

            $user = new Users();
            try {

                list($success, $type) = $user->login($mail, $password);

                if ($success === false) {
                    $message = ($type === "fail") ? MSG_LOGIN_FAIL : MSG_LOGIN_LOCK;
                    Session::set("message", $message);
                    Session::set("mail", $mail);
                    Helper::redirectTo("/login");
                } 
            } catch (PDOException $e) {
                Helper::redirectTo("/server-error");
            }

            // ログイン情報を保持してリダイレクト
            Session::regenID();
            Session::set("userID", $user->getID());
            Helper::redirectTo("/");
        }
    }
?>