<?php

    namespace app\helper;

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    class Log {

        private $logger;

        function __construct() {
            $this->logger = new Logger("AppLog");
            $this->logger->pushHandler(new StreamHandler($_SERVER["DOCUMENT_ROOT"] . "/logs/app-log.log", Logger::INFO));
            $this->logger->pushHandler(new StreamHandler("php://stderr", Logger::INFO));
        }

        public function error($message) {
            $this->logger->error($message);
        }

        public function info($message) {

            $this->logger->info($message);
        }
    }
?>