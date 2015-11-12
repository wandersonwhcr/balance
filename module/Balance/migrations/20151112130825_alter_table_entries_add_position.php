<?php

use Phinx\Migration\AbstractMigration;

class AlterTableEntriesAddPosition extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE "entries" ADD COLUMN "position" INTEGER NULL');

        $this->execute('
            UPDATE "entries" AS "e" SET
                "position" = "p"."position" - 1
            FROM (
                SELECT
                    "p"."id" AS "posting_id",
                    "a"."id" AS "account_id",
                    ROW_NUMBER() OVER (PARTITION BY "p"."id" ORDER BY "a"."type", "a"."name") AS "position"
                FROM "entries" AS "e"
                JOIN "accounts" AS "a" ON "a"."id" = "e"."account_id"
                JOIN "postings" AS "p" ON "p"."id" = "e"."posting_id"
            ) AS "p"
            WHERE
                "e"."posting_id" = "p"."posting_id"
                AND "e"."account_id" = "p"."account_id"
        ');

        $this->execute('ALTER TABLE "entries" ALTER COLUMN "position" SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE "entries" DROP COLUMN "position"');
    }
}
