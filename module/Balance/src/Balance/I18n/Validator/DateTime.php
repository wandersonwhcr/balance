<?php

namespace Balance\I18n\Validator;

use Zend\I18n\Validator\DateTime as BaseDateTime;

/**
 * Classe para Correção de PR em zendframework/zend-i18n#19
 */
class DateTime extends BaseDateTime
{
    /**
     * {@inheritdoc}
     */
    protected function error($messageKey, $value = null)
    {
        $result = parent::error($messageKey, $value);
        if (self::INVALID_DATETIME === $messageKey) {
            $this->invalidateFormatter = true;
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function getIntlDateFormatter()
    {
        $formatter = parent::getIntlDateFormatter();
        $this->setTimezone($formatter->getTimezone());
        $this->setCalendar($formatter->getCalendar());
        $this->setPattern($formatter->getPattern());
        return $formatter;
    }
}
