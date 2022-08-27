<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%rent}}`.
 */
class m220827_131427_drop_username_column_from_rent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('rent', 'username');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('rent', 'username', $this->string(64)->notNull());
    }
}
