<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Exception;
use IntlDateFormatter;
use NumberFormatter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Paginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo de Banco de Dados para Lançamentos
 */
class Postings implements ServiceLocatorAwareInterface, PersistenceInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Apresentar um Formatador de Datas
     *
     * @return IntlDateFormatter Elemento Solicitado
     */
    protected function buildDateFormatter()
    {
        return new IntlDateFormatter('pt_BR', IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(array('p' => 'postings'))
            ->columns(array('id', 'datetime', 'description'));
        // Pesquisa: Palavras-Chave
        if ($params['keywords']) {
            $select->where(function ($where) use ($params) {
                $where->expression('"p"."description" ILIKE ?', '%' . $params['keywords'] . '%');
            });
        }
        // Conversão para Banco de Dados
        $formatter = $this->buildDateFormatter();
        // Pesquisa: Data e Hora Inicial
        if ($params['datetime_begin']) {
            // Filtrar Valor
            $datetime = date('c', $formatter->parse($params['datetime_begin']));
            // Filtro
            $select->where(function ($where) use ($datetime) {
                $where->greaterThanOrEqualTo('p.datetime', $datetime);
            });
        }
        // Pesquisa: Data e Hora Final
        if ($params['datetime_end']) {
            // Filtrar Valor
            $datetime = date('c', $formatter->parse($params['datetime_begin']));
            // Filtro
            $select->where(function ($where) use ($datetime) {
                $where->lessThanOrEqualTo('p.datetime', $datetime);
            });
        }
        // Paginação
        $result = new Paginator\Paginator(new Paginator\Adapter\DbSelect($select, $db));
        // Página?
        if ($params['page']) {
            // Configurar Página Atual
            $result->setCurrentPageNumber($params['page']);
        }
        // Apresentação
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Parameters $params)
    {
        // Chave Primária?
        if (! $params['id']) {
            throw new ModelException('Unknown Primary Key');
        }
        // Adaptador de Banco de Dados
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(array('p' => 'postings'))
            ->columns(array('id', 'datetime', 'description'))
            ->where(function ($where) use ($params) {
                $where->equalTo('p.id', (int) $params['id']);
            });
        // Consulta
        $row = $db->query($select->getSqlString($db->getPlatform()))->execute()->current();
        // Encontrado?
        if (! $row) {
            throw new ModelException('Unknown Element');
        }
        // Conversão para Banco de Dados
        $formatter = $this->buildDateFormatter();
        // Configurações
        $element = array(
            'id'          => (int) $row['id'],
            'datetime'    => $formatter->format(strtotime($row['datetime'])),
            'description' => $row['description'],
            'entries'     => array(),
        );
        // Carregar Entradas
        // Seletor
        $select = (new Select())
            ->from(array('e' => 'entries'))
            ->columns(array('type', 'account_id', 'value'))
            ->where(function ($where) use ($element) {
                $where->equalTo('e.posting_id', $element['id']);
            });
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Formatador de Números
        $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
        // Configuração de Símbolo
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
        // Configurações
        foreach ($rowset as $row) {
            $element['entries'][] = array(
                'type'       => $row['type'],
                'account_id' => $row['account_id'],
                'value'      => $formatter->format($row['value']),
            );
        }
        // Apresentação
        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Parameters $data)
    {
        // Inicialização
        $connection = $this->getServiceLocator()->get('db')->getDriver()->getConnection();
        $tbPostings = $this->getServiceLocator()->get('Balance\Db\TableGateway\Postings');
        $tbEntries  = $this->getServiceLocator()->get('Balance\Db\TableGateway\Entries');
        // Conversão para Banco de Dados
        $formatter = $this->buildDateFormatter();
        $datetime  = date('c', $formatter->parse($data['datetime']));

        // Tratamento
        try {
            // Transação
            $connection->beginTransaction();
            // Chave Primária?
            if ($data['id']) {
                // Atualizar Elemento
                $tbPostings->update(array(
                    'datetime'    => $datetime,
                    'description' => $data['description'],
                ), function ($where) use ($data) {
                    $where->equalTo('id', $data['id']);
                });
            } else {
                // Inserir Elemento
                $tbPostings->insert(array(
                    'datetime'    => $datetime,
                    'description' => $data['description'],
                ));
                // Chave Primária
                $data['id'] = (int) $tbPostings->getLastInsertValue();
            }
            // Remover Entradas
            $tbEntries->delete(function ($delete) use ($data) {
                $delete->where(function ($where) use ($data) {
                    $where->equalTo('posting_id', $data['id']);
                });
            });
            // Formatador de Números
            $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
            // Configuração de Símbolo
            $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
            // Salvar Entradas
            foreach ($data['entries'] as $subdata) {
                // Salvar Entradas
                $tbEntries->insert(array(
                    'posting_id' => $data['id'],
                    'account_id' => $subdata['account_id'],
                    'type'       => $subdata['type'],
                    'value'      => $formatter->parseCurrency($subdata['value'], $currency),
                ));
                // Limpeza PHPMD
                unset($currency);
            }
            // Finalização
            $connection->commit();
        } catch (Exception $e) {
            // Retorno
            $connection->rollback();
            // Apresentar Erro para Camada Superior
            throw new ModelException('Database Error', null, $e);
        }
        // Encadeamento
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Parameters $params)
    {
        // Chave Primária?
        if (! $params['id']) {
            throw new ModelException('Unknwon Primary Key');
        }
        // Inicialização
        $tbPostings = $this->getServiceLocator()->get('Balance\Db\TableGateway\Postings');
        // Remover Elemento
        $count = $tbPostings->delete(function ($delete) use ($params) {
            $delete->where(function ($where) use ($params) {
                $where->equalTo('id', $params['id']);
            });
        });
        // Sucesso?
        if ($count !== 1) {
            throw new ModelException('Unknown Element');
        }
        // Encadeamento
        return $this;
    }

    /**
     * Consultar o Balanço Completo
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Conjunto de Valores Encontrados
     */
    public function fetchBalance(Parameters $params)
    {
        // Inicialização
        $db     = $this->getServiceLocator()->get('db');
        $result = array(
            'ACTIVE'     => array(),
            'PASSIVE'    => array(),
            'ACCUMULATE' => array(),
        );
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
        // Formatador de Moedas
        $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
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
        // Captura
        return array(
            'ACTIVE'  => $result['ACTIVE'],
            'PASSIVE' => $result['PASSIVE'],
            'ACCUMULATE' => array(
                'name'  => 'Lucro',
                'value' => 'R$1200,00',
            ),
        );
    }
}
