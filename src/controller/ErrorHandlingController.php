<?php

    namespace app\controller;

    class ErrorHandlingController {

        public static function error500() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/server-error.php");
        }

        public static function error404() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/file-not-found-error.php");
        }

        public static function errorInvalidToken() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/token-error.php");
        }

        public static function errorAlreadyRegistered() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/already-register-error.php");
        }
    }
?>