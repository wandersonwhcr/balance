<?php

namespace Balance\Stdlib\Hydrator\Strategy;

use IntlDateFormatter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Estratégia de Hidratador para Data e Hora
 */
class Datetime implements StrategyInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Formatador de Data
     * @type IntlDateFormatter
     */
    protected $formatter;

    /**
     * Apresentação de Formatador de Data
     *
     * @return IntlDateFormatter Elemento Solicitado
     */
    protected function getFormatter()
    {
        // Formatador Inicializado?
        if (! $this->formatter) {
            // Inicialização
            $this->formatter = $this->getServiceLocator()->get('i18n')
                ->createDateFormatter(IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM);
        }
        // Apresentação
        return $this->formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($value)
    {
        return $this->getFormatter()->format(strtotime($value));
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        return date('Y-m-d H:i:s', $this->getFormatter()->parse($value));
    }
}
