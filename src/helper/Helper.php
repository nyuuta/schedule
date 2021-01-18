<?php

    namespace app\helper;

    class Helper {

        public static function h($str) {
            return htmlspecialchars($str, ENT_QUOTES, "utf-8");
        }

        public static function redirectTo($uri) {
            $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $uri;
            header("Location: " . $url);
            exit();
        }
    }

?>