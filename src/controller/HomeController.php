<?php

    require_once "./Log.php";
    require_once "./src/model/Users.php";

    class HomeController {

        public function show() {

            $isLogin = Users::isLogin();

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/home.php");
        }
    }
?>