<?php

use Phinx\Migration\AbstractMigration;

class CreateTableModules extends AbstractMigration
{
    public function change()
    {
        $this->table('modules', array('id' => false, 'primary_key' => 'name'))
            ->addColumn('name', 'string', array('limit' => 20))
            ->create();
    }
}
