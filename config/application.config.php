<?php
return array(
    'modules' => array(
        'Balance',
    ),
    'service_manager' => array(
        'factories' => array(
            'db' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            'module',
        ),
    ),
);
