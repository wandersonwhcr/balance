<?php

namespace Balance\Form\Element;

use Balance\Mvc\Application;
use NumberFormatter;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Form\FormElementManager;

class CurrencyTest extends TestCase
{
    public function testDefaultClassAttribute()
    {
        $element = new Currency();

        $value = $element->getAttribute('class');
        $this->assertEquals('form-control-currency', $value);

        $value = $element->setAttribute('class', 'foo-bar')->getAttribute('class');
        $this->assertEquals('form-control-currency foo-bar', $value);
    }

    public function testDefaultAddOnPrependOption()
    {
        $element = new Currency();

        $i18n = Application::getApplication()->getServiceManager()->get('i18n');

        $symbol = $i18n
            ->createNumberFormatter(NumberFormatter::CURRENCY)
            ->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        $formElementManager = new FormElementManager();
        $element->setServiceLocator($formElementManager);

        $serviceManager = new ServiceManager();
        $formElementManager->setServiceLocator($serviceManager);

        $serviceManager->setService('i18n', $i18n);

        $value = $element->getOption('add-on-prepend');
        $this->assertEquals($symbol, $value);
    }
}
