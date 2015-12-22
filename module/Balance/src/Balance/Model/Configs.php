<?php

namespace Balance\Model;

use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo para Configurações
 */
class Configs
{
    /**
     * Consulta de Módulos
     *
     * Responsável pela consulta de módulos disponíveis, estejam eles instalados ou não. Apresenta o identificador do
     * módulo, título, descrição, um estado informando se está instalado ou não e outro se ele é padrão do sistema. As
     * informações dos módulos são retornadas na ordem em que devem ser apresentados. Ainda existe a possibilidade de
     * filtrar módulos instalados ou não instalados.
     *
     * @param  Parameters    $params Parâmetros de Consulta
     * @return ArrayIterator Conjunto de Elementos Encontrados
     */
    public function fetchModules(Parameters $params)
    {
        return [
            [
                'identifier'  => 'Balance',
                'title'       => 'Módulo Padrão',
                'description' => 'Este módulo representa todos os recursos básicos do Balance, incluindo o gerenciamento de contas e lançamentos, bem como o cálculo do balance na página principal do sistema.',
                'core'        => true,
                'installed'   => true,
            ],
            [
                'identifier'  => 'BalanceReports',
                'title'       => 'Relatórios',
                'description' => 'Módulo responsável pela apresentação de relatórios, utilizando os dados cadastrados no módulo básico do Balance. Informando uma conta na filtragem, podemos visualizar uma listagem com valores e tipos de entradas de lançamentos e sua descrição.',
                'core'        => false,
                'installed'   => false,
            ],
            [
                'identifier'  => 'BalanceTags',
                'title'       => 'Etiquetas',
                'description' => 'Etiquetas identificam e catalogam lançamentos. Estes poderão receber múltiplas etiquetas, representando assim a sua finalidade de lançamento. Com isto, podemos utilizar a consulta de lançamentos, filtrando as informações conforme as etiquetas utilizadas.',
                'core'        => false,
                'installed'   => false,
            ],
        ];
    }
}
