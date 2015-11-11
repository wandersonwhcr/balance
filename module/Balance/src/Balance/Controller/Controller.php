<?php

namespace Balance\Controller;

use Balance\Model\Model;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Controladora
 */
class Controller extends AbstractActionController implements ModelAwareInterface, RedirectRouteNameAwareInterface
{
    // Traits
    use ModelAwareTrait;
    use RedirectRouteNameAwareTrait;
    // Traits de Ação
    use IndexActionTrait;
    use EditActionTrait;
    use RemoveActionTrait;

    /**
     * Construtor Padrão
     *
     * @param Model  $model             Camada de Modelo
     * @param string $redirectRouteName Nome da Rota para Redirecionamento
     */
    public function __construct(Model $model, $redirectRouteName)
    {
        $this
            ->setModel($model)
            ->setRedirectRouteName($redirectRouteName);
    }
}
