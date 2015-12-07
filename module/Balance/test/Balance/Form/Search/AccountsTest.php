<?php

namespace Balance\Form\Search;

use Balance\Model\AccountType;
use PHPUnit_Framework_TestCase as TestCase;

class AccountsTest extends TestCase
{
    public function testInit()
    {
        $form = new Accounts();

        $form->init();

        $this->assertTrue($form->has('type'));
        $this->assertInstanceOf('Zend\Form\Element\Select', $form->get('type'));
        $this->assertEquals((new AccountType())->getDefinition(), $form->get('type')->getValueOptions());

        $this->assertTrue($form->has('keywords'));
        $this->assertInstanceOf('Zend\Form\Element\Text', $form->get('keywords'));
    }
}
