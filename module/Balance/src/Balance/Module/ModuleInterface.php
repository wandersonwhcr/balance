<?php

namespace Balance\Module;

/**
 * Interface para Criação de Módulos do Balance
 */
interface ModuleInterface
{
    /**
     * Apresentação de Identificador
     *
     * Valor que será utilizado como identificador único do módulo durante a execução do Balance. Este método pode
     * retornar o nome da classe do módulo. Todavia, não está fixo porque precisamos de métodos genéricos para melhoria
     * de encapsulamento e testes.
     *
     * @return string Valor Solicitado
     */
    public function getIdentifier();

    /**
     * Apresentação de Título
     *
     * Nome que será utilizado para exibição do módulo.
     *
     * @return string Valor Solicitado
     */
    public function getName();

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
