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

                $dns = "mysql:host=".$host.";dbname=".$database.";charset=utf8mb4";
                $option = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
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