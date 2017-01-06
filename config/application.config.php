<?php
return [
    'modules' => [
        'Balance',
        'BalanceTags',
        'BalanceReports',
        'TwbBundle',
    ],
    'service_manager' => [
        'factories' => [
            'db' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            'module',
        ],
    ],
];
