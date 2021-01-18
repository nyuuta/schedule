<?php

    require_once "./src/controller/PreRegisterController.php";
    require_once "./src/controller/RegisterController.php";
    require_once "./src/controller/LoginController.php";
    require_once "./src/controller/LogoutController.php";
    require_once "./src/controller/AccountDeleteController.php";
    require_once "./src/controller/HomeController.php";
    require_once "./src/controller/ErrorHandlingController.php";
    require_once "./src/controller/MainController.php";
    require_once "./src/controller/AjaxScheduleController.php";

    require_once "./src/controller/MailTestController.php";

    $dotenv = Dotenv\Dotenv::createImmutable($_SERVER["DOCUMENT_ROOT"]);
    $dotenv->load();

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
        case "/register":
            $inst = new RegisterController();
            if ($reqMethod == "POST") {
                $inst->register();
            } else {
                $inst->show();
            }
            break;
        case "/server-error": 
            $inst = new ErrorHandlingController();
            $inst->error500();
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
        case "/main": 
            $inst = new MainController();
            $inst->show();
            break;
        case "/ajax/createSchedule": 
            $inst = new AjaxScheduleController();
            $inst->create();
            break;
        case "/ajax/readSchedule": 
            $inst = new AjaxScheduleController();
            $inst->read();
            break;
        case "/ajax/updateSchedule": 
            $inst = new AjaxScheduleController();
            $inst->update();
            break;
        case "/ajax/deleteSchedule": 
            $inst = new AjaxScheduleController();
            $inst->delete();
            break;
        case "/mail": 
            $inst = new MailTestController();
            $inst->mail();
            break;
        default :
            $inst = new ErrorHandlingController();
            $inst->error404();
            break;
     }

?>