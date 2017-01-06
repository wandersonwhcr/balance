<?php
return [
    'router' => [
        'routes' => [
            'reports' => [
                'type'    => 'literal',
                'options' => [
                    'route' => '/relatorios',
                ],
                'child_routes' => [
                    'accounts' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/contas',
                            'defaults' => [
                                'controller' => 'BalanceReports\Mvc\Controller\Accounts',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            'balance' => [
                'pages' => [
                    'reports' => [
                        'label' => 'Relatórios',
                        'route' => 'reports/accounts',
                        'order' => 250,
                        'pages' => [
                            'accounts' => [
                                'label' => 'Contas',
                                'route' => 'reports/accounts',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
