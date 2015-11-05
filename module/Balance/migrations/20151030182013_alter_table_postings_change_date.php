<?php

use Phinx\Migration\AbstractMigration;

class AlterTablePostingsChangeDate extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE "postings" RENAME COLUMN "date" TO "datetime"');
    }

    public function down()
    {
        $this->execute('ALTER TABLE "postings" RENAME COLUMN "datetime" TO "date"');
    }
}
