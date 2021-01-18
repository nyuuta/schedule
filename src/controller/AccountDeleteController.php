<?php

    require_once "./DB.php";
    require_once "./src/helper/Log.php";
    require_once "./src/model/Users.php";
    require_once "./src/model/PreUsers.php";
    require_once "./src/helper/CSRF.php";
    require_once "./src/helper/Helper.php";
    require_once "./src/helper/Session.php";
    require_once "./src/helper/message.php";

    class AccountDeleteController {

        /**
         * アカウント削除用画面を表示
         * ログインしていない場合はトップへリダイレクト
         * 
         */
        public function show() {

            $isLogin = Users::isLogin();

            if ($isLogin === false) {
                Helper::redirectTo("/");
            }

            $message = Session::get("message");
            Session::set("message", "");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/account-delete.php");
        }

        public function delete() {

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                Helper::redirectTo("/server-error");
            }

            // ログインユーザIDの取得
            // ログインしていない場合はリダイレクト
            $id = Users::getUserID();
            if ($id === false) {
                Helper::redirectTo("/");
            }

            // 妥当なパスワードが入力されていない場合は削除画面へ戻る
            if ( ! $password = filter_input(INPUT_POST, "password")) {
                Session::set("message", MSG_PASSWORD_INCORRECT);
                Helper::redirectTo("/account-delete");
            }

            // 削除
            try {
                if ( Users::identifyUserByPassword($id, $password) === false) {
                    Session::set("message", MSG_PASSWORD_INCORRECT);
                    Helper::redirectTo("/account-delete");
                }

                $users = new Users();
                $users->deleteAccount($id);
                Session::destroy();
                Helper::redirectTo("/");
            } catch (PDOException $e) {
                Helper::redirectTo("/server-error");
            }
        }
    }
?>