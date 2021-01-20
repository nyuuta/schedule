<?php

    namespace app\controller;

    use app\model\Users;

    class HomeController {

        public static function show() {

            $logger = new \app\helper\Log();
            $logger->info("START HomeController@show");

            $isLogin = Users::isLogin();
            include($_SERVER["DOCUMENT_ROOT"]."/src/view/home.php");

            $logger->info("END");
        }
    }
?>