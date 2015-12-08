<?php

namespace Balance\Model;

use Balance\Form\Element\DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Form\FormElementManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class BalanceTest extends TestCase
{
    public function testGetFormSearch()
    {
        $model = new Balance();

        $formElementManager = new FormElementManager();
        $inputFilterManager = new InputFilterPluginManager();

        $serviceLocator = new ServiceManager();
        $serviceLocator
            ->setService('FormElementManager', $formElementManager)
            ->setService('InputFilterManager', $inputFilterManager);

        $model->setServiceLocator($serviceLocator);

        $formSearch = $model->getFormSearch();
        $this->assertInstanceOf('Balance\Form\Search\Balance', $formSearch);
        $this->assertSame($formSearch, $model->getFormSearch());
    }

    public function testFetch()
    {
        $model = new Balance();

        $formElementManager = new FormElementManager();
        $inputFilterManager = new InputFilterPluginManager();

        $formElementManager->setService('datetime', new DateTime());

        $serviceLocator = new ServiceManager();
        $serviceLocator
            ->setService('FormElementManager', $formElementManager)
            ->setService('InputFilterManager', $inputFilterManager);

        $model->setServiceLocator($serviceLocator);

        $data = array(
            array(
                'id'   => 1,
                'name' => 'one',
            ),
            array(
                'id'   => 2,
                'name' => 'two',
            ),
        );

        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $persistence
            ->expects($this->atLeastOnce())
            ->method('fetch')
            ->will($this->returnValue($data));
        $serviceLocator->setService('Balance\Model\Persistence\Balance', $persistence);

        $result = $model->fetch(new Parameters(array(
            'datetime' => '10/10/2010 10:10:10',
        )));

        $this->assertEquals($data, $result);
    }

    public function testFetchWithoutDatetime()
    {
        $model = new Balance();

        $formElementManager = new FormElementManager();
        $inputFilterManager = new InputFilterPluginManager();

        $formElementManager->setService('datetime', new DateTime());

        $serviceLocator = new ServiceManager();
        $serviceLocator
            ->setService('FormElementManager', $formElementManager)
            ->setService('InputFilterManager', $inputFilterManager);

        $model->setServiceLocator($serviceLocator);

        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $persistence
            ->expects($this->atLeastOnce())
            ->method('fetch')
            ->will($this->returnCallback(function ($params) {
                return isset($params['datetime']) ? array(array('one' => 'two')) : array();
            }));
        $serviceLocator->setService('Balance\Model\Persistence\Balance', $persistence);

        $result = $model->fetch(new Parameters());

        $this->assertEquals(array(array('one' => 'two')), $result);
    }
}
