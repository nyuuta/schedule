<?php

    require_once "vendor/autoload.php";
    require_once "./src/helper/message.php";

    $logger = new app\helper\Log();

    $logger->info("START routing");
    $logger->info("INPUT " . $_SERVER['REQUEST_URI']);

    // phpdotenvを用いて、環境変数を.envファイルから読み込み
    if (file_exists(__DIR__ . "/.env")) {
        $dotenv = Dotenv\Dotenv::createImmutable($_SERVER["DOCUMENT_ROOT"]);
        $dotenv->load();
    }
    // 以下、AltoRouterライブラリを用いたルーティング処理

    $router = new AltoRouter();

    $routes = require_once("./route.php");

    // 各ルート情報を...演算子で展開して$routerに渡す
    foreach( $routes as $route ) {
        $router->map(...$route);
    }
    
    $match = $router->match();

    if( is_array($match) && is_callable( $match["target"] ) ) {
        $logger->info("END found");
        $params = explode("::", $match["target"]);
        $action = new $params[0]();
        call_user_func_array(array($action, $params[1]) , $match["params"]);
    } else {
        $logger->info("END not found");
        header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        exit();
    }

?>