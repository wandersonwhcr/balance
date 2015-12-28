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
];
