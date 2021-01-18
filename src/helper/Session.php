<?php

    namespace app\helper;

    class Session {

        public static function set($key, $value) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION[$key] = $value;
            return ;
        }

        public static function get($key) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            return $_SESSION[$key] ?? "";
        }

        public static function regenID() {
            session_regenerate_id();
        }

        public static function destroy() {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION = [];
            session_destroy();
        }

        public static function unset($key) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION[$key]);
        }
    }
?>