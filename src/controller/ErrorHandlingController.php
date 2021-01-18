<?php

    namespace app\controller;

    class ErrorHandlingController {

        public function error500() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/server-error.php");
        }

        public function error404() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/file-not-found-error.php");
        }

        public function errorInvalidToken() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/token-error.php");
        }

        public function errorAlreadyRegistered() {
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/already-register-error.php");
        }
    }
?>