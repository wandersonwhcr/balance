<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Balance\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),
            'accounts' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/accounts',
                    'defaults' => array(
                        'controller' => 'Balance\Controller\Accounts',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'add' => array(
                        'type'    => 'literal',
                        'options' => array(
                            'route'    => '/add',
                            'defaults' => array(
                                'action' => 'edit',
                                'id'     => 0,
                            ),
                        ),
                    ),
                    'edit' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/edit/:id',
                            'defaults' => array(
                                'action' => 'edit',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/remove/:id',
                            'defaults' => array(
                                'action' => 'edit',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                ),
            ),
            'postings' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/postings',
                    'defaults' => array(
                        'controller' => 'Balance\Controller\Postings',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'add' => array(
                        'type'    => 'literal',
                        'options' => array(
                            'route'    => '/add',
                            'defaults' => array(
                                'action' => 'edit',
                                'id'     => 0,
                            ),
                        ),
                    ),
                    'edit' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/edit/:id',
                            'defaults' => array(
                                'action' => 'edit',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/remove/:id',
                            'defaults' => array(
                                'action' => 'edit',
                            ),
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Balance',
                'route' => 'home',
                'pages' => array(
                    array(
                        'label' => 'Home',
                        'route' => 'home',
                    ),
                    array(
                        'label' => 'Contas',
                        'route' => 'accounts',
                        'pages' => array(
                            array(
                                'label' => 'Listar',
                                'route' => 'accounts',
                            ),
                            array(
                                'label' => 'Adicionar',
                                'route' => 'accounts/add',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Lançamentos',
                        'route' => 'postings',
                        'pages' => array(
                            array(
                                'label' => 'Listar',
                                'route' => 'postings',
                            ),
                            array(
                                'label' => 'Adicionar',
                                'route' => 'postings/add',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Balance\Controller\Home'     => 'Balance\Controller\Home',
            'Balance\Controller\Accounts' => 'Balance\Controller\Accounts',
            'Balance\Controller\Postings' => 'Balance\Controller\Postings',
        ),
    ),

    'view_manager' => array(
        'doctype' => 'HTML5',

        'display_exceptions'       => false,
        'display_not_found_reason' => false,

        'not_found_template' => 'error/404',
        'exception_template' => 'error/500',

        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/500'     => __DIR__ . '/../view/error/500.phtml',
        ),

        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
