<?php

namespace Balance\Model;

use DateInterval;
use DateTime;
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
     * Data e Hora
     * @type DateTime
     */
    protected $dateTime;

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
     * Configura a Data e Hora
     *
     * @param  DateTime $dateTime Elemento para Configuração
     * @return self     Próprio Objeto para Encadeamento
     */
    public function setDateTime(DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * Apresenta a Data e Hora
     *
     * @return DateTime Elemento Configurado
     */
    public function getDateTime()
    {
        // Configurado?
        if (! $this->dateTime) {
            // Inicialização
            $dateTime = new DateTime('first day of next month');
            // Meia Noite - 1 Segundo
            $dateTime->setTime(0, 0, 0)->sub(new DateInterval('PT1S'));
            // Inicialização
            $this->setDateTime($dateTime);
        }
        // Apresentação
        return $this->dateTime;
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
            $formatter = new IntlDateFormatter(null, IntlDateFormatter::SHORT, IntlDateFormatter::MEDIUM);
            // Colocar a Data Atual
            $params['datetime'] = $formatter->format($this->getDateTime()->getTimestamp());
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
