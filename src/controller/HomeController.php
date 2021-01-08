<?php

    require_once "./Log.php";
    require_once "./src/helper/Session.php";

    class HomeController {

        public function show() {

            $userID = Session::get("userID");
            $isLogin = ! empty($userID); 

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/home.php");
        }
    }
?>