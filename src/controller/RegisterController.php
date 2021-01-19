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

    class RegisterController {

        public static function show() {

            session_start();

            $isLogin = Users::isLogin();

            if ($isLogin === true) {
                Helper::redirectTo("/");
                exit();
            }

            // トークンが存在しない場合は不正扱い
            if (!$token = filter_input(INPUT_GET, "k")) {
                Helper::redirectTo("/");
            }

            $preUser = new PreUsers();
            try {

                list($success, $message) = $preUser->validateToken($token);
                if ($success === false) {
                    Helper::redirectTo("/");
                }

            } catch (PDOException $e) {
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }

            $message = Session::get("message");
            Session::unset("message");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/register.php");
        }

        public static function register() {

            session_start();

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                Helper::redirectTo("/");
            }

            $prevUrl = $_SERVER['HTTP_REFERER'];
            $uri = parse_url($prevUrl, PHP_URL_PATH) . "?" . parse_url($prevUrl, PHP_URL_QUERY);
            $userToken = explode("=", parse_url($prevUrl, PHP_URL_QUERY))[1];

            // 妥当なパスワードが入力されていない場合は1つ前の画面へ戻る
            if ((!$password = filter_input(INPUT_POST, "password")) || (!$passwordConfirm = filter_input(INPUT_POST, "password-confirm"))) {
                Session::set("message", MSG_INVALID_PASSWORD);
                Helper::redirectTo($uri);
            }

            if (!self::validate($password, $passwordConfirm)) {
                Session::set("message", MSG_INVALID_PASSWORD);
                Helper::redirectTo($uri);
            }

            $user = new Users();
            try {
                $user->register($userToken, $password);
                $user->sendRegisterDoneMail();
                Session::destroy();
                Helper::redirectTo("/");
            } catch (PDOException $e) {
                header( $_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
                exit();
            }
        }

        /**
         * 入力値のバリデーション
         */
        private static function validate($password, $passwordConfirm) {

            $pattern = "/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,32}$/i";

            return (preg_match($pattern, $password) && ($password == $passwordConfirm));
        }
    }
?>