<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m220827_100651_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull(),
            'email' => $this->string(50)->notNull(),
            'fio' => $this->string(100)->notNull(),
            'created_at' => $this->datetime()->notNull(),
            'updated_at' => $this->datetime(),
        ]);

        $this->createIndex('idx-user-username', 'user', 'username', true);
        $this->createIndex('idx-user-email', 'user', 'email', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-user-username', 'user');
        $this->dropIndex('idx-user-email', 'user');

        $this->dropTable('{{%user}}');
    }
}
