<?php

use Phinx\Migration\AbstractMigration;

class CreateTableEntries extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('entries')
            ->addColumn('account_id', 'integer')
            ->addColumn('posting_id', 'integer')
            ->addColumn('type', 'text')
            ->addColumn('value', 'decimal', array('scale' => 2, 'precision' => 15))
            ->create();

        $this->execute('ALTER TABLE entries ALTER COLUMN type SET DATA TYPE operation USING type::operation');
    }
}
