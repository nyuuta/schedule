<?php

    namespace app\controller;

    use app\model\Users;
    use app\helper\Helper;
    use app\Auth\Authorization;

    class MainController {

        public static function show() {

            $logger = new \app\helper\Log();
            $logger->info("START MainController@show");

            // $isLogin = Users::isLogin();
            // if ($isLogin === false) {
            //     $logger->info("user isn't login");
            //     $logger->info("END NG redirect to /");
            //     Helper::redirectTo("/");
            // }

            // ログイン状態のみを許可
            Authorization::checkAuth(true);

            include($_SERVER["DOCUMENT_ROOT"]."/src/view/main.php");
            $logger->info("END OK");
        }
    }
?>