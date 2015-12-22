<?php

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Balance\Mvc\Controller\Home',
                        'action'     => 'index',
                    ],
                ],
            ],
            'configs' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/configs',
                    'defaults' => [
                        'controller' => 'Balance\Mvc\Controller\Configs',
                    ],
                ],
                'may_terminate' => false,
                'child_routes'  => [
                    'js' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/configs.js',
                            'defaults' => [
                                'action' => 'js',
                            ],
                        ],
                    ],
                    'modules' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/modules',
                            'defaults' => [
                                'action' => 'modules',
                            ],
                        ],
                    ],
                ],
            ],
            'accounts' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/accounts',
                    'defaults' => [
                        'controller' => 'Balance\Mvc\Controller\Accounts',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'add' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'action' => 'edit',
                            ],
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                        ],
                    ],
                    'remove' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/remove/:id',
                            'defaults' => [
                                'action' => 'remove',
                            ],
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                        ],
                    ],
                    'order' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/order',
                            'defaults' => [
                                'action' => 'order',
                            ],
                        ],
                    ],
                ],
            ],
            'postings' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/postings',
                    'defaults' => [
                        'controller' => 'Balance\Mvc\Controller\Postings',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'add' => [
                        'type'    => 'literal',
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'action' => 'edit',
                            ],
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                        ],
                    ],
                    'remove' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/remove/:id',
                            'defaults' => [
                                'action' => 'remove',
                            ],
                            'constraints' => [
                                'id' => '[0-9]+',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'navigation' => [
        'default' => [
            [
                'label' => 'Balance',
                'route' => 'home',
                'pages' => [
                    [
                        'label' => 'Home',
                        'route' => 'home',
                        'order' => 0,
                    ],
                    [
                        'label' => 'Contas',
                        'route' => 'accounts',
                        'order' => 100,
                        'pages' => [
                            [
                                'label' => 'Listar',
                                'route' => 'accounts',
                            ],
                            [
                                'label' => 'Adicionar',
                                'route' => 'accounts/add',
                            ],
                            [
                                'label'   => 'Editar',
                                'route'   => 'accounts/edit',
                                'visible' => false,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Lançamentos',
                        'route' => 'postings',
                        'order' => 200,
                        'pages' => [
                            [
                                'label' => 'Listar',
                                'route' => 'postings',
                            ],
                            [
                                'label' => 'Adicionar',
                                'route' => 'postings/add',
                            ],
                            [
                                'label'   => 'Editar',
                                'route'   => 'postings/edit',
                                'visible' => false,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Configurações',
                        'route' => 'configs/modules',
                        'order' => 300,
                        'pages' => [
                            [
                                'label' => 'Módulos',
                                'route' => 'configs/modules',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'balance_manager' => [
        'factories' => [
            // Controllers
            'Balance\Mvc\Controller\Accounts' => [
                'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                'params'  => [
                    'model'               => 'Balance\Model\Accounts',
                    'redirect_route_name' => 'accounts',
                ],
            ],
            'Balance\Mvc\Controller\Postings' => [
                'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                'params'  => [
                    'model'               => 'Balance\Model\Postings',
                    'redirect_route_name' => 'postings',
                ],
            ],

            // Models
            'Balance\Model\Accounts' => [
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => [
                    'form'                => 'Balance\Form\Accounts',
                    'input_filter'        => 'Balance\InputFilter\Accounts',
                    'form_search'         => 'Balance\Form\Search\Accounts',
                    'input_filter_search' => 'Balance\InputFilter\Search\Accounts',
                    'persistence'         => 'Balance\Model\Persistence\Accounts',
                ],
            ],
            'Balance\Model\Postings' => [
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => [
                    'form'                => 'Balance\Form\Postings',
                    'input_filter'        => 'Balance\InputFilter\Postings',
                    'form_search'         => 'Balance\Form\Search\Postings',
                    'input_filter_search' => 'Balance\InputFilter\Search\Postings',
                    'persistence'         => 'Balance\Model\Persistence\Postings',
                ],
            ],

            // TableGateway
            'Balance\Db\TableGateway\Accounts' => [
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => [
                    'table'       => 'accounts',
                    'primary_key' => 'id',
                    'sequence'    => 'accounts_id_seq',
                ],
            ],
            'Balance\Db\TableGateway\Postings' => [
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => [
                    'table'       => 'postings',
                    'primary_key' => 'id',
                    'sequence'    => 'postings_id_seq',
                ],
            ],
            'Balance\Db\TableGateway\Entries' => [
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => [
                    'table' => 'entries',
                ],
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            'Balance\Mvc\Controller\Home'    => 'Balance\Mvc\Controller\Home',
            'Balance\Mvc\Controller\Configs' => 'Balance\Mvc\Controller\Configs',
        ],
        'abstract_factories' => [
            'Balance\Mvc\Controller\Accounts' => 'Balance\Mvc\Controller\AbstractControllerFactory',
            'Balance\Mvc\Controller\Postings' => 'Balance\Mvc\Controller\AbstractControllerFactory',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            // Models
            'Balance\Model\Balance' => 'Balance\Model\Balance',
            'Balance\Model\Modules' => 'Balance\Model\Modules',
            // Persistences
            'Balance\Model\Persistence\Accounts' => 'Balance\Model\Persistence\Db\Accounts',
            'Balance\Model\Persistence\Postings' => 'Balance\Model\Persistence\Db\Postings',
            'Balance\Model\Persistence\Balance'  => 'Balance\Model\Persistence\Db\Balance',
            // Hydrators
            'Balance\Stdlib\Hydrator\Strategy\Datetime' => 'Balance\Stdlib\Hydrator\Strategy\Datetime',
        ],
        'abstract_factories' => [
            // Models
            'Balance\Model\Accounts' => 'Balance\Model\AbstractModelFactory',
            'Balance\Model\Postings' => 'Balance\Model\AbstractModelFactory',
            // TableGateways
            'Balance\Db\TableGateway\Accounts' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
            'Balance\Db\TableGateway\Postings' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
            'Balance\Db\TableGateway\Entries'  => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
        ],
        'factories' => [
            // Navegação
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ],
    ],

    'view_manager' => [
        'doctype' => 'HTML5',

        'display_exceptions'       => true,
        'display_not_found_reason' => true,

        'not_found_template' => 'error/404',
        'exception_template' => 'error/500',

        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/500'     => __DIR__ . '/../view/error/500.phtml',
        ],

        'template_path_stack' => [
            __DIR__ . '/../view',
        ],

        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'form_elements' => [
        'invokables' => [
            'select'   => 'Balance\Form\Element\Select',
            'boolean'  => 'Balance\Form\Element\Boolean',
            'datetime' => 'Balance\Form\Element\DateTime',
            'currency' => 'Balance\Form\Element\Currency',
        ],
    ],
];
