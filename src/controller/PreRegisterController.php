<?php

    namespace app\controller;

    use app\helper\CSRF;
    use app\helper\Helper;
    use app\helper\Session;
    use app\Auth\Authorization;
    use app\Validation\PreRegisterValidation;
    use app\model\AccountManager;
    use app\helper\Errors;

    class PreRegisterController {

        public static function show() {

            $logger = new \app\helper\Log();
            $logger->info("START PreRegisterController@show");

            // エラーメッセージの取得
            $errors = new Errors();
            $errors->create();

            Authorization::checkAuth(false);

            $logger->info("END");

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/pre-register.php");
        }

        public static function preRegister() {

            session_start();
    
            $logger = new \app\helper\Log();
            $logger->info("START PreRegisterController@preRegister");

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");

            if (!CSRF::validate($token)) {
                $logger->info("invalid csrf token");
                $logger->info("END NG redirect to /");
                Helper::redirectTo("/");
            }

            // メールアドレスのバリデーション
            $mail = filter_input(INPUT_POST, "mail");
            $validator = new PreRegisterValidation();
            $validator->validate(["mail" => $mail]);

            // 仮登録処理
            $manager = new AccountManager();
            $manager->preRegister($mail);

            // 処理完了メッセージをセット
            Session::set("message", MSG_DONE_PREREGISTER);

            $logger->info("END redirect to /.");
            return Helper::redirectTo("/");
        }
    }
?>