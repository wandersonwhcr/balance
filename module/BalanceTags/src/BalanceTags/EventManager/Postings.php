<?php

namespace BalanceTags\EventManager;

use Balance\Form\Search\Postings as PostingsFormSearch;
use Zend\EventManager\Event;
use Zend\Filter;
use Zend\InputFilter\Input;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Eventos para Lançamentos
 */
class Postings implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Configurar Formulário de Pesquisa de Lançamentos
     *
     * @param Event $event Evento Utilizado
     */
    public function setFormSearch(Event $event)
    {
        // Capturar Formulário
        $form = $event->getTarget();
        // Formulário na Tipagem Correta?
        if ($form instanceof PostingsFormSearch) {
            // Capturar Camada de Persistência de Etiquetas
            $pTags = $this->getServiceLocator()->get('BalanceTags\Model\Persistence\Tags');
            // Adicionar Campo
            $form->add([
                'type'    => 'Select',
                'name'    => 'tag_id',
                'options' => [
                    'label'         => 'Etiqueta',
                    'value_options' => $pTags->getValueOptions(),
                ],
            ]);
        }
    }

    /**
     * Configurar Filtro de Dados de Pesquisa de Lançamentos
     *
     * @param Event $event Evento Utilizado
     */
    public function setInputFilterSearch(Event $event)
    {
        // Capturar Formulário
        $form = $event->getTarget();
        // Formulário na Tipagem Correta?
        if ($form instanceof PostingsFormSearch) {
            // Capturar Filtro de Entrada de Dados
            $inputFilter = $form->getInputFilter();
            // Campo: Tags
            $input = (new Input())
                ->setRequired(false);
            $input->getFilterChain()
                ->attach(new Filter\ToInt())
                ->attach(new Filter\ToNull());
            $inputFilter->add($input, 'tag_id');
        }
    }
}
