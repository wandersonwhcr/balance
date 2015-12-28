<?php

use Phinx\Migration\AbstractMigration;

class CreateTableTags extends AbstractMigration
{
    public function change()
    {
        $this->table('tags')
            ->addColumn('name', 'text')
            ->create();
    }
}
