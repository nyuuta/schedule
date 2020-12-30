<?php

    require_once "./DB.php";
    require_once "./Log.php";
    require_once "./src/model/PreUsers.php";

    class PreRegisterController {

        public function show() {

            session_start();

            // POST後、メールアドレス無効で画面に戻った時はメッセージと入力したメールアドレスを保持
            $message = "";
            $mail = "";
            if (isset($_SESSION["errorMessage"])) {
                $message = $_SESSION["errorMessage"];
                $mail = $_SESSION["errorMail"];
            }

            $_SESSION = array();

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register.php");
        }

        public function confirm() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register-confirm.php");
        }

        public function preRegister() {

            session_start();

            // 有効なメールアドレスが設定されていなければ拒否
            if (!isset($_POST["mail"])) {
                $_SESSION["errorMessage"] = "無効なメールアドレスです。";
                $_SESSION["errorMail"] = "";
                header("Location: http://192.168.99.100/pre-register");
                exit() ;
            }

            if (!$this->validate($_POST["mail"])) {
                $_SESSION["errorMessage"] = "無効なメールアドレスです。";
                $_SESSION["errorMail"] = $_POST["mail"];
                header("Location: http://192.168.99.100/pre-register");
                exit() ;
            }

            $preUsersModel = new PreUsers();
            try {
                // 仮登録に成功した場合はメール送信後遷移
                if ($preUsersModel->preRegister($_POST["mail"])) {
                    $preUsersModel->sendTokenURLMail();
                    header("Location: http://192.168.99.100/pre-register-confirm");
                    exit() ;
                } else {
                    $_SESSION["errorMessage"] = "本登録が完了しているメールアドレスです。";
                    $_SESSION["errorMail"] = $_POST["mail"];
                    header("Location: http://192.168.99.100/pre-register");
                    exit() ;
                }
            } catch (PDOException $e) {
                header("Location: http://192.168.99.100/server-error");
                exit() ;
            }
        }

        /**
         * 入力値のバリデーション
         */
        private function validate($mail) {

            $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";

            return preg_match($pattern, $mail);
        }
    }
?>