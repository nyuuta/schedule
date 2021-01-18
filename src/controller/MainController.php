<?php

    namespace app\controller;

    use app\model\Users;
    use app\helper\Helper;

    class MainController {

        public static function show() {

            $isLogin = Users::isLogin();
            if ($isLogin === false) {
                Helper::redirectTo("/");
            }

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/main.php");
        }
    }
?>