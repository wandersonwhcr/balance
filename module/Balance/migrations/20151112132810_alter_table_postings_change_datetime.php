<?php

use Phinx\Migration\AbstractMigration;

class AlterTablePostingsChangeDatetime extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE "postings" ALTER COLUMN "datetime" TYPE TIMESTAMP WITH TIME ZONE');
    }

    public function down()
    {
        $this->execute('ALTER TABLE "postings" ALTER COLUMN "datetime" TYPE TIMESTAMP WITHOUT TIME ZONE');
    }
}
