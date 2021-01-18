<?php

    require_once "./src/helper/Log.php";
    require_once "./src/model/Users.php";
    require_once "./src/helper/Helper.php";

    class MainController {

        public function show() {

            $isLogin = Users::isLogin();
            if ($isLogin === false) {
                Helper::redirectTo("/");
            }

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/main.php");
        }
    }
?>