<?php

namespace Balance\Model;

use IntlDateFormatter;
use Traversable;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
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
            $form = $this->getServiceLocator()->get('FormElementManager')
                ->get('Balance\Form\Search\Balance');
            // Filtro de Dados
            $inputFilter = $this->getServiceLocator()->get('InputFilterManager')
                ->get('Balance\InputFilter\Search\Balance');
            // Configuração
            $form->setInputFilter($inputFilter);
            // Configuração
            $this->formSearch = $form;
        }
        // Apresentação
        return $this->formSearch;
    }

    /**
     * Consultar Elementos
     *
     * @param  Parameters  $params Parâmetros de Execução
     * @return Traversable Conjunto de Valores Encontrados
     */
    public function fetch(Parameters $params)
    {
        // Formulário de Pesquisa
        $form = $this->getFormSearch();
        // Data Informada?
        if (! isset($params['datetime'])) {
            // Formatador de Data e Hora
            $formatter = new IntlDateFormatter(null, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
            // Colocar a Data Atual
            $params['datetime'] = $formatter->format(strtotime('first day of next month midnight -1 second'));
        }
        // Preencher Formulário
        $form->setData($params);
        // Validar Dados
        $form->isValid();
        // Reiniciar Parâmetros
        $params = new Parameters();
        // Capturar Valores Válidos
        foreach ($form->getInputFilter()->getValidInput() as $identifier => $input) {
            $params[$identifier] = $input->getValue();
        }
        // Consulta
        $result = $this->getServiceLocator()->get('Balance\Model\Persistence\Balance')->fetch($params);
        // Tipagem Correta?
        if (! $result instanceof Traversable) {
            // Erro Encontrado!
            throw new ModelException('Persistence Result is not Traversable');
        }
        // Resultado
        return $result;
    }
}
