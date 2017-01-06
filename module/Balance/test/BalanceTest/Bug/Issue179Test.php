<?php

namespace Balance\Bug;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Dom\Query;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;

class Issue179Test extends TestCase
{
    public function testCheckbox()
    {
        // Resolução de Scripts
        $resolver = new TemplateMapResolver([
            'balance/configs/modules' => './module/Balance/view/balance/configs/modules.phtml',
            'layout/page-header' => './module/Balance/view/layout/page-header.phtml',
        ]);

        // Renderização
        $renderer = (new PhpRenderer())->setResolver($resolver);

        // Dependências de Plugins
        $manager = $renderer->getHelperPluginManager();

        // Caminho Base
        $manager->get('BasePath')->setBasePath('/');

        // URL
        $match = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
            ->disableOriginalConstructor()
            ->getMock();
        $match->method('getMatchedRouteName')->will($this->returnValue('default'));
        // Configurações
        $manager->get('Url')
            ->setRouter($this->getMock('Zend\Mvc\Router\RouteStackInterface'))
            ->setRouteMatch($match);

        // Camada de Visualização
        $view = (new ViewModel())->setTemplate('balance/configs/modules');

        // Variáveis
        $view->setVariable('elements', [
            [
                'name'        => 'Testing Module A',
                'identifier'  => 'ModuleA',
                'enabled'     => false,
                'description' => 'Description of Module A',
            ],
            [
                'name'        => 'Testing Module B',
                'identifier'  => 'ModuleB',
                'enabled'     => true,
                'description' => 'Description of Module B',
            ],
        ]);

        // Renderização
        $content = $renderer->render($view);

        // Execução
        $query = new Query($content);
        // Captura
        $elements = $query->execute('#form-modules .panel .panel-heading input[type="checkbox"]');

        // Verificações
        $this->assertCount(3, $elements);

        $element = $elements->current();
        $this->assertEquals('Balance', $element->getAttribute('value'));
        $this->assertTrue($element->hasAttribute('checked'));
        $this->assertTrue($element->hasAttribute('disabled'));

        $elements->next();
        $element = $elements->current();
        $this->assertEquals('ModuleA', $element->getAttribute('value'));
        $this->assertTrue($element->hasAttribute('checked'));
        $this->assertTrue($element->hasAttribute('disabled'));

        $elements->next();
        $element = $elements->current();
        $this->assertEquals('ModuleB', $element->getAttribute('value'));
        $this->assertTrue($element->hasAttribute('checked'));
        $this->assertTrue($element->hasAttribute('disabled'));
    }
}
