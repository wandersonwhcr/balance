<?php

namespace Balance\InputFilter;

use Balance\Model\AccountType;
use Balance\Model\BooleanType;
use PHPUnit_Framework_TestCase as TestCase;

class AccountsTest extends TestCase
{
    public function testInit()
    {
        $inputFilter = new Accounts();

        $inputFilter->init();

        $input = $inputFilter->get('id');
        $this->assertTrue($input->isRequired());
        $this->assertNotNull($input->setValue('')->getValue());
        $this->assertInternalType('int', $input->setValue('1')->getValue());

        $input = $inputFilter->get('type');
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->setValue(AccountType::ACTIVE)->isValid());
        $this->assertTrue($input->setValue(AccountType::PASSIVE)->isValid());
        $this->assertFalse($input->setValue('FOOBAR')->isValid());

        $input = $inputFilter->get('accumulate');
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->setValue(BooleanType::YES)->isValid());
        $this->assertTrue($input->setValue(BooleanType::NO)->isValid());
        $this->assertFalse($input->setValue('FOOBAR')->isValid());

        $input = $inputFilter->get('name');
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->setValue('FOOBAR')->isValid());
        $this->assertFalse($input->setValue('')->isValid());

        $input = $inputFilter->get('description');
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->setValue('FOOBAR')->isValid());
        $this->assertFalse($input->setValue('')->isValid());
    }
}
