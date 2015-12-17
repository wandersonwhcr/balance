<?php

namespace Balance\Model;

use ArrayIterator;
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

        $data = new ArrayIterator([
            [
                'id'   => 1,
                'name' => 'one',
            ],
            [
                'id'   => 2,
                'name' => 'two',
            ],
        ]);

        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $persistence
            ->expects($this->atLeastOnce())
            ->method('fetch')
            ->will($this->returnValue($data));
        $serviceLocator->setService('Balance\Model\Persistence\Balance', $persistence);

        $result = $model->fetch(new Parameters([
            'datetime' => '10/10/2010 10:10:10',
        ]));

        $this->assertSame($data, $result);
    }

    public function testFetchWithoutTraversable()
    {
        $this->setExpectedException('Balance\Model\ModelException', 'Persistence Result is not Traversable');

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
            ->will($this->returnValue([]));
        $serviceLocator->setService('Balance\Model\Persistence\Balance', $persistence);

        $model->fetch(new Parameters());
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

        $data = new ArrayIterator([['one' => 'two']]);

        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $persistence
            ->expects($this->atLeastOnce())
            ->method('fetch')
            ->will($this->returnCallback(function ($params) use ($data) {
                return isset($params['datetime']) ? $data : new ArrayIterator();
            }));
        $serviceLocator->setService('Balance\Model\Persistence\Balance', $persistence);

        $result = $model->fetch(new Parameters());

        $this->assertSame($data, $result);
    }
}
