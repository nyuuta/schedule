<?php

    namespace app\controller;

    use app\helper\Log;
    use app\helper\Session;
    use app\helper\Helper;
    use app\helper\CSRF;

    class LogoutController {

        public static function logout() {

            // CSRF対策のトークンチェック
            $token = filter_input(INPUT_POST, "token");
            if (!CSRF::validate($token)) {
                Helper::redirectTo("/server-error");
            }

            // セッション情報を破棄
            Session::destroy();
            Helper::redirectTo("/");
        }
    }
?>