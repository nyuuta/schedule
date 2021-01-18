<?php

    namespace app\controller;

    use app\model\Users;
    use app\helper\Log;

    class HomeController {

        public static function show() {

            $isLogin = Users::isLogin();

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/home.php");
        }
    }
?>