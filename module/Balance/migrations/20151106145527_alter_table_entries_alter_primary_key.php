<?php

use Phinx\Migration\AbstractMigration;

class AlterTableEntriesAlterPrimaryKey extends AbstractMigration
{
    public function up()
    {
        // Remover Chave Primária Simples
        $this->execute('ALTER TABLE "entries" DROP CONSTRAINT "entries_pkey"');
        $this->execute('ALTER TABLE "entries" DROP COLUMN "id"');
        // Inclusão de Chave Primária Composta
        $this->execute('ALTER TABLE "entries" ADD CONSTRAINT "entries_pkey" PRIMARY KEY("posting_id", "account_id")');
    }

    public function down()
    {
        // Remover Chave Primária Composta
        $this->execute('ALTER TABLE "entries" DROP CONSTRAINT "entries_pkey"');
        // Inclusão de Chave Primária Simples
        $this->execute('ALTER TABLE "entries" ADD COLUMN "id" SERIAL');
        $this->execute('ALTER TABLE "entries" ADD CONSTRAINT "entries_pkey" PRIMARY KEY("id")');
    }
}
