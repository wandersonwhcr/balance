<?php

namespace Balance\Model\Persistence\Db;

use PHPUnit_Framework_TestCase as TestCase;

class PostingsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf('Balance\Model\Persistence\PersistenceInterface', new Postings());
    }
}
