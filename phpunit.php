<?php

chdir(__DIR__);

require 'vendor/autoload.php';

$application = Zend\Mvc\Application::init(require 'config/application.config.php');

Balance\Test\Mvc\Application::setApplication($application);
