<?php

namespace Balance\Module;

/**
 * Interface para Criação de Módulos do Balance
 */
interface ModuleInterface
{
    /**
     * Apresentação do Identificador
     *
     * Representa o identificador único do módulo dentro do projeto de módulos do Balance.
     *
     * @return string Valor Solicitado
     */
    public function getIdentifier();

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
