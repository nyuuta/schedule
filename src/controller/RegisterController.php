<?php

    namespace app\controller;

    use app\helper\Log;
    use app\helper\CSRF;
    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\DB;
    use app\model\PreUsers;
    use app\model\Users;
    use app\Auth\Authorization;
    use app\model\AccountManager;
    use app\Validation\RegisterValidation;
    use app\helper\Errors;

    use PDOException;

    class RegisterController {

        public static function show() {

            // エラーメッセージの取得
            $errors = new Errors();
            $errors->create();

            $logger = new \app\helper\Log();
            $logger->info("START RegisterController@show");

            // 非ログイン状態のみ許可
            Authorization::checkAuth(false);

            // URLに含まれるトークンの妥当性チェック
            $token = filter_input(INPUT_GET, "k"); 
            $manager = new AccountManager();
            $manager->tokenValidation($token);

            // Viewの表示
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/register.php");
            $logger->info("END OK");
        }

        public static function register() {

            session_start();

            $logger = new \app\helper\Log();
            $logger->info("START RegisterController@register");

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if ( ! CSRF::validate($token) ) {
                $logger->info("invalid csrf token");
                $logger->info("END redirect to /");
                Helper::redirectTo("/");
            }

            // CSRFチェックを通り抜けた場合の直前のURL(本登録画面)からトークンを取得
            $prevUrl = $_SERVER["HTTP_REFERER"];
            $uri = parse_url($prevUrl, PHP_URL_PATH) . "?" . parse_url($prevUrl, PHP_URL_QUERY);
            $oneTimeToken = explode("=", parse_url($prevUrl, PHP_URL_QUERY))[1];

            // パスワードのバリデーション
            $password = filter_input(INPUT_POST, "password");
            $passwordConfirm = filter_input(INPUT_POST, "password-confirm");

            $validator = new RegisterValidation();
            $validator->validate([
                "password" => $password,
                "password-confirm" => $passwordConfirm
            ]);

            // 本登録処理
            $manager = new AccountManager();
            $manager->register($oneTimeToken, $password);

            // 処理完了メッセージをセット
            Session::destroy();
            Session::set("message", MSG_DONE_REGISTER);

            $logger->info("END redirect to /.");
            return Helper::redirectTo("/");
        }
    }
?>