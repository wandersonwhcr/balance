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
                                'action' => 'remove',
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
                                'action' => 'remove',
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
                            array(
                                'label' => 'Editar',
                                'route' => 'accounts/edit',
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
                            array(
                                'label' => 'Editar',
                                'route' => 'postings/edit',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'balance_manager' => array(
        'factories' => array(
            // Controllers
            'Balance\Controller\Accounts' => array(
                'factory' => 'Balance\Controller\AbstractControllerFactory',
                'params'  => array(
                    'model'               => 'Balance\Model\Accounts',
                    'redirect_route_name' => 'accounts',
                ),
            ),
            'Balance\Controller\Postings' => array(
                'factory' => 'Balance\Controller\AbstractControllerFactory',
                'params'  => array(
                    'model'               => 'Balance\Model\Postings',
                    'redirect_route_name' => 'postings',
                ),
            ),

            // Models
            'Balance\Model\Accounts' => array(
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => array(
                    'form'         => 'Balance\Form\Accounts',
                    'form_search'  => 'Balance\Form\Search\Accounts',
                    'input_filter' => 'Balance\InputFilter\Accounts',
                    'persistence'  => 'Balance\Model\Persistence\Accounts',
                ),
            ),
            'Balance\Model\Postings' => array(
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => array(
                    'form'         => 'Balance\Form\Postings',
                    'form_search'  => 'Balance\Form\Search\Postings',
                    'input_filter' => 'Balance\InputFilter\Postings',
                    'persistence'  => 'Balance\Model\Persistence\Postings',
                ),
            ),

            // TableGateway
            'Balance\Db\TableGateway\Accounts' => array(
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => array(
                    'table'       => 'accounts',
                    'primary_key' => array('id'),
                    'sequence'    => 'accounts_id_seq',
                ),
            ),
            'Balance\Db\TableGateway\Postings' => array(
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => array(
                    'table'       => 'postings',
                    'primary_key' => array('id'),
                    'sequence'    => 'postings_id_seq',
                ),
            ),
            'Balance\Db\TableGateway\Entries' => array(
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => array(
                    'table'       => 'entries',
                    'primary_key' => array('account_id', 'posting_id'),
                    'sequence'    => false,
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Balance\Controller\Home' => 'Balance\Controller\Home',
        ),
        'abstract_factories' => array(
            'Balance\Controller\Accounts' => 'Balance\Controller\AbstractControllerFactory',
            'Balance\Controller\Postings' => 'Balance\Controller\AbstractControllerFactory',
        ),
    ),

    'service_manager' => array(
        'aliases' => array(
            //'translator' => 'MvcTranslator',
        ),
        'invokables' => array(
            // Persistences
            'Balance\Model\Persistence\Accounts' => 'Balance\Model\Persistence\Db\Accounts',
            'Balance\Model\Persistence\Postings' => 'Balance\Model\Persistence\Db\Postings',
        ),
        'abstract_factories' => array(
            // Models
            'Balance\Model\Accounts' => 'Balance\Model\AbstractModelFactory',
            'Balance\Model\Postings' => 'Balance\Model\AbstractModelFactory',
            // TableGateways
            'Balance\Db\TableGateway\Accounts' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
            'Balance\Db\TableGateway\Postings' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
            'Balance\Db\TableGateway\Entries'  => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
        ),
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),

    'view_manager' => array(
        'doctype' => 'HTML5',

        'display_exceptions'       => true,
        'display_not_found_reason' => true,

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

    'form_elements' => array(
        'invokables' => array(
            'select' => 'Balance\Form\Element\Select',
        ),
    ),
);
