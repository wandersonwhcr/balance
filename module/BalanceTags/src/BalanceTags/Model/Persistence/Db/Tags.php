<?php

namespace BalanceTags\Model\Persistence\Db;

use ArrayIterator;
use ArrayObject;
use Balance\Model\ModelException;
use Balance\Model\Persistence\PersistenceInterface;
use Exception as BaseException;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Parameters;

/**
 * Camada de Persistência de Etiquetas
 */
class Tags implements ServiceLocatorAwareInterface, PersistenceInterface
{
    use EventManagerAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameters $params)
    {
        // Resultado Inicial
        $result = [];
        // Inicialização
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(['t' => 'tags'])
            ->columns(['id', 'name'])
            ->order('name');
        // Pesquisa: Palavras-Chave
        if ($params['keywords']) {
            // Filtro
            $select->where(function ($where) use ($params) {
                // Idioma
                $language = locale_get_display_language(null, 'en');
                // Documento
                $document = new Expression('TO_TSVECTOR(\'' . $language . '\', "t"."name")');
                // Construção de Documento
                $search = (new Select())
                    ->from(['t' => 'tags'])
                    ->columns(['tag_id' => 'id', 'document' => $document]);
                // Pesquisa Interna
                $subselect = (new Select())
                    ->from(['search' => $search])
                    ->columns(['tag_id'])
                    ->where(function ($where) use ($params, $language) {
                        $where->expression(
                            '"search"."document" @@ TO_TSQUERY(\'' . $language . '\', ?)',
                            sprintf("'%s'", addslashes($params['keywords']))
                        );
                    });
                // Aplicação do Filtro
                $where->in('t.id', $subselect);
            });
        }
        // Consulta
        $rowset = $db->query($select->getSqlString($db->getPlatform()))->execute();
        // Captura
        foreach ($rowset as $row) {
            $result[] = [
                'id'   => (int) $row['id'],
                'name' => $row['name'],
            ];
        }
        // Apresentação
        return new ArrayIterator($result);
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
        // Inicialização
        $db = $this->getServiceLocator()->get('db');
        // Seletor
        $select = (new Select())
            ->from(['t' => 'tags'])
            ->columns(['id', 'name'])
            ->where(function ($where) use ($params) {
                $where->equalTo('t.id', $params['id']);
            });
        // Consulta
        $row = $db->query($select->getSqlString($db->getPlatform()))->execute()->current();
        // Encontrado?
        if (! $row) {
            throw new ModelException('Unknown Element');
        }
        // Configurações
        $element = [
            'id'   => (int) $row['id'],
            'name' => $row['name'],
        ];
        // Apresentação
        return new ArrayObject($element);
    }

    /**
     * {@inheritdoc}
     */
    public function save(Parameters $data)
    {
        // Inicialização
        $tbTags     = $this->getServiceLocator()->get('BalanceTags\Db\TableGateway\Tags');
        $db         = $this->getServiceLocator()->get('db');
        $connection = $db->getDriver()->getConnection();
        // Tratamento
        try {
            // Transação
            $connection->beginTransaction();
            // Chave Primária?
            if ($data['id']) {
                // Atualizar Informação
                $tbTags->update([
                    'name' => $data['name'],
                ], function ($where) use ($data) {
                    $where->equalTo('id', $data['id']);
                });
            } else {
                // Inserir Informação
                $tbTags->insert([
                    'name' => $data['name'],
                ]);
                // Chave Primária
                $data['id'] = (int) $tbTags->getLastInsertValue();
            }
            // Finalização
            $connection->commit();
        } catch (BaseException $e) {
            // Retorno
            $connection->rollback();
            // Apresentar Erro
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
        $tbTags     = $this->getServiceLocator()->get('BalanceTags\Db\TableGateway\Tags');
        $db         = $this->getServiceLocator()->get('db');
        $connection = $db->getDriver()->getConnection();
        // Tratamento
        try {
            // Transação
            $connection->beginTransaction();
            // Remover Elemento
            $tbTags->delete(function ($delete) use ($params) {
                $delete->where(function ($where) use ($params) {
                    $where->equalTo('id', $params['id']);
                });
            });
            // Finalização
            $connection->commit();
        } catch (BaseException $e) {
            // Retorno
            $connection->rollback();
            // Apresentar Erro
            throw new ModelException('Database Error', null, $e);
        }
        // Encadeamento
        return $this;
    }
}
