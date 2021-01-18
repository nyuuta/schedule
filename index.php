<?php

    require_once "vendor/autoload.php";
    require_once "./src/helper/message.php";

    // phpdotenvを用いて、環境変数を.envファイルから読み込み
    $dotenv = Dotenv\Dotenv::createImmutable($_SERVER["DOCUMENT_ROOT"]);
    $dotenv->load();

    // 以下、AltoRouterライブラリを用いたルーティング処理

    $router = new AltoRouter();

    $routes = require_once("./route.php");

    // 各ルート情報を...演算子で展開して$routerに渡す
    foreach( $routes as $route ) {
        $router->map(...$route);
    }
    
    $match = $router->match();
    
    if( is_array($match) && is_callable( $match["target"] ) ) {
      $params = explode("::", $match["target"]);
      $action = new $params[0]();
      call_user_func_array(array($action, $params[1]) , $match["params"]);
    } else {
      header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
      exit();
    }

?>