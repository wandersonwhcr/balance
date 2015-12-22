<?php

namespace Balance\Model\Persistence\File;

use ArrayIterator;
use Zend\Stdlib\Parameters;

/**
 * Camada de Persistência para Módu
 */
class Modules
{
    /**
     * Apresentação de Elementos
     *
     * Captura informações do projeto, verificando quais os módulos que estão disponíveis e apresentando informações
     * sobre o mesmo. Módulos instalados e não instalados podem ser filtrados, bem como módulos pertencentes ao sistema.
     *
     * @param  Parameters  $params Parâmetros de Execução
     * @return Traversable Conjunto de Elementos Solicitados
     */
    public function fetch(Parameters $params)
    {
        // Resultado Inicial
        $result = [
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
        // Apresentação
        return new ArrayIterator($result);
    }
}
