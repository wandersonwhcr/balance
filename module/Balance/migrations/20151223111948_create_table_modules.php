<?php

use Phinx\Migration\AbstractMigration;

class CreateTableModules extends AbstractMigration
{
    public function change()
    {
        $this->table('modules', array('id' => false, 'primary_key' => 'identifier'))
            ->addColumn('identifier', 'string', array('limit' => 20))
            ->addColumn('enabled', 'boolean', array('default' => 'FALSE'))
            ->create();
    }
}
