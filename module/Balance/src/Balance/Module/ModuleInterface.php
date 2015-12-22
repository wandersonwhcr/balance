<?php

namespace Balance\Module;

/**
 * Interface para Criação de Módulos do Balance
 */
interface ModuleInterface
{
    /**
     * Apresentação de Título
     *
     * Nome que será utilizado para exibição do módulo.
     *
     * @return string Valor Solicitado
     */
    public function getTitle();

    /**
     * Apresentação da Descrição do Módulo
     *
     * A descrição do módulo é utilizada para apresentar ao usuário as funcionalidades e recursos que o módulo irá
     * adicionar no sistema caso ele seja instalado.
     *
     * @return string Valor Solicitado
     */
    public function getDescription();
}
