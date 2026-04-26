<?php

use yii\db\Migration;

class m260420_220715_add_about_column_to_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->addColumn('users', 'about', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropColumn('users', 'about');
    }
}
