<?php

namespace Balance\I18n;

use IntlDateFormatter;
use NumberFormatter;
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

    public function testCreateNumberFormatter()
    {
        $element = new I18n('pt_BR');

        $formatter = $element->createNumberFormatter(NumberFormatter::DECIMAL);
        $this->assertInstanceOf('NumberFormatter', $formatter);

        $anotherFormatter = $element->createNumberFormatter(NumberFormatter::DECIMAL);
        $this->assertNotSame($formatter, $anotherFormatter);
    }

    public function testCreateDateFormatter()
    {
        $element = new I18n('pt_BR');

        $formatter = $element->createDateFormatter(IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
        $this->assertInstanceOf('IntlDateFormatter', $formatter);

        $anotherFormatter = $element->createDateFormatter(IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
        $this->assertNotSame($formatter, $anotherFormatter);
    }

    public function testLanguageLocale()
    {
        $element = new I18n('pt_BR');

        $this->assertEquals('pt-br', $element->getLanguageLocale());

        $element->setLocale('en_US');
        $this->assertEquals('en-us', $element->getLanguageLocale());

        $element->setLocale('en');
        $this->assertEquals('en', $element->getLanguageLocale());
    }
}
