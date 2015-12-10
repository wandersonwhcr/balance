<?php

namespace Balance\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;

class SelectTest extends TestCase
{
    public function testEmptyOption()
    {
        $element = new Select();

        $emptyOption = $element->getEmptyOption();

        $this->assertEquals('-- Selecione --', $emptyOption);
    }
}
