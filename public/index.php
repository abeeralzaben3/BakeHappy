<?php
$config = require __DIR__ . '/../app/config/config.php';
define('BASE_URL', $config['base_url']);

// Load any class automatically from core/ or models/
spl_autoload_register(function ($class) {
    foreach (['core', 'models'] as $dir) {
        $file = __DIR__ . "/../app/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

Session::start();
new App();