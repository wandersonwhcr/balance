<?php

namespace BalanceTest\Docs;

use PHPUnit_Framework_TestCase as TestCase;

class LicenseTest extends TestCase
{
    protected function getYear()
    {
        // Abrir Arquivo
        $resource = fopen(__DIR__ . '/../../../../../LICENSE', 'r');
        // Capturar Primeira Linha do Arquivo
        $firstLine = fgets($resource);
        // Fechar Arquivo
        fclose($resource);

        // Captura
        preg_match('/^Copyright \(c\) (?<year>[0-9]+)/', $firstLine, $match);

        // Apresentação
        return (isset($match['year']) ? $match['year'] : null);
    }

    public function testYear()
    {
        // Verificação
        $this->assertSame(date('Y'), $this->getYear());
    }
}
