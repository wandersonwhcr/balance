<?php

namespace Balance\Stdlib\Hydrator\Strategy;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
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
        // Adicionar Estratégia de Filtro
        $hydrator->addStrategy('datetime', new Datetime());
        // Apresentação
        return $hydrator;
    }

    public function testExtract()
    {
        // Inicialização
        $hydrator = $this->buildHydrator();
        $element  = new ArrayObject(array(
            'datetime' => '1999-10-31 23:59:59',
        ));
        // Dados para Expansão
        $element = $hydrator->extract($element);
        // Testes
        $this->assertEquals('31/10/1999 23:59:59', $element['datetime']);
    }

    public function testHydrate()
    {
        // Inicialização
        $hydrator = $this->buildHydrator();
        $element  = new ArrayObject(array());
        // Dados para Hidratação
        $hydrator->hydrate(array(
            'datetime' => '31/10/1999 23:59:59',
        ), $element);
        // Testes
        $this->assertEquals('1999-10-31 23:59:59', $element['datetime']);
    }
}
