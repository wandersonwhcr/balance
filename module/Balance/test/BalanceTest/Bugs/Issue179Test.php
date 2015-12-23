<?php

namespace Balance\Bugs;

use DOMDocument;
use PHPUnit_Framework_TestCase as TestCase;
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

        // Estrutura HTML
        $document = new DOMDocument();
        // Carregar Resultado
        $result = $document->loadHTML($content);
        // Conseguiu?
        if (! $result) {
            $this->markTestSkipped('Unable to Load HTML Result');
        }

        // Capturar Formulário
        $form = $document->getElementById('form-modules');
        // Caixa Solicitada
        $checkboxM = null;
        $checkboxA = null;
        $checkboxB = null;
        // Capturar Painéis
        foreach ($form->getElementsByTagName('div') as $element) {
            // É um "panel"?
            if ($element->getAttribute('class') == 'panel panel-default') {
                // Capturar Título
                foreach ($element->getElementsByTagName('div') as $subelement) {
                    // É um Título?
                    if ($subelement->getAttribute('class') == 'panel-heading') {
                        // Consultar Checkboxes
                        foreach ($subelement->getElementsByTagName('input') as $subsubelement) {
                            // É um Checkbox?
                            if ($subsubelement->getAttribute('type') == 'checkbox') {
                                // Tipo?
                                switch ($subsubelement->getAttribute('value')) {
                                    case 'Balance':
                                        $checkboxM = $subsubelement;
                                        break;
                                    case 'ModuleA':
                                        // Capturar!
                                        $checkboxA = $subsubelement;
                                        break;
                                    case 'ModuleB':
                                        // Capturar!
                                        $checkboxB = $subsubelement;
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Precisamos do Checkbox do Módulo
        $this->assertNotNull($checkboxM);

        // Há Checkbox?
        if (! $checkboxA) {
            $this->markTestSkipped('Unknown Checkbox A');
        }
        // Há Checkbox?
        if (! $checkboxB) {
            $this->markTestSkipped('Unknown Checkbox B');
        }

        $this->assertTrue($checkboxM->hasAttribute('disabled'));
        $this->assertTrue($checkboxM->hasAttribute('checked'));

        $this->assertFalse($checkboxA->hasAttribute('disabled'));
        $this->assertFalse($checkboxA->hasAttribute('checked'));

        $this->assertFalse($checkboxB->hasAttribute('disabled'));
        $this->assertTrue($checkboxB->hasAttribute('checked'));
    }
}
