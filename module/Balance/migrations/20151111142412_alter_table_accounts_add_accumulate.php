<?php

use Phinx\Migration\AbstractMigration;

class AlterTableAccountsAddAccumulate extends AbstractMigration
{
    public function change()
    {
        $this->table('accounts')
            ->addColumn('accumulate', 'boolean', ['default' => 'FALSE'])
            ->update();
    }
}
