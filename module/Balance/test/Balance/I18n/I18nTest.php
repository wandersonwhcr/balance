<?php

namespace Balance\I18n;

use PHPUnit_Framework_TestCase as TestCase;

class I18nTest extends TestCase
{
    public function testLocale()
    {
        $element = new I18n('pt_BR');

        $this->assertEquals('pt_BR', $element->getLocale());

        $result = $element->setLocale('en_US');
        $this->assertSame($result, $element);
        $this->assertEquals('en_US', $element->getLocale());
    }
}
