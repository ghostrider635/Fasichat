<?php
namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\ConvocationController;
use App\Controllers\DashboardController;
use App\Controllers\FileController;
use App\Controllers\MessageController;
use App\Controllers\MurController;
use App\Controllers\ValveController;

class Router
{
    public static function dispatch(array $request): void
    {
        $action = $request['action'] ?? 'home';

        match ($action) {
            'login' => AuthController::login(),
            'logout' => AuthController::logout(),
            'reset_password' => AuthController::resetPassword(),
            'contact_admin' => AuthController::contactAdmin(),
            'dashboard' => DashboardController::show(),
            'conversation_thread' => MessageController::thread(),
            'message_send' => MessageController::send(),
            'mur_publish' => MurController::publish(),
            'convocation_create' => ConvocationController::create(),
            'valve_list' => ValveController::list(),
            'valve_create' => ValveController::create(),
            'valve_update' => ValveController::update(),
            'valve_delete' => ValveController::delete(),
            'file_upload' => FileController::upload(),
            default => DashboardController::home(),
        };
    }
}
