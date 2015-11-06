<?php

use Phinx\Migration\AbstractMigration;

class AlterTableEntriesAddForeignKeys extends AbstractMigration
{
    public function up()
    {
        // Chave Estrangeira de Contas
        $this->execute(
            'ALTER TABLE "entries" ADD CONSTRAINT "entries_account_id"'
            . ' FOREIGN KEY("account_id") REFERENCES "accounts"("id")'
            . ' ON UPDATE CASCADE ON DELETE RESTRICT'
        );
        // Chave Estrangeira de Lançamentos
        $this->execute(
            'ALTER TABLE "entries" ADD CONSTRAINT "entries_posting_id"'
            . ' FOREIGN KEY("posting_id") REFERENCES "postings"("id")'
            . ' ON UPDATE CASCADE ON DELETE CASCADE'
        );
    }

    public function down()
    {
        // Remover Chaves Estrangeiras
        $this->execute('ALTER TABLE "entries" DROP CONSTRAINT "entries_posting_id"');
        $this->execute('ALTER TABLE "entries" DROP CONSTRAINT "entries_account_id"');
    }
}
