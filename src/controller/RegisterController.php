<?php

    require_once "./DB.php";
    require_once "./Log.php";
    require_once "./src/model/Users.php";
    require_once "./src/model/PreUsers.php";

    class RegisterController {

        public function show() {

            session_start();

            if (!isset($_GET["k"])) {
                header("Location: http://192.168.99.100/token-error");
                exit() ;
            }

            $token = $_GET["k"];

            try {
                $dbh = DB::singleton()->get();
        
                $stmt = $dbh->prepare("SELECT * FROM pre_users WHERE token = ?");
                $stmt->bindValue(1, $token);
                $stmt->execute();
                $userData = $stmt->fetch();

                // 見つからなかった場合(適当なトークンだった場合)
                if ($userData === false) {
                    header("Location: http://192.168.99.100/token-error");
                    exit() ;
                }

                // 見つかったが、本登録済みだった場合
                if ($userData["enabled"] == 0) {
                    header("Location: http://192.168.99.100/already-register-error");
                    exit() ;
                }

                // 見つかり、仮登録済みだった場合で、かつ、トークンの期限が切れている場合
                if ($userData["expiration"] < time()) {
                    header("Location: http://192.168.99.100/token-error");
                    exit() ;
                }

            } catch (PDOException $e) {
                header("Location: http://192.168.99.100/server-error");
                exit() ;
            }

            $_SESSION["mail"] = $userData["mail"];
            $_SESSION["token"] = $userData["token"];
            $message = (isset($_SESSION["errorMessage"]) ? $_SESSION["errorMessage"] : "");
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/register.php");
        }

        public function confirm() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/register-confirm.php");
        }

        public function register() {

            session_start();

            $url = "http://192.168.99.100/register?k=".$_SESSION["token"];

            // 有効なパスワードが設定されていなければ拒否
            if (!isset($_POST["password"]) || !isset($_POST["password-confirm"])) {
                $_SESSION["errorMessage"] = "無効なパスワードです。";
                header("Location: ".$url);
                exit() ;
            }

            if (!$this->validate($_POST["password"], $_POST["password-confirm"])) {
                $_SESSION["errorMessage"] = "無効なパスワードです。";
                header("Location: ".$url);
                exit() ;
            }

            $userModel = new Users();
            try {
                $userModel->register($_SESSION["mail"], $_POST["password"]);
                $userModel->sendRegisterDoneMail();
                header("Location: http://192.168.99.100/register-confirm");
                exit() ;
            } catch (PDOException $e) {
                header("Location: http://192.168.99.100/server-error");
                exit() ;
            }
        }

        /**
         * 入力値のバリデーション
         */
        private function validate($password, $passwordConfirm) {

            $pattern = "/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}$/i";

            return (preg_match($pattern, $password) && ($password == $passwordConfirm));
        }
    }
?>