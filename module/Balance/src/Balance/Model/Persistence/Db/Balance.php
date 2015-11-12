<?php

namespace Balance\Model\Persistence\Db;

use Balance\ServiceManager\ServiceLocatorAwareTrait;
use NumberFormatter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 */
class Balance implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Consultar o Balanço Completo
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Conjunto de Valores Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Inicialização
        $db     = $this->getServiceLocator()->get('db');
        $result = array(
            'ACTIVE'  => array(),
            'PASSIVE' => array(),
        );
        // Formatador de Moedas
        $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
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
        $subselect = (new Select())
            ->from(array('a' => 'accounts'))
            ->columns(array('id', 'value' => $eValue))
            ->join(array('e' => 'entries'), 'a.id = e.account_id', array())
            ->where(function ($where) {
                $where->equalTo('a.accumulate', 0);
            });
        // Seletor
        $select = (new Select())
            ->from(array('b' => $subselect))
            ->columns(array('value' => new Expression('SUM("b"."value")')))
            ->join(array('a' => 'accounts'), 'a.id = b.id', array('type', 'id', 'name'))
            ->group(array('a.id'))
            ->order(array('a.type', 'a.position'));
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Processamento
        foreach ($rowset as $row) {
            // Tipagem
            $type = $row['type'];
            // Adicionar Entrada
            $result[$type][] = array(
                'id'    => (int) $row['id'],
                'name'  => $row['name'],
                'value' => $formatter->format($row['value']),
            );
        }

        // Seletor de Acumuladores
        $subselect = (new Select())
            ->from(array('a' => 'accounts'))
            ->columns(array('value' => $eValue))
            ->join(array('e' => 'entries'), 'a.id = e.account_id', array())
            ->where(function ($where) {
                $where->equalTo('a.accumulate', 1);
            });
        // Seletor
        $select = (new Select())
            ->from(array('b' => $subselect))
            ->columns(array('value' => new Expression('SUM("b"."value")')));
        // Consulta
        $value = (float) $db->query($select->getSqlString($db->getPlatform()))->execute()->current()['value'];
        // Captura
        return array_merge($result, array(
            'ACCUMULATE' => array(
                'name'  => $value < 0 ? 'Prejuízo' : 'Lucro',
                'value' => $formatter->format($value),
            ),
        ));
    }
}
