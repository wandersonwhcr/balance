<?php
return [
    'router' => [
        'routes' => [
            'tags' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/tags',
                    'defaults' => [
                        'controller' => 'BalanceTags\Mvc\Controller\Tags',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
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
            'balance' => [
                'pages' => [
                    'tags' => [
                        'label' => 'Etiquetas',
                        'route' => 'tags',
                        'order' => 150,
                        'pages' => [
                            'tags' => [
                                'label' => 'Listar',
                                'route' => 'tags',
                            ],
                            'add' => [
                                'label' => 'Adicionar',
                                'route' => 'tags/add',
                            ],
                            'edit' => [
                                'label'   => 'Editar',
                                'route'   => 'tags/edit',
                                'visible' => false,
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
            'BalanceTags\Mvc\Controller\Tags' => [
                'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                'params'  => [
                    'model'               => 'BalanceTags\Model\Tags',
                    'redirect_route_name' => 'tags',
                ],
            ],
            // Models
            'BalanceTags\Model\Tags' => [
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => [
                    'form'                => 'BalanceTags\Form\Tags',
                    'input_filter'        => 'BalanceTags\InputFilter\Tags',
                    'form_search'         => 'BalanceTags\Form\Search\Tags',
                    'input_filter_search' => 'BalanceTags\InputFilter\Search\Tags',
                    'persistence'         => 'BalanceTags\Model\Persistence\Tags',
                ],
            ],
            // TableGateway
            'BalanceTags\Db\TableGateway\Tags' => [
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => [
                    'table'       => 'tags',
                    'primary_key' => 'id',
                    'sequence'    => 'tags_id_seq',
                ],
            ],
        ],
    ],

    'controllers' => [
        'abstract_factories' => [
            'BalanceTags\Mvc\Controller\Tags' => 'Balance\Mvc\Controller\AbstractControllerFactory',
        ],
    ],

    'service_manager' => [
        'invokables' => [
            // Persistences
            'BalanceTags\Model\Persistence\Tags' => 'BalanceTags\Model\Persistence\Db\Tags',
            // Gerenciador de Eventos
            'BalanceTags\EventManager\Postings' => 'BalanceTags\EventManager\Postings',
        ],
        'abstract_factories' => [
            // Models
            'BalanceTags\Model\Tags' => 'Balance\Model\AbstractModelFactory',
            // TableGateways
            'BalanceTags\Db\TableGateway\Tags' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
