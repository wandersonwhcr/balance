<?php

namespace BalanceTest\View\Template\Postings;

use Balance\Form\Element\Currency as CurrencyElement;
use Balance\Form\Postings as PostingsForm;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Dom\Query;
use Zend\Form\FormElementManager;
use Zend\Form\View\HelperConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\View;
use Zend\View\ViewEvent;

class EditTest extends TestCase
{
    protected function setUp()
    {
        $serviceManager = new ServiceManager();

        $serviceLocator = new FormElementManager();
        $serviceLocator->setServiceLocator($serviceManager);

        $pAccounts = $this->getMock('Balance\Model\Persistence\ValueOptionsInterface');
        $pAccounts->method('getValueOptions')->will($this->returnValue([]));
        $serviceManager->setService('Balance\Model\Persistence\Accounts', $pAccounts);

        $serviceLocator->setService('Currency', new CurrencyElement());

        $view = new View();

        $view->getEventManager()->attach(ViewEvent::EVENT_RENDERER, function () {
            $renderer = (new PhpRenderer())->setResolver(new TemplateMapResolver([
                // Páginas Básicas
                'layout/page-header'    => './module/Balance/view/layout/page-header.phtml',
                'balance/postings/edit' => './module/Balance/view/balance/postings/edit.phtml',
                // Páginas Internas
                'tests/postings/before-entries-collection-a'
                    => './module/Balance/test/BalanceTest/View/Scripts/postings-before-entries-collection-a.phtml',
                'tests/postings/before-entries-collection-b'
                    => './module/Balance/test/BalanceTest/View/Scripts/postings-before-entries-collection-b.phtml',
            ]));

            $helpers = $renderer->getHelperPluginManager();
            $helpers->get('BasePath')->setBasePath('/');

            $match = $this->getMockBuilder('Zend\Mvc\Router\RouteMatch')
                ->disableOriginalConstructor()
                ->getMock();
            $match->method('getMatchedRouteName')->will($this->returnValue('default'));

            $helpers->get('Url')
                ->setRouter($this->getMock('Zend\Mvc\Router\RouteStackInterface'))
                ->setRouteMatch($match);

            $configs = new HelperConfig();
            $configs->configureServiceManager($helpers);

            return $renderer;
        });

        $form = new PostingsForm();
        $form->setServiceLocator($serviceLocator);

        $model = (new ViewModel())
            ->setTemplate('balance/postings/edit')
            ->setVariable('form', $form)
            ->setOption('has_parent', true);

        $form->init();

        $this->view  = $view;
        $this->model = $model;
    }

    protected function tearDown()
    {
        unset($this->view);
        unset($this->model);
    }

    public function testRenderBeforeEntriesCollection()
    {
        $this->model->setVariable('beforeEntriesCollection', '<div id="before-entries-container">foobar</div>');

        $content = $this->view->render($this->model);

        $result = (new Query($content))->execute('#before-entries-container');

        $this->assertCount(1, $result);

        $element = $result->current();

        $this->assertEquals('foobar', $element->textContent);
    }

    public function testRenderBeforeEntriesCollectionWithAppend()
    {
        $submodelA = (new ViewModel())
            ->setTemplate('tests/postings/before-entries-collection-a')
            ->setAppend(true);

        $submodelB = (new ViewModel())
            ->setTemplate('tests/postings/before-entries-collection-b')
            ->setAppend(true);

        $this->model
            ->addChild($submodelA, 'beforeEntriesCollection')
            ->addChild($submodelB, 'beforeEntriesCollection');

        $content = $this->view->render($this->model);

        $result = (new Query($content))->execute('.before-entries-collection');
        $this->assertCount(2, $result);

        $element = $result->current();
        $this->assertEquals('foobar', $element->textContent);

        $result->next();

        $element = $result->current();
        $this->assertEquals('foobaz', $element->textContent);
    }
}
