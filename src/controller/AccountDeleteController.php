<?php

    namespace app\controller;

    use app\helper\Log;
    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\CSRF;
    use app\helper\DB;
    use app\model\PreUsers;
    use app\model\Users;

    use PDOException;

    class AccountDeleteController {

        /**
         * アカウント削除用画面を表示
         * ログインしていない場合はトップへリダイレクト
         * 
         */
        public static function show() {

            $isLogin = Users::isLogin();

            if ($isLogin === false) {
                Helper::redirectTo("/");
            }

            $message = Session::get("message");
            Session::set("message", "");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/account-delete.php");
        }

        public static function delete() {

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