<?php

    require_once "./Log.php";
    require_once "./src/helper/CSRF.php";
    require_once "./src/helper/Helper.php";
    require_once "./src/helper/Session.php";

    class LogoutController {

        public function logout() {

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