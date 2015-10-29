<?php

use Phinx\Migration\AbstractMigration;

class AlterTypeOperationFixNames extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TYPE "operation" RENAME TO _ACCOUNT_TYPE');

        $this->execute('CREATE TYPE ACCOUNT_TYPE AS ENUM (\'ACTIVE\', \'PASSIVE\')');
        $this->execute('CREATE TYPE ENTRY_TYPE AS ENUM (\'CREDIT\', \'DEBIT\')');

        $this->execute('ALTER TABLE "accounts" ALTER COLUMN "type" TYPE ACCOUNT_TYPE USING "type"::TEXT::ACCOUNT_TYPE');
        $this->execute('ALTER TABLE "entries" ALTER COLUMN "type" TYPE ENTRY_TYPE USING "type"::TEXT::ENTRY_TYPE');

        $this->execute('DROP TYPE _ACCOUNT_TYPE');
    }

    public function down()
    {
        $this->execute('CREATE TYPE operation AS ENUM (\'CREDIT\', \'DEBIT\', \'INPUT\', \'OUTPUT\')');

        $this->execute('ALTER TABLE "accounts" ALTER COLUMN "type" TYPE OPERATION USING "type"::TEXT::OPERATION');
        $this->execute('ALTER TABLE "entries" ALTER COLUMN "type" TYPE OPERATION USING "type"::TEXT::OPERATION');

        $this->execute('DROP TYPE ACCOUNT_TYPE');
        $this->execute('DROP TYPE ENTRY_TYPE');
    }
}
