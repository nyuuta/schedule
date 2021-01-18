<?php

    namespace app\helper;

    use app\helper\Session;

    class CSRF {

        public static function generate() {

            $token = bin2hex(session_id());
            Session::set("token", $token);

            return $token;
        }

        public static function validate($token) {

            $sToken = Session::get("token");
            Session::unset("token");

            // hiddenパラメータで送られてくるトークンかセッション変数に保持されているトークンが不適切
            // もしくは、トークンが不一致の場合は失敗
            if ( empty($token) || empty($sToken) || ! hash_equals($token, $sToken)) {
                return false;
            }

            return true;
        }
    }

?>