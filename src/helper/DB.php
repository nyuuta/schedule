<?php

    namespace app\helper;

    use PDO;
    use PDOException;

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

                // 環境変数から設定読み込み
                $host = $_SERVER["DB_HOST"];
                $database = $_SERVER["DB_NAME"];
                $user = $_SERVER["DB_USER"];
                $password = $_SERVER["DB_PASSWORD"];

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