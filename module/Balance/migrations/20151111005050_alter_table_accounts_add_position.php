<?php

use Phinx\Migration\AbstractMigration;

class AlterTableAccountsAddPosition extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE "accounts" ADD COLUMN "position" INTEGER NULL');

        $this->execute('
            UPDATE "accounts" AS "a" SET
                "position" = "p"."position" - 1
            FROM (
                SELECT
                    "a"."id",
                    ROW_NUMBER() OVER (ORDER BY "a"."type", "a"."name") AS "position"
                FROM "accounts" AS "a"
            ) AS "p"
            WHERE
                "a"."id" = "p"."id"
        ');

        $this->execute('ALTER TABLE "accounts" ALTER COLUMN "position" SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE "accounts" DROP COLUMN "position"');
    }
}
