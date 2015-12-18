<?php

namespace BalanceTest\Form\Element;

use Balance\Form\Element\Currency;
use NumberFormatter;
use PHPUnit_Framework_TestCase as TestCase;

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

        $symbol = (new NumberFormatter(null, NumberFormatter::CURRENCY))
            ->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        $value = $element->getOption('add-on-prepend');
        $this->assertEquals($symbol, $value);
    }
}
