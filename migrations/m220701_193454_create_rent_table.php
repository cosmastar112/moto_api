<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%rent}}`.
 */
class m220701_193454_create_rent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%rent}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull(),
            'moto_id' => $this->integer()->notNull(),
            'date_rent_started' => $this->datetime()->notNull(),
            'date_rent_ended' => $this->datetime()->notNull(),
            'created_at' => $this->datetime()->notNull(),
            'updated_at' => $this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%rent}}');
    }
}
