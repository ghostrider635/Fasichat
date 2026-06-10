<?php
require_once __DIR__ . '/../src/core/Config.php';
require_once __DIR__ . '/../src/core/Database.php';
require_once __DIR__ . '/../src/core/Router.php';
require_once __DIR__ . '/../src/services/SessionManager.php';
require_once __DIR__ . '/../src/services/SecurityService.php';

use App\Core\Config;
use App\Core\Router;
use App\Services\SessionManager;
use App\Services\SecurityService;

Config::load(__DIR__ . '/../config/app.php');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

SessionManager::start();
SecurityService::setSecureHeaders();

$action = $_GET['action'] ?? null;
Router::dispatch(['action' => $action]);
