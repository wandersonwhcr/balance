<?php

namespace BalanceTest\InputFilter\Search;

use Balance\InputFilter\Search\Balance;
use PHPUnit_Framework_TestCase as TestCase;

class BalanceTest extends TestCase
{
    public function testInit()
    {
        $inputFilter = new Balance();

        $inputFilter->init();

        $element = $inputFilter->get('datetime');
        $this->assertFalse($element->isRequired());
        $this->assertTrue($element->setValue('10/10/2010 10:10:10')->isValid());
        $this->assertFalse($element->setValue('10/10/2010')->isValid());
        $this->assertFalse($element->setValue('10:10:10')->isValid());
    }
}
