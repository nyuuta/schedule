<?php

    class Log {

        function __construct() {

        }

        static function error($message) {
            error_log($message, 3, "./log.txt");
        }
    }
?>