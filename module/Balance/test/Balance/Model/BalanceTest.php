<?php

namespace Balance\Model;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\ServiceManager;

class BalanceTest extends TestCase
{
    public function testGetFormSearch()
    {
        $model = new Balance();

        $formElementManager = new FormElementManager();

        $inputFilterManager = new InputFilterPluginManager();

        $serviceLocator = new ServiceManager();
        $serviceLocator
            ->setService('FormElementManager', $formElementManager)
            ->setService('InputFilterManager', $inputFilterManager);

        $model->setServiceLocator($serviceLocator);

        $formSearch = $model->getFormSearch();
        $this->assertInstanceOf('Balance\Form\Search\Balance', $formSearch);
        $this->assertSame($formSearch, $model->getFormSearch());
    }
}
