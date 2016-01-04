<?php

namespace BalanceTags\EventManager;

use Balance\Form\Postings as PostingsForm;
use Balance\Form\Search\Postings as PostingsFormSearch;
use Zend\EventManager\Event;
use Zend\Filter;
use Zend\InputFilter\Input;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Validator;
use Zend\View\Model\ViewModel;

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
            // Prioridade do Campo
            $form->setPriority('tag_id', 101);
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

    /**
     * Configurar Formulário de Lançamentos
     *
     * @param Event $event Evento Utilizado
     */
    public function setForm(Event $event)
    {
        // Capturar Formulário
        $form = $event->getTarget();
        // Formulário na Tipagem Correta?
        if ($form instanceof PostingsForm) {
            // Capturar Camada de Persistência de Etiquetas
            $pTags = $this->getServiceLocator()->get('BalanceTags\Model\Persistence\Tags');
            // Adicionar Campo de Etiquetas
            $form->add([
                'type'    => 'MultiCheckbox',
                'name'    => 'tags',
                'options' => [
                    'value_options' => $pTags->getValueOptions(),
                ],
            ]);
        }
    }

    /**
     * Configurar Filtro de Dados de Lançamentos
     *
     * @param Event $event Evento Utilizado
     */
    public function setInputFilter(Event $event)
    {
        // Capturar Formulário
        $form = $event->getTarget();
        // Formulário na Tipagem Correta?
        if ($form instanceof PostingsForm) {
            // Capturar Camada de Persistência de Etiquetas
            $pTags = $this->getServiceLocator()->get('BalanceTags\Model\Persistence\Tags');
            // Capturar Filtro de Entrada de Dados
            $inputFilter = $form->getInputFilter();
            // Campo: Tags
            $input = (new Input())
                ->setRequired(false);
            // Filtros
            $input->getFilterChain()
                ->attach(new Filter\ToInt());
            // Configuração
            $inputFilter->add($input, 'tags');
        }
    }

    /**
     * Configurar Camada de Visualização para Incluir Campo de Etiquetas no Formulário de Lançamentos
     *
     * @param Event $event Evento Utilizado
     */
    public function setViewModel(Event $event)
    {
        // Capturar Visualização
        $viewModel = $event->getTarget();
        // Formulário
        $form = $viewModel->getVariable('form');
        // Tipagem Correta?
        if ($form instanceof PostingsForm) {
            // Novo Modelo de Visualização
            $subViewModel = (new ViewModel())
                ->setAppend(true)
                ->setVariable('form', $form)
                ->setTemplate('balance-tags/postings/edit-before-entries');
            // Adicionar Subcamada
            $viewModel->addChild($subViewModel, 'beforeEntries');
        }
    }
}
