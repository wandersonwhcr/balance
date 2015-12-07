<?php

namespace Balance\InputFilter\Search;

use Balance\Model\AccountType;
use PHPUnit_Framework_TestCase as TestCase;

class AccountsTest extends TestCase
{
    public function testInit()
    {
        $inputFilter = new Accounts();

        $inputFilter->init();

        $element = $inputFilter->get('type');
        $this->assertFalse($element->isRequired());
        $this->assertTrue($element->setValue(AccountType::ACTIVE)->isValid());
        $this->assertTrue($element->setValue(AccountType::PASSIVE)->isValid());
        $this->assertFalse($element->setValue('UNKNOWN')->isValid());

        $element = $inputFilter->get('keywords');
        $this->assertFalse($element->isRequired());
        $this->assertTrue($element->setValue('FOOBAR')->isValid());
    }
}
