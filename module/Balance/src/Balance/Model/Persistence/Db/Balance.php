<?php

namespace Balance\Model\Persistence\Db;

use ArrayIterator;
use ArrayObject;
use IntlDateFormatter;
use NumberFormatter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 */
class Balance implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Consultar o Balanço Completo
     *
     * @param  Parameters  $params Parâmetros de Execução
     * @return Traversable Conjunto de Valores Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Inicialização
        $db     = $this->getServiceLocator()->get('db');
        $result = [
            'ACTIVE'  => new ArrayIterator(),
            'PASSIVE' => new ArrayIterator(),
        ];

        // Data e Hora Limite
        $datetime = false;
        // Enviado?
        if ($params['datetime']) {
            // Formatador
            $formatter = new IntlDateFormatter(null, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);
            // Captura
            $datetime = date('c', $formatter->parse($params['datetime']));
        }

        // Formatador de Moedas
        $formatter = new NumberFormatter(null, NumberFormatter::CURRENCY);
        // Expressões
        $eValue = new Expression(
            'CASE'
            . ' WHEN'
            . '  "a"."type" = \'ACTIVE\' AND "e"."type" = \'CREDIT\''
            . '  OR "a"."type" = \'PASSIVE\' AND "e"."type" = \'DEBIT\''
            . ' THEN "e"."value" * -1'
            . ' ELSE "e"."value"'
            . ' END'
        );

        // Seletor de Balanço
        $balanceSelect = (new Select())
            ->from(['a' => 'accounts'])
            ->columns(['id', 'value' => $eValue])
            ->join(['e' => 'entries'], 'a.id = e.account_id', [])
            ->join(['p' => 'postings'], 'p.id = e.posting_id', []);
        // Captura
        $subselect = clone($balanceSelect);
        // Filtro de Não Acumulados
        $subselect->where(function ($where) {
            $where->equalTo('a.accumulate', 0);
        });
        // Filtro?
        if ($datetime) {
            // Aplicar Filtro de Data Limite
            $subselect->where(function ($where) use ($datetime) {
                $where->lessThanOrEqualTo('p.datetime', $datetime);
            });
        }
        // Seletor
        $select = (new Select())
            ->from(['b' => $subselect])
            ->columns(['value' => new Expression('SUM("b"."value")')])
            ->join(['a' => 'accounts'], 'a.id = b.id', ['type', 'id', 'name'])
            ->group(['a.id'])
            ->order(['a.type', 'a.position'])
            ->having(function ($where) {
                $where->notEqualTo(new Expression('SUM("b"."value")'), 0);
            });
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Processamento
        foreach ($rowset as $row) {
            // Tipagem
            $type = $row['type'];
            // Adicionar Entrada
            $result[$type]->append([
                'id'       => (int) $row['id'],
                'name'     => $row['name'],
                'value'    => (float) $row['value'],
                'currency' => $formatter->format($row['value']),
            ]);
        }

        // Seletor de Acumuladores
        $subselect = clone($balanceSelect);
        // Filtro para Acumulados
        $subselect->where(function ($where) {
            $where->equalTo('a.accumulate', 1);
        });
        // Seletor
        $select = (new Select())
            ->from(['b' => $subselect])
            ->columns(['value' => new Expression('SUM("b"."value")')]);
        // Consulta
        $value = (float) $db->query($select->getSqlString($db->getPlatform()))->execute()->current()['value'];
        // Captura
        $result = array_merge($result, [
            'ACCUMULATE' => new ArrayObject([
                'name'     => $value < 0 ? 'Prejuízo' : 'Lucro',
                'value'    => (float) $value,
                'currency' => $formatter->format($value),
            ]),
        ]);
        // Apresentação
        return new ArrayIterator($result);
    }
}
