<?php

    namespace app\helper;

    use app\helper\Session;
    use app\helper\Helper;

    /**
     * バリデーションエラー時のメッセージ処理に関するクラス
     * 
     */
    class Errors {

        private $errors = [];

        public function create() {

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_SESSION["errors"])) {
                $this->errors = $_SESSION["errors"];
                unset($_SESSION["errors"]);
            }
        }

        public function get($key) {

            $msg = $this->errors[$key] ?? "";
            return Helper::h($msg);
        }

        public function all() {
            return $this->errors;
        }

    }

?>