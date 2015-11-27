<?php

namespace Balance\Mvc\Controller;

/**
 * Configuração de Nome da Rota para Redirecionamento
 */
trait RedirectRouteNameAwareTrait
{
    /**
     * Nome da Rota para Redirecionamento
     * @type string
     */
    private $redirectRouteName;

    /**
     * Configuração do Nome da Rota para Redirecionamento
     *
     * @param  string                      $redirectRouteName Valor para Configuração
     * @return RedirectRouteNameAwareTrait Próprio Objeto para Encadeamento
     */
    public function setRedirectRouteName($redirectRouteName)
    {
        $this->redirectRouteName = $redirectRouteName;
        return $this;
    }

    /**
     * Apresentação do Nome da Rota para Redirecionamento
     *
     * @return string Valor Configurado
     */
    public function getRedirectRouteName()
    {
        return $this->redirectRouteName;
    }
}
