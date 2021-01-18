<?php

    require_once "vendor/autoload.php";
    require_once "./src/helper/message.php";

    $dotenv = Dotenv\Dotenv::createImmutable($_SERVER["DOCUMENT_ROOT"]);
    $dotenv->load();

    $reqUri = $_SERVER["REQUEST_URI"];
    $reqMethod = $_SERVER["REQUEST_METHOD"];

    $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $path = parse_url($url, PHP_URL_PATH);

    switch ($path) {
        case "/":
            $inst = new app\controller\HomeController();
            $inst->show();
            break;
        case "/pre-register":
            $inst = new app\controller\PreRegisterController();
            if ($reqMethod == "POST") {
                $inst->preRegister();
            } else {
                $inst->show();
            }
            break;
        case "/register":
            $inst = new app\controller\RegisterController();
            if ($reqMethod == "POST") {
                $inst->register();
            } else {
                $inst->show();
            }
            break;
        case "/server-error": 
            $inst = new app\controller\ErrorHandlingController();
            $inst->error500();
            break;
        case "/login": 
            $inst = new app\controller\LoginController();
            if ($reqMethod == "POST") {
                $inst->login();
            } else {
                $inst->show();
            }
            break;
        case "/logout": 
            $inst = new app\controller\LogoutController();
            if ($reqMethod == "POST") {
                $inst->logout();
            }
            break;
        case "/account-delete": 
            $inst = new app\controller\AccountDeleteController();
            if ($reqMethod == "GET") {
                $inst->show();
            } else {
                $inst->delete();
            }
            break;
        case "/main": 
            $inst = new app\controller\MainController();
            $inst->show();
            break;
        case "/ajax/createSchedule": 
            $inst = new app\controller\AjaxScheduleController();
            $inst->create();
            break;
        case "/ajax/readSchedule": 
            $inst = new app\controller\AjaxScheduleController();
            $inst->read();
            break;
        case "/ajax/updateSchedule": 
            $inst = new app\controller\AjaxScheduleController();
            $inst->update();
            break;
        case "/ajax/deleteSchedule": 
            $inst = new app\controller\AjaxScheduleController();
            $inst->delete();
            break;
        default :
            $inst = new app\controller\ErrorHandlingController();
            $inst->error404();
            break;
     }

?>