<?php

    namespace app\helper;

    class Log {

        function __construct() {

        }

        static function error($message) {

            error_log($message, 3, $_SERVER["LOG_PATH"]);
        }
    }
?>