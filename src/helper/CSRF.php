<?php

    require_once "./src/helper/Session.php";

    class CSRF {

        public static function generate() {
            
            $token = bin2hex(random_bytes(16));
            Session::set("token", $token);

            return $token;
        }

        public static function validate($token) {

            $sToken = Session::get("token");

            // hiddenパラメータで送られてくるトークンかセッション変数に保持されているトークンが不適切
            // もしくは、トークンが不一致の場合は失敗
            if ( empty($token) || empty($sToken) || ! hash_equals($token, $sToken)) {
                return false;
            }

            return true;
        }
    }

?>