<?php

use Phinx\Migration\AbstractMigration;

class AlterTableEntriesAddForeignKeys extends AbstractMigration
{
    public function change()
    {
        $this->table('entries')
            ->addForeignKey('account_id', 'accounts', 'id', array('update' => 'cascade', 'delete' => 'restrict'))
            ->addForeignKey('posting_id', 'postings', 'id', array('update' => 'cascade', 'delete' => 'cascade'))
            ->update();
    }
}
