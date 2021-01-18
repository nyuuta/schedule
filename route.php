<?php

    return [

        ["GET", "/", "app\controller\HomeController::show", "home"],
        ["GET", "/pre-register", "app\controller\PreRegisterController::show"],
        ["POST", "/pre-register", "app\controller\PreRegisterController::preRegister"],
        ["GET", "/register", "app\controller\RegisterController::show"],
        ["POST", "/register", "app\controller\RegisterController::register"],
        ["GET", "/login", "app\controller\LoginController::show"],
        ["POST", "/login", "app\controller\LoginController::login"],
        ["POST", "/logout", "app\controller\LogoutController::logout"],
        ["GET", "/main", "app\controller\MainController::show"],
        ["GET", "/account-delete", "app\controller\AccountDeleteController::show"],
        ["POST", "/account-delete", "app\controller\AccountDeleteController::delete"],
        ["POST", "/ajax/createSchedule", "app\controller\AjaxScheduleController::create"],
        ["GET", "/ajax/readSchedule", "app\controller\AjaxScheduleController::read"],
        ["POST", "/ajax/updateSchedule", "app\controller\AjaxScheduleController::update"],
        ["POST", "/ajax/deleteSchedule", "app\controller\AjaxScheduleController::delete"],
        ["GET", "/server-error", "app\controller\ErrorHandlingController::error500"]
    ];

?>