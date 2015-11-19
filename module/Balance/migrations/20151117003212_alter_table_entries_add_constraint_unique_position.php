<?php

use Phinx\Migration\AbstractMigration;

class AlterTableEntriesAddConstraintUniquePosition extends AbstractMigration
{
    public function up()
    {
        // Reordenar Linhas (Corrigindo Possíveis Problemas)
        $this->execute('
            UPDATE "entries" AS "e" SET
                "position" = "p"."position" - 1
            FROM (
                SELECT
                    "e"."posting_id",
                    "e"."account_id",
                    ROW_NUMBER() OVER (PARTITION BY "e"."posting_id" ORDER BY "e"."position") AS "position"
                FROM "entries" AS "e"
            ) AS "p"
            WHERE
                "p"."posting_id" = "e"."posting_id"
                AND "p"."account_id" = "e"."account_id"
        ');

        // Adicionar Verificação
        $this->execute('ALTER TABLE "entries" ADD UNIQUE("posting_id", "position")');
    }

    public function down()
    {
        // Remover Verificação
        $this->execute('ALTER TABLE "entries" DROP CONSTRAINT "entries_posting_id_position_key"');
    }
}
