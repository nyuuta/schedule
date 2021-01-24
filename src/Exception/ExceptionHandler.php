<?php

    namespace app\Exception;

    use \app\helper\Log;
    use \app\helper\Helper;

    class ExceptionHandler {

        public static function handle($e) {

            $logger = new Log();
            $logger->error($e->getMessage());
            Helper::redirectTo($e->getRedirectTo());
        }
    }

?>