<?php

namespace Balance\Model;

use Balance\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Stdlib\Parameters;

/**
 * Camada de Modelo para Balancete
 */
class Balance implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Formulário de Pesquisa
     * @type Balance\Form\Search\Balance
     */
    protected $formSearch;

    /**
     * Apresentação de Formulário de Pesquisa
     *
     * @return Balance\Form\Search\Balance Elemento Solicitado
     */
    public function getFormSearch()
    {
        // Inicializado?
        if (! $this->formSearch) {
            // Inicialização
            $this->formSearch = $this->getServiceLocator()->get('FormElementManager')
                ->get('Balance\Form\Search\Balance');
        }
        // Apresentação
        return $this->formSearch;
    }

    /**
     * Consultar Elementos
     *
     * @param  Parameters $params Parâmetros de Execução
     * @return array      Conjunto de Valores Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Formulário de Pesquisa
        $form = $this->getFormSearch();
        // Preencher Formulário
        $form->setData($params);
        // Consulta
        return $this->getServiceLocator()->get('Balance\Model\Persistence\Balance')->fetch($params);
    }
}
