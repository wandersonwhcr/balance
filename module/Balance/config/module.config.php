<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Balance\Mvc\Controller\Home',
                        'action'     => 'index',
                    ),
                ),
            ),
            'configs' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/configs',
                    'defaults' => array(
                        'controller' => 'Balance\Mvc\Controller\Configs',
                        'action'     => 'index',
                    ),
                ),
            ),
            'accounts' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/accounts',
                    'defaults' => array(
                        'controller' => 'Balance\Mvc\Controller\Accounts',
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
                    'order' => array(
                        'type'    => 'literal',
                        'options' => array(
                            'route'    => '/order',
                            'defaults' => array(
                                'action' => 'order',
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
                        'controller' => 'Balance\Mvc\Controller\Postings',
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
            'Balance\Mvc\Controller\Accounts' => array(
                'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                'params'  => array(
                    'model'               => 'Balance\Model\Accounts',
                    'redirect_route_name' => 'accounts',
                ),
            ),
            'Balance\Mvc\Controller\Postings' => array(
                'factory' => 'Balance\Mvc\Controller\AbstractControllerFactory',
                'params'  => array(
                    'model'               => 'Balance\Model\Postings',
                    'redirect_route_name' => 'postings',
                ),
            ),

            // Models
            'Balance\Model\Accounts' => array(
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => array(
                    'form'                => 'Balance\Form\Accounts',
                    'input_filter'        => 'Balance\InputFilter\Accounts',
                    'form_search'         => 'Balance\Form\Search\Accounts',
                    'input_filter_search' => 'Balance\InputFilter\Search\Accounts',
                    'persistence'         => 'Balance\Model\Persistence\Accounts',
                ),
            ),
            'Balance\Model\Postings' => array(
                'factory' => 'Balance\Model\AbstractModelFactory',
                'params'  => array(
                    'form'                => 'Balance\Form\Postings',
                    'input_filter'        => 'Balance\InputFilter\Postings',
                    'form_search'         => 'Balance\Form\Search\Postings',
                    'input_filter_search' => 'Balance\InputFilter\Search\Postings',
                    'persistence'         => 'Balance\Model\Persistence\Postings',
                ),
            ),

            // TableGateway
            'Balance\Db\TableGateway\Accounts' => array(
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => array(
                    'table'       => 'accounts',
                    'primary_key' => 'id',
                    'sequence'    => 'accounts_id_seq',
                ),
            ),
            'Balance\Db\TableGateway\Postings' => array(
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => array(
                    'table'       => 'postings',
                    'primary_key' => 'id',
                    'sequence'    => 'postings_id_seq',
                ),
            ),
            'Balance\Db\TableGateway\Entries' => array(
                'factory' => 'Balance\Db\TableGateway\AbstractTableGatewayFactory',
                'params'  => array(
                    'table' => 'entries',
                ),
            ),
        ),
    ),

    'balance_i18n' => array(
        'locale'   => 'pt_BR',
        'timezone' => 'America/Sao_Paulo',
    ),

    'controllers' => array(
        'invokables' => array(
            'Balance\Mvc\Controller\Home'    => 'Balance\Mvc\Controller\Home',
            'Balance\Mvc\Controller\Configs' => 'Balance\Mvc\Controller\Configs',
        ),
        'abstract_factories' => array(
            'Balance\Mvc\Controller\Accounts' => 'Balance\Mvc\Controller\AbstractControllerFactory',
            'Balance\Mvc\Controller\Postings' => 'Balance\Mvc\Controller\AbstractControllerFactory',
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            // Models
            'Balance\Model\Balance' => 'Balance\Model\Balance',
            // Persistences
            'Balance\Model\Persistence\Accounts' => 'Balance\Model\Persistence\Db\Accounts',
            'Balance\Model\Persistence\Postings' => 'Balance\Model\Persistence\Db\Postings',
            'Balance\Model\Persistence\Balance'  => 'Balance\Model\Persistence\Db\Balance',
            // Hydrators
            'Balance\Stdlib\Hydrator\Strategy\Datetime' => 'Balance\Stdlib\Hydrator\Strategy\Datetime',
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
            // Navegação
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            // I18n
            'i18n' => function ($serviceLocator) {
                // Configurações
                $config = $serviceLocator->get('Config')['balance_i18n'];
                // Definição do Timezone no PHP
                date_default_timezone_set($config['timezone']);
                // Definição de Locale Padrão
                locale_set_default($config['locale']);
                // Inicialização
                return new Balance\I18n\I18n($config['locale']);
            },
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

        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'form_elements' => array(
        'invokables' => array(
            'select'   => 'Balance\Form\Element\Select',
            'boolean'  => 'Balance\Form\Element\Boolean',
            'datetime' => 'Balance\Form\Element\DateTime',
            'currency' => 'Balance\Form\Element\Currency',
        ),
    ),
);
