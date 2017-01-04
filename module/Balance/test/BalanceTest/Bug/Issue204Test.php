<?php

namespace BalanceTest\Bug;

use ArrayIterator;
use Balance\Model\Model;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Form;
use Zend\Stdlib\Parameters;

class Issue204Test extends TestCase
{
    public function testFetchWithPage()
    {
        // Inicialização
        $persistence = $this->getMock('Balance\Model\Persistence\PersistenceInterface');
        $model       = new Model($persistence);
        $formSearch  = new Form();
        $params      = new Parameters(['page' => 1]);
        $result      = new ArrayIterator();

        // Configurações
        $model->setFormSearch($formSearch);

        // Verificações
        $persistence->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo($params))
            ->will($this->returnValue($result));

        // Execução
        $model->fetch($params);
    }
}
