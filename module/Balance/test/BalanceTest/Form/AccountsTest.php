<?php

namespace BalanceTest\Form;

use Balance\Form\Accounts;
use Balance\Form\Element\Boolean;
use Balance\Model\AccountType;
use PHPUnit_Framework_TestCase as TestCase;

class AccountsTest extends TestCase
{
    public function testInit()
    {
        $form = new Accounts();

        $form->getFormFactory()->getFormElementManager()
            ->setService('Boolean', new Boolean());

        $form->init();

        $this->assertTrue($form->has('id'));
        $this->assertInstanceOf('Zend\Form\Element\Hidden', $form->get('id'));

        $this->assertTrue($form->has('type'));
        $this->assertInstanceOf('Zend\Form\Element\Select', $form->get('type'));
        $this->assertEquals((new AccountType())->getDefinition(), $form->get('type')->getValueOptions());

        $this->assertTrue($form->has('accumulate'));
        $this->assertInstanceOf('Balance\Form\Element\Boolean', $form->get('accumulate'));

        $this->assertTrue($form->has('name'));
        $this->assertInstanceOf('Zend\Form\Element\Text', $form->get('name'));

        $this->assertTrue($form->has('description'));
        $this->assertInstanceOf('Zend\Form\Element\Textarea', $form->get('description'));
    }
}
