<?php

namespace Balance\Bug;

use BalanceTest\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Dom\Document;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class Issue181Test extends TestCase
{
    public function testDom()
    {
        // Gerenciador de Serviços
        $serviceManager = Application::getApplication()->getServiceManager();

        // Resolução de Scripts
        $resolver = new TemplateMapResolver([
            'error/404'          => './module/Balance/view/error/404.phtml',
            'layout/page-header' => './module/Balance/view/layout/page-header.phtml',
        ]);

        // Renderização
        $renderer = (new PhpRenderer())->setResolver($resolver);

        // Configurar Serviço
        $renderer->getHelperPluginManager()->setServiceLocator($serviceManager);

        // Camada de Visualização
        $view = (new ViewModel())->setTemplate('error/404');

        // Configurar Controladora
        $view->setVariable('controller', 'FooController');

        // Renderização
        $content = $renderer->render($view);
        // Apresentar Estrutura
        $document = new Document($content);
        // Capturar DOM
        $document->getDomDocument();

        // Validação
        $this->assertEmpty($document->getErrors(), 'Invalid Document Structure');
    }
}
