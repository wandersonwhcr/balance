<?php

namespace Balance\Mvc\Controller;

/**
 * Configuração de Nome da Rota para Redirecionamento
 */
interface RedirectRouteNameAwareInterface
{
    /**
     * Configuração do Nome da Rota para Redirecionamento
     *
     * @param  string                          $redirectRouteName Valor para Configuração
     * @return RedirectRouteNameAwareInterface Próprio Objeto para Encadeamento
     */
    public function setRedirectRouteName($redirectRouteName);

    /**
     * Apresentação do Nome da Rota para Redirecionamento
     *
     * @return string Valor Configurado
     */
    public function getRedirectRouteName();
}
