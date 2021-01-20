<?php

    namespace app\controller;

    use app\model\Users;
    use app\helper\Helper;

    class MainController {

        public static function show() {

            $logger = new \app\helper\Log();
            $logger->info("START MainController@show");

            $isLogin = Users::isLogin();
            if ($isLogin === false) {
                $logger->info("user isn't login");
                $logger->info("END NG redirect to /");
                Helper::redirectTo("/");
            }

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/main.php");
            $logger->info("END OK");
        }
    }
?>