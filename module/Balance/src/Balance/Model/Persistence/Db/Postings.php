<?php

namespace Balance\Model\Persistence\Db;

use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Exception;
use NumberFormatter;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo de Banco de Dados para Lançamentos
 */
class Postings implements ServiceLocatorAwareInterface, PersistenceInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        // Resultado Inicial
        $result = array();
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
        // Pesquisa: Data e Hora Inicial
        if ($params['datetime_begin']) {
            // Filtrar Valor
            $datetime = date('Y-m-d H:i:s', strtotime($params['datetime_begin']));
            // Filtro
            $select->where(function ($where) use ($datetime) {
                $where->greaterThanOrEqualTo('p.datetime', $datetime);
            });
        }
        // Pesquisa: Data e Hora Final
        if ($params['datetime_end']) {
            // Filtrar Valor
            $datetime = date('Y-m-d H:i:s', strtotime($params['datetime_end']));
            // Filtro
            $select->where(function ($where) use ($datetime) {
                $where->lessThanOrEqualTo('p.datetime', $datetime);
            });
        }
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Captura
        foreach ($rowset as $row) {
            $result[] = array(
                'id'          => (int) $row['id'],
                'datetime'    => date('d/m/Y H:i:s', strtotime($row['datetime'])),
                'description' => $row['description'],
            );
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
        // Configurações
        $element = array(
            'id'          => (int) $row['id'],
            'datetime'    => date('d/m/Y H:i:s', strtotime($row['datetime'])),
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
        $datetime = date('Y-m-d H:i:s', strtotime($data['datetime']));

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
}
