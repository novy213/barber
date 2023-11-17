<?php

use yii\db\Migration;

/**
 * Class m231117_113358_notification
 */
class m231117_113358_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('visit', 'notified', $this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('visit', 'notified');
    }
}
