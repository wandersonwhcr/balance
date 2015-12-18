<?php

namespace BalanceTest\Stdlib\Hydrator\Strategy;

use ArrayObject;
use Balance\Stdlib\Hydrator\Strategy\Datetime;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator;

/**
 * Testes em Estratégia de Hidratação de Datas
 */
class DatetimeTest extends TestCase
{
    protected function buildHydrator()
    {
        // Inicialização
        $hydrator = new Hydrator\ArraySerializable();
        $datetime = new Datetime();
        // Internacionalização
        $serviceLocator = new ServiceManager();
        $datetime->setServiceLocator($serviceLocator);
        // Adicionar Estratégia de Filtro
        $hydrator->addStrategy('datetime', $datetime);
        // Apresentação
        return $hydrator;
    }

    public function testExtract()
    {
        // Inicialização
        $hydrator = $this->buildHydrator();
        $element  = new ArrayObject([
            'datetime' => '1999-10-31 23:59:59',
        ]);
        // Dados para Expansão
        $element = $hydrator->extract($element);
        // Testes
        $this->assertEquals('31/10/1999 23:59:59', $element['datetime']);
    }

    public function testHydrate()
    {
        // Inicialização
        $hydrator = $this->buildHydrator();
        $element  = new ArrayObject();
        // Dados para Hidratação
        $hydrator->hydrate([
            'datetime' => '31/10/1999 23:59:59',
        ], $element);
        // Testes
        $this->assertEquals('1999-10-31T23:59:59-02:00', $element['datetime']);
    }
}
