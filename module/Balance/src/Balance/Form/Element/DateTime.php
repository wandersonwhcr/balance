<?php

namespace Balance\Form\Element;

use Zend\Form\Element\DateTime as ZendDateTime;

/**
 * Elemento de Formulário para Data e Hora
 */
class DateTime extends ZendDateTime
{
    public function init()
    {
        $this
            ->setAttribute('class', 'form-control-datetimepicker')
            ->setOption('add-on-append', '<span class="glyphicon glyphicon-calendar"></span>');
    }
}
