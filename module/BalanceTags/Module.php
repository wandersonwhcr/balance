<?php

namespace BalanceTags;

use Balance\Module\ModuleInterface;

/**
 * Módulo de Tags
 *
 * Possibilita o relacionamento de lançamentos a etiquetas previamente cadastradas. Isto facilita algumas pesquisas,
 * adicionando fatores de pesquisa e agrupamentos.
 */
class Module implements ModuleInterface
{
    /**
     * Configurações do Módulo
     *
     * @return array Valores Solicitados
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'BalanceTags';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Etiquetas';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return <<<DESCRIPTION
Adiciona a possibilidade de relacionamento de lançamentos com etiquetas previamente cadastradas, facilitando pesquisas e
agrupamentos de informações. A pesquisa de lançamentos também recebe um filtro de etiquetas, aumentando a possibilidade
de pesquisas.
DESCRIPTION;
    }

    /**
     * {@inheritdoc}
     */
    public function onBootstrap($event)
    {
        // Gerenciador de Eventos Compartilhado
        $serviceLocator = $event->getApplication()->getServiceManager();
        $eventManager   = $serviceLocator->get('EventManager')->getSharedManager();

        // Evento: Configurar Formulário de Pesquisa de Lançamentos
        $eventManager->attach('*', 'Balance\Model\Model::setFormSearch', function ($event) use ($serviceLocator) {
            // Capturar Gerenciador de Eventos para Lançamentos
            $emPostings = $serviceLocator->get('BalanceTags\EventManager\Postings');
            // Executar Evento
            $emPostings->setFormSearch($event);
        });

        // Evento: Configurar Filtro de Dados para Formulário de Pesquisa de Lançamento
        $eventManager->attach('*', 'Balance\Model\Model::setFormSearch', function ($event) use ($serviceLocator) {
            // Capturar Gerenciador de Eventos para Lançamentos
            $emPostings = $serviceLocator->get('BalanceTags\EventManager\Postings');
            // Executar Evento
            $emPostings->setInputFilterSearch($event);
        });
    }
}
