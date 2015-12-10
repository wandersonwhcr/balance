<?php

namespace Balance\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;

class DateTimeTest extends TestCase
{
    public function testDefaultClassAttribute()
    {
        $element = new DateTime();

        $value = $element->getAttribute('class');
        $this->assertEquals('form-control-datetimepicker', $value);

        $value = $element->setAttribute('class', 'foo-bar')->getAttribute('class');
        $this->assertEquals('form-control-datetimepicker foo-bar', $value);
    }

    public function testDefaultAddOnAppendOption()
    {
        $element = new DateTime();

        $value = $element->getOption('add-on-append');
        $this->assertEquals('<span class="glyphicon glyphicon-calendar"></span>', $value);
    }
}
