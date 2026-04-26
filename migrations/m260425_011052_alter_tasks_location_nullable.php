<?php

use yii\db\Migration;

class m260425_011052_alter_tasks_location_nullable extends Migration
{
    public function safeUp(): void
    {
        $this->execute("
        ALTER TABLE tasks 
        MODIFY location POINT NULL
    ");
    }

    public function safeDown(): void
    {
        $this->execute("
        UPDATE tasks 
        SET location = ST_GeomFromText('POINT(0 0)')
        WHERE location IS NULL
        ");

        $this->execute("
        ALTER TABLE tasks 
        MODIFY location POINT NOT NULL
        ");
    }
}
