<?php

namespace BalanceTest\Bug;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Dom\Query;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Uri as Page;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\Navigation as Helper;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class Issue182Test extends TestCase
{
    protected function setUp()
    {
        // Localizador de Serviços
        $serviceLocator = new ServiceManager();

        // Resolução de Scripts
        $resolver = new TemplateMapResolver([
            'error/404'          => './module/Balance/view/error/404.phtml',
            'error/500'          => './module/Balance/view/error/500.phtml',
            'layout/page-header' => './module/Balance/view/layout/page-header.phtml',
            'layout/navigation'  => './module/Balance/view/layout/navigation.phtml',
        ]);

        // Rederizador
        $renderer = (new PhpRenderer())->setResolver($resolver);

        // Camada de Visualização
        $view = new ViewModel();

        // Navegação
        $navigation = new Navigation();
        // Página Principal
        $page = new Page(['label' => 'Balance']);
        // Adicionar Página
        $navigation->addPage($page);
        // Configurar Serviço
        $serviceLocator->setService('navigation', $navigation);

        // Auxiliar de Navegação
        $helper = (new Helper())
            ->setContainer($navigation)
            ->setView($renderer);

        // Configurar Auxiliar como Plugin
        $renderer->getHelperPluginManager()
            ->setService('navigation', $helper)
            ->setServiceLocator($serviceLocator);

        // Configurações
        $this->renderer = $renderer;
        $this->view     = $view;
        $this->page     = $page;
    }

    protected function tearDown()
    {
        unset($this->renderer);
        unset($this->view);
        unset($this->page);
    }

    protected function getVisiblePagesText($content)
    {
        // Consulta DOM
        $elements = (new Query($content))->execute('#menu li a, #menu li span');

        // Capturar Visíveis
        $visible = [];
        foreach ($elements as $element) {
            if ($element->textContent) {
                $visible[] = utf8_decode(trim($element->textContent));
            }
        }

        // Apresentação
        return $visible;
    }

    public function test404Menu()
    {
        // Página não Encontrada
        $this->view->setTemplate('error/404');

        // Renderização
        $this->renderer->render($this->view);

        // Menu Superior
        $this->view->setTemplate('layout/navigation');

        // Renderização
        $content = $this->renderer->render($this->view);

        // Conteúdo Visível
        $visible = $this->getVisiblePagesText($content);

        // Verificações
        $this->assertNotContains('Página Desconhecida', $visible);
    }

    public function test500Menu()
    {
        // Página não Encontrada
        $this->view->setTemplate('error/500');

        // Renderização
        $this->renderer->render($this->view);

        // Menu Superior
        $this->view->setTemplate('layout/navigation');

        // Renderização
        $content = $this->renderer->render($this->view);

        // Conteúdo Visível
        $visible = $this->getVisiblePagesText($content);

        // Verificações
        $this->assertNotContains('Erro Encontrado', $visible);
    }

    public function testHideInvisible()
    {
        // Página Visível
        $subpage = new Page(['label' => 'A', 'visible' => true]);
        // Configurar Página Visível na Visível
        $subpage->addPage(new Page(['label' => 'AA', 'visible' => true]));
        // Configurar Página Invisível na Visível
        $subpage->addPage(new Page(['label' => 'AB', 'visible' => false]));
        // Configurações
        $this->page->addPage($subpage);

        // Página Invisível
        $subpage = new Page(['label' => 'B', 'visible' => false]);
        // Configurar Página Visível na Invisível
        $subpage->addPage(new Page(['label' => 'BA', 'visible' => true]));
        // Configurar Página Invisível na Invisível
        $subpage->addPage(new Page(['label' => 'BB', 'visible' => false]));
        // Configurações
        $this->page->addPage($subpage);

        // Camada de Visualização
        $this->view->setTemplate('layout/navigation');

        // Renderização
        $content = $this->renderer->render($this->view);

        // Conteúdo Visível
        $visible = $this->getVisiblePagesText($content);

        // Verificações
        $this->assertContains('A', $visible);
        $this->assertContains('AA', $visible);
        $this->assertNotContains('AB', $visible);
        $this->assertNotContains('B', $visible);
        $this->assertNotContains('BA', $visible);
        $this->assertNotContains('BB', $visible);
    }
}
