<?php

    namespace app\helper;

    use app\helper\Session;
    use app\helper\Helper;

    class Helper {

        public static function h($str) {
            return htmlspecialchars($str, ENT_QUOTES, "utf-8");
        }

        public static function redirectTo($uri) {
            $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $uri;
            header("Location: " . $url);
            exit();
        }

        public static function redirect($url) {
            header("Location: " . $url);
            exit();
        }

        /**
         * フォームに保持された値を返す
         * $name : inputタグに付けられたname属性の名前
         */
        public static function old($name) {

            $old = Session::get($name);
            Session::unset($name);

            return Helper::h($old);
        }

        public static function hasFlashMessage() {
            $message = Session::get("message");
            return !empty($message);
        }

        public static function getFlashMessage() {
            $message = Session::get("message");
            Session::unset("message");

            return Helper::h($message);
        }

        public static function setFlashMessage($message) {
            Session::set("message", $message);
            return ;
        }
    }

?>