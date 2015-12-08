<?php

namespace Balance\InputFilter;

use Balance\Model\EntryType;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\ServiceManager;

class PostingsTest extends TestCase
{
    public function testInit()
    {
        $inputFilter = new Postings();

        $serviceLocator = new ServiceManager();

        $persistence = $this->getMock('Balance\Model\Persistence\ValueOptionsInterface');
        $persistence
            ->expects($this->atLeastOnce())
            ->method('getValueOptions')
            ->will($this->returnValue(array(1 => 'One', 2 => 'Two')));
        $serviceLocator->setService('Balance\Model\Persistence\Accounts', $persistence);

        $inputFilterPluginManager = new InputFilterPluginManager();
        $inputFilterPluginManager->setServiceLocator($serviceLocator);
        $inputFilter->setServiceLocator($inputFilterPluginManager);

        $inputFilter->init();

        $input = $inputFilter->get('id');
        $this->assertTrue($input->isRequired());
        $this->assertNull($input->setValue('')->getValue());
        $this->assertInternalType('int', $input->setValue('1')->getValue());

        $element = $inputFilter->get('datetime');
        $this->assertTrue($element->isRequired());
        $this->assertTrue($element->setValue('10/10/2010 10:10:10')->isValid());
        $this->assertFalse($element->setValue('10/10/2010')->isValid());
        $this->assertFalse($element->setValue('10:10:10')->isValid());

        $input = $inputFilter->get('description');
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->setValue('FOOBAR')->isValid());
        $this->assertFalse($input->setValue('')->isValid());

        $collection = $inputFilter->get('entries');
        $this->assertEquals(2, $collection->getCount());

        $subInputFilter = $collection->getInputFilter();

        $input = $subInputFilter->get('type');
        $this->assertTrue($input->isRequired());
        $this->assertTrue($input->setValue(EntryType::CREDIT)->isValid());
        $this->assertTrue($input->setValue(EntryType::DEBIT)->isValid());
        $this->assertFalse($input->setValue('FOOBAR')->isValid());

        $input = $subInputFilter->get('account_id');
        $this->assertTrue($input->isRequired());

        $input = $subInputFilter->get('value');
        $this->assertTrue($input->isRequired());
    }
}
