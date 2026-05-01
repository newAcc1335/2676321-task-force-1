<?php

use yii\db\Migration;

class m000000_000000_init extends Migration
{
    public function safeUp(): void
    {
        $base = __DIR__ . '/../data/database';

        $this->runSqlFile($base . '/schema/schema.sql');
        $this->runSqlFile($base . '/seed/categories.sql');
        $this->runSqlFile($base . '/seed/cities.sql');

        $this->addColumn('users', 'about', $this->text()->null());
        $this->execute("ALTER TABLE tasks MODIFY location POINT NULL");

        $filesPath = Yii::getAlias('@webroot/files/');
        if (!is_dir($filesPath)) {
            mkdir($filesPath, 0755, true);
        }
    }

    public function safeDown(): void
    {
        $this->dropTable('user_categories');
        $this->dropTable('task_files');
        $this->dropTable('reviews');
        $this->dropTable('responses');
        $this->dropTable('tasks');
        $this->dropTable('users');
        $this->dropTable('cities');
        $this->dropTable('categories');
    }

    private function runSqlFile(string $path): void
    {
        $sql = file_get_contents($path);
        $sql = preg_replace('/^\s*USE\s+\w+\s*;\s*/mi', '', $sql);

        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn ($s) => $s !== ''
        );

        foreach ($statements as $statement) {
            $this->execute($statement);
        }
    }
}
