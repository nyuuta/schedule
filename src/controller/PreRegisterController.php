<?php

    namespace app\controller;

    use app\helper\Log;
    use app\helper\CSRF;
    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\DB;
    use app\model\PreUsers;
    use app\model\Users;

    use PDOException;

    class PreRegisterController {

        public function show() {

            session_start();

            $isLogin = Users::isLogin();

            if ($isLogin === true) {
                Helper::redirectTo("/");
            }

            $message = Session::get("message");
            $mail = Session::get("mail");

            Session::unset("message");
            Session::unset("mail");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register.php");
        }

        public function confirm() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register-confirm.php");
        }

        public function preRegister() {

            session_start();
    
            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                Helper::redirectTo("/");
            }

            // 妥当なメールアドレスが入力されていない場合はログイン画面へ戻る
            if ((!$mail = filter_input(INPUT_POST, "mail")) || (!$this->validate($mail))) {
                Session::set("message", MSG_INVALID_MAIL);
                Session::set("mail", $mail);
                Helper::redirectTo("/pre-register");
            }

            $preUsersModel = new PreUsers();
            try {
                // 仮登録に成功した場合はメール送信後遷移
                $success = $preUsersModel->preRegister($mail);
                if ($success === true) {
                    $preUsersModel->sendTokenURLMail();
                    Helper::redirectTo("/");
                } else {
                    Session::set("message", MSG_REGISTERED_MAIL);
                    Session::set("mail", $mail);
                    Helper::redirectTo("/pre-register");
                }
            } catch (PDOException $e) {
                Helper::redirectTo("/server-error");
            }
        }

        /**
         * メールアドレスのバリデーション
         */
        private function validate($mail) {

            $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";

            return preg_match($pattern, $mail);
        }
    }
?>