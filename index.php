<?php

    require_once "./src/controller/PreRegisterController.php";
    require_once "./src/controller/RegisterController.php";
    require_once "./src/controller/LoginController.php";
    require_once "./src/controller/LogoutController.php";
    require_once "./src/controller/AccountDeleteController.php";
    require_once "./src/controller/HomeController.php";
    require_once "./src/controller/ErrorHandlingController.php";

    $reqUri = $_SERVER["REQUEST_URI"];
    $reqMethod = $_SERVER["REQUEST_METHOD"];

    $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $path = parse_url($url, PHP_URL_PATH);

    switch ($path) {
        case "/":
            $inst = new HomeController();
            $inst->show();
            break;
        case "/pre-register":
            $inst = new PreRegisterController();
            if ($reqMethod == "POST") {
                $inst->preRegister();
            } else {
                $inst->show();
            }
            break;
        case "/pre-register-confirm":
            $inst = new PreRegisterController();
            $inst->confirm();
            break;
        case "/register":
            $inst = new RegisterController();
            if ($reqMethod == "POST") {
                $inst->register();
            } else {
                $inst->show();
            }
            break;
        case "/register-confirm":
            $inst = new RegisterController();
            $inst->confirm();
            break;
        case "/server-error": 
            $inst = new ErrorHandlingController();
            $inst->error500();
            break;
        case "/token-error": 
            $inst = new ErrorHandlingController();
            $inst->errorInvalidToken();
            break;
        case "/already-register-error": 
            $inst = new ErrorHandlingController();
            $inst->errorAlreadyRegistered();
            break;
        case "/login": 
            $inst = new LoginController();
            if ($reqMethod == "POST") {
                $inst->login();
            } else {
                $inst->show();
            }
            break;
        case "/logout": 
            $inst = new LogoutController();
            if ($reqMethod == "POST") {
                $inst->logout();
            }
            break;
        case "/account-delete": 
            $inst = new AccountDeleteController();
            if ($reqMethod == "GET") {
                $inst->show();
            } else {
                $inst->delete();
            }
            break;
        default :
            $inst = new ErrorHandlingController();
            $inst->error404();
            break;
     }

?>