<?php

namespace Balance\Form\Element;

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

        $symbol = (new NumberFormatter('pt_BR', NumberFormatter::CURRENCY))
            ->getSymbol(NumberFormatter::CURRENCY_SYMBOL);

        $value = $element->getOption('add-on-prepend');
        $this->assertEquals($symbol, $value);
    }
}
