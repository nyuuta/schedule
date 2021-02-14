<?php

    namespace app\controller;

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

            $logger = new \app\helper\Log();
            $logger->info("START AccountDeleteController@show");

            $isLogin = Users::isLogin();

            if ($isLogin === false) {
                $logger->info("user isn't login");
                $logger->info("END redirect to /");
                Helper::redirectTo("/");
            }

            $message = Session::get("message");
            Session::set("message", "");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/account-delete.php");
            $logger->info("END OK");
        }

        public static function delete() {

            $logger = new \app\helper\Log();
            $logger->info("START AccountDeleteController@delete");

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if ( ! CSRF::validate($token) ) {
                $logger->info("invalid csrf token");
                $logger->info("END redirect to /");
                Helper::redirectTo("/");
            }

            // ログインユーザIDの取得
            // ログインしていない場合はリダイレクト
            $id = Users::getUserID();
            if ($id === false) {
                $logger->info("user isn't login");
                $logger->info("END redirect to /");
                Helper::redirectTo("/");
            }

            // 妥当なパスワードが入力されていない場合は削除画面へ戻る
            if ( ! $password = filter_input(INPUT_POST, "password") ) {
                Session::set("message", MSG_PASSWORD_INCORRECT);
                $logger->info("invalid password");
                $logger->info("END redirect to /account-delete");
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

                Helper::setFlashMessage(MSG_DONE_ACCOUNT_DELETE);

                $logger->error("END OK");
                Helper::redirectTo("/");
            } catch (PDOException $e) {
                $logger->error($e->getMessage());
                $logger->error("END redirect to 500 internal server error");
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }
        }
    }
?>