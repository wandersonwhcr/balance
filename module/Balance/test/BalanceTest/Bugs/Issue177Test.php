<?php

namespace BalanceTest\Bugs;

use BalanceTest\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class Issue177Test extends TestCase
{
    protected function renderTitle($template)
    {
        // Gerenciador de Serviços
        $serviceManager = Application::getApplication()->getServiceManager();

        // Resolução de Scripts
        $resolver = new TemplateMapResolver([
            'error/404'          => './module/Balance/view/error/404.phtml',
            'error/500'          => './module/Balance/view/error/500.phtml',
            'layout/page-header' => './module/Balance/view/layout/page-header.phtml',
        ]);

        // Renderização
        $renderer = (new PhpRenderer())->setResolver($resolver);

        // Configurar Serviço
        $renderer->getHelperPluginManager()->setServiceLocator($serviceManager);

        // Camada de Visualização
        $view = (new ViewModel())->setTemplate($template);

        // Renderização
        $renderer->render($view);

        // Capturar Título
        return $renderer->headTitle()->toString();
    }

    public function test404Title()
    {
        // Renderizar Título
        $result = $this->renderTitle('error/404');
        // Verificar
        $this->assertContains('Erro Encontrado', $result);
        $this->assertContains('Página Desconhecida', $result);
    }

    public function test500Title()
    {
        // Renderizar Título
        $result = $this->renderTitle('error/500');
        // Verificar
        $this->assertContains('Erro Encontrado', $result);
        $this->assertContains('Problema Interno', $result);
    }
}
