<?php

namespace Balance\Model\Persistence\Db;

use ArrayObject;
use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Balance\Stdlib\Synchronizer;
use Exception;
use IntlDateFormatter;
use NumberFormatter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo de Banco de Dados para Lançamentos
 */
class Postings implements ServiceLocatorAwareInterface, PersistenceInterface
{
    use EventManagerAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * Apresentar um Formatador de Datas
     *
     * @return IntlDateFormatter Elemento Solicitado
     */
    protected function buildDateFormatter()
    {
        return new IntlDateFormatter(null, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
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
            ->from(['p' => 'postings'])
            ->columns(['id', 'datetime', 'description'])
            ->order(['p.datetime DESC']);
        // Pesquisa: Palavras-Chave
        if ($params['keywords']) {
            // Filtro
            $select->where(function ($where) use ($params) {
                // Linguagem
                $language = locale_get_display_language(null, 'en');
                // Documento
                $document = new Expression(
                    'TO_TSVECTOR(\'' . $language . '\', STRING_AGG("a"."name", \' \'))'
                    . ' || TO_TSVECTOR(\'' . $language . '\', STRING_AGG("p"."description", \' \'))'
                );
                // Construção do Documento
                $search = (new Select())
                    ->from(['p' => 'postings'])
                    ->columns(['posting_id' => 'id', 'document' => $document])
                    ->join(['e' => 'entries'], 'p.id = e.posting_id', [])
                    ->join(['a' => 'accounts'], 'a.id = e.account_id', [])
                    ->group(['p.id']);
                // Pesquisa Interna
                $subselect = (new Select())
                    ->from(['search' => $search])
                    ->columns(['posting_id'])
                    ->where(function ($where) use ($params, $language) {
                        $where->expression(
                            '"search"."document" @@ TO_TSQUERY(\'' . $language . '\', ?)',
                            sprintf("'%s'", addslashes($params['keywords']))
                        );
                    });
                // Aplicação do Filtro
                $where->in('p.id', $subselect);
            });
        }
        // Pesquisa: Conta
        if ($params['account_id']) {
            // Consulta Interna
            $subselect = (new Select())
                ->from(['e' => 'entries'])
                ->columns(['posting_id'])
                ->where(function ($where) use ($params) {
                    $where->equalTo('e.account_id', $params['account_id']);
                });
            // Aplicar Filtro
            $select->where(function ($where) use ($subselect) {
                $where->in('p.id', $subselect);
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
            $datetime = date('c', $formatter->parse($params['datetime_end']));
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
            ->from(['p' => 'postings'])
            ->columns(['id', 'datetime', 'description'])
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
        $element = [
            'id'          => (int) $row['id'],
            'datetime'    => $formatter->format(strtotime($row['datetime'])),
            'description' => $row['description'],
            'entries'     => [],
        ];
        // Carregar Entradas
        // Seletor
        $select = (new Select())
            ->from(['e' => 'entries'])
            ->columns(['type', 'account_id', 'value'])
            ->where(function ($where) use ($element) {
                $where->equalTo('e.posting_id', $element['id']);
            })
            ->order(['e.position']);
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Formatador de Números
        $formatter = new NumberFormatter(null, NumberFormatter::DECIMAL);
        // Configuração de Símbolo
        $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
        // Número de Casas Decimais
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
        // Configurações
        foreach ($rowset as $row) {
            $element['entries'][] = [
                'type'       => $row['type'],
                'account_id' => $row['account_id'],
                'value'      => $formatter->format($row['value']),
            ];
        }
        // Apresentação
        return new ArrayObject($element);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Parameters $data)
    {
        // Inicialização
        $db         = $this->getServiceLocator()->get('db');
        $connection = $db->getDriver()->getConnection();
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
                $count = $tbPostings->update([
                    'datetime'    => $datetime,
                    'description' => $data['description'],
                ], function ($where) use ($data) {
                    $where->equalTo('id', $data['id']);
                });
                // Verificações
                if ($count !== 1) {
                    throw new ModelException('Unknown Element');
                }
            } else {
                // Inserir Elemento
                $tbPostings->insert([
                    'datetime'    => $datetime,
                    'description' => $data['description'],
                ]);
                // Chave Primária
                $data['id'] = (int) $tbPostings->getLastInsertValue();
            }
            // Seletor Entradas
            $select = (new Select())
                ->from(['e' => 'entries'])
                ->columns(['account_id', 'position'])
                ->where(function ($where) use ($data) {
                    $where->equalTo('e.posting_id', $data['id']);
                });
            // Consulta de Entradas Antigas
            $oldEntries = [];
            $rowset     = $db->query($select->getSqlString($db->getPlatform()))->execute();
            foreach ($rowset as $row) {
                $oldEntries[] = $row;
            }
            // Captura de Novas Entradas
            $newEntries = $data['entries'];
            // Colocar Posições
            $position = 0;
            foreach (array_keys($newEntries) as $i) {
                $newEntries[$i]['position'] = $position++;
            }
            // Sincronização
            $entries = (new Synchronizer())
                ->setColumns(['account_id', 'position'])
                ->synchronize($oldEntries, $newEntries);
            // Processar Remoções
            foreach ($entries[Synchronizer::DELETE] as $subdata) {
                // Remover Elemento
                $tbEntries->delete(function ($delete) use ($data, $subdata) {
                    $delete->where(function ($where) use ($data, $subdata) {
                        $where
                            ->equalTo('posting_id', $data['id'])
                            ->equalTo('account_id', $subdata['account_id']);
                    });
                });
            }
            // Formatador de Números
            $formatter = new NumberFormatter(null, NumberFormatter::CURRENCY);
            // Configuração de Símbolo
            $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
            // Salvar Entradas
            foreach ($entries[Synchronizer::INSERT] as $subdata) {
                // Salvar Entradas
                $tbEntries->insert([
                    'posting_id' => $data['id'],
                    'account_id' => $subdata['account_id'],
                    'type'       => $subdata['type'],
                    'value'      => $formatter->parseCurrency($subdata['value'], $currency),
                    'position'   => $subdata['position'],
                ]);
                // Limpeza PHPMD
                unset($currency);
            }
            // Atualizar Entradas
            foreach ($entries[Synchronizer::UPDATE] as $subdata) {
                // Atualizar Entradas
                $tbEntries->update([
                    'posting_id' => $data['id'],
                    'account_id' => $subdata['account_id'],
                    'type'       => $subdata['type'],
                    'value'      => $formatter->parseCurrency($subdata['value'], $currency),
                    'position'   => $subdata['position'],
                ], function ($where) use ($data, $subdata) {
                    $where
                        ->equalTo('posting_id', $data['id'])
                        ->equalTo('account_id', $subdata['account_id']);
                });
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
            throw new ModelException('Unknown Primary Key');
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
