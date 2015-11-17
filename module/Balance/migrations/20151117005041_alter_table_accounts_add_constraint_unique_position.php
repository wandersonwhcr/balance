<?php

use Phinx\Migration\AbstractMigration;

class AlterTableAccountsAddConstraintUniquePosition extends AbstractMigration
{
    public function up()
    {
        // Organizar Ordenação de Contas
        $this->execute('
            UPDATE "accounts" AS "a" SET
                "position" = "p"."position" - 1
            FROM (
                SELECT
                    "a"."id",
                    ROW_NUMBER() OVER (ORDER BY "a"."position") AS "position"
                FROM "accounts" AS "a"
            ) AS "p"
            WHERE
                "p"."id" = "a"."id"
        ');

        // Adicionar Verificação
        $this->execute('ALTER TABLE "accounts" ADD UNIQUE("position")');
    }

    public function down()
    {
        // Remover Verificação
        $this->execute('ALTER TABLE "accounts" DROP CONSTRAINT "accounts_position_key"');
    }
}
