<?php

use Phinx\Migration\AbstractMigration;

class CreateTableModules extends AbstractMigration
{
    public function change()
    {
        $this->table('modules', ['id' => false, 'primary_key' => 'identifier'])
            ->addColumn('identifier', 'string', ['limit' => 20])
            ->addColumn('enabled', 'boolean', ['default' => 'FALSE'])
            ->create();
    }
}
