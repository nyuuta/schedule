<?php

    require_once "./DB.php";
    require_once "./Log.php";
    require_once "./src/model/Users.php";
    require_once "./src/model/PreUsers.php";
    require_once "./src/helper/CSRF.php";
    require_once "./src/helper/Helper.php";
    require_once "./src/helper/Session.php";

    class LoginController {

        public function show() {

            $message = Session::get("message");
            $mail = Session::get("mail");

            Session::destroy();

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/login.php");
        }

        public function login() {

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                Helper::redirectTo("/server-error");
                exit();
            }

            // 妥当なメールアドレスとパスワードが入力されていない場合はログイン画面へ戻る
            if ((!$mail = filter_input(INPUT_POST, "mail")) || (!$password = filter_input(INPUT_POST, "password"))) {
                Session::set("message", "メールアドレスかパスワードが間違っています。");
                Helper::redirectTo("/login");
            }

            $user = new Users();
            try {
                if (!$user->login($mail, $password)) {
                    Session::set("message", "メールアドレスかパスワードが間違っています。");
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