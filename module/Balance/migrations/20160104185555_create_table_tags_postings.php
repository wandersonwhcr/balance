<?php

use Phinx\Migration\AbstractMigration;

class CreateTableTagsPostings extends AbstractMigration
{
    public function change()
    {
        $this->table('tags_postings', ['id' => false, 'primary_key' => ['tag_id', 'posting_id']])
            ->addColumn('tag_id', 'integer')
            ->addColumn('posting_id', 'integer')
            ->addForeignKey('tag_id', 'tags', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->addForeignKey('posting_id', 'postings', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->create();
    }
}
