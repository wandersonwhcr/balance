<?php

namespace Balance\Model\Db;

use PHPUnit_Framework_TestCase as TestCase;

class PostingsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf('Balance\Model\ModelInterface', new Postings());
    }
}
