<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%motorbike}}`.
 */
class m220701_193016_create_motorbike_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%motorbike}}', [
            'id' => $this->primaryKey(),
            'model' => $this->string(64)->notNull(),
            'color' => $this->string(64)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%motorbike}}');
    }
}
