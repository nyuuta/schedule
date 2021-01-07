<?php

    class DB {

        private static $dbh;
        private static $instance;

        private function __construct() {

        }

        static function singleton() {
            if (!isset(self::$instance)) {
                self::$instance = new static;
            }
            return self::$instance;
        }

        public function get() {
            if (!isset(self::$dbh)) {

                // TODO: コンフィグファイル作成
                $host = "mysql";
                $database = "schedule";
                $user = "root";
                $password = "secret";

                $dns = "mysql:host=".$host.";dbname=".$database.";charset=utf8";
                $option = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];
    
                try {
                    self::$dbh = new PDO($dns, $user, $password, $option);
                } catch (PDOException $e) {
                    throw $e;
                }
            }
    
            return self::$dbh;
        }
    }
?>