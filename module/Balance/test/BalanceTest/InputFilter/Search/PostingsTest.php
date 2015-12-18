<?php

namespace BalanceTest\InputFilter\Search;

use Balance\InputFilter\Search\Postings;
use PHPUnit_Framework_TestCase as TestCase;

class PostingsTest extends TestCase
{
    public function testInit()
    {
        $inputFilter = new Postings();

        $inputFilter->init();

        $element = $inputFilter->get('keywords');
        $this->assertFalse($element->isRequired());
        $this->assertTrue($element->setValue('FOOBAR')->isValid());

        $element = $inputFilter->get('account_id');
        $this->assertFalse($element->isRequired());
        $this->assertNull($element->setValue('')->getValue());
        $this->assertInternalType('int', $element->setValue('1')->getValue());

        $element = $inputFilter->get('datetime_begin');
        $this->assertFalse($element->isRequired());
        $this->assertTrue($element->setValue('10/10/2010 10:10:10')->isValid());
        $this->assertFalse($element->setValue('10/10/2010')->isValid());
        $this->assertFalse($element->setValue('10:10:10')->isValid());

        $element = $inputFilter->get('datetime_end');
        $this->assertFalse($element->isRequired());
        $this->assertTrue($element->setValue('10/10/2010 10:10:10')->isValid());
        $this->assertFalse($element->setValue('10/10/2010')->isValid());
        $this->assertFalse($element->setValue('10:10:10')->isValid());
    }
}
