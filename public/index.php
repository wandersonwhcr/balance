<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', true);

chdir(dirname(__DIR__));

if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

if (!is_file('vendor/autoload.php')) {
    exit(-1);
}

require 'vendor/autoload.php';

Zend\Mvc\Application::init(require 'config/application.config.php')->run();
