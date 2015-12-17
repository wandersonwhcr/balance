<?php

namespace BalanceTest\Form\Search;

use Balance\Form\Search\Balance;
use PHPUnit_Framework_TestCase as TestCase;

class BalanceTest extends TestCase
{
    public function testInit()
    {
        $form = new Balance();

        $form->init();

        $this->assertTrue($form->has('datetime'));
        $this->assertInstanceOf('Zend\Form\Element\DateTime', $form->get('datetime'));
    }
}
