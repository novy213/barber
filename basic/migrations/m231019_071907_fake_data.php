<?php

use yii\db\Migration;

/**
 * Class m231019_071907_fake_data
 */
class m231019_071907_fake_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('type', ['id'=>1, 'label'=>'combo_razor', 'time'=>60,'price'=>50]);
        $this->insert('type', ['id'=>2, 'label'=>'combo_golarka', 'time'=>60,'price'=>50]);
        $this->insert('type', ['id'=>3, 'label'=>'combo_razorFarb', 'time'=>90,'price'=>50]);
        $this->insert('type', ['id'=>4, 'label'=>'combo_golarkaFarb', 'time'=>90,'price'=>50]);
        $this->insert('type', ['id'=>5, 'label'=>'wlosy', 'time'=>30,'price'=>35]);
        $this->insert('type', ['id'=>6, 'label'=>'broda_razor', 'time'=>30,'price'=>30]);
        $this->insert('type', ['id'=>7, 'label'=>'broda_golarka', 'time'=>30,'price'=>30]);
        $this->insert('type', ['id'=>8, 'label'=>'dayoff', 'time'=>0,'price'=>0]);
        $this->insert('type', ['id'=>9, 'label'=>'buzz cut', 'time'=>30,'price'=>20]);
        $this->insert('additional_services', ['id'=>1, 'label'=>'razor', 'price'=>5, 'time'=>0]);
        $this->insert('additional_services', ['id'=>2, 'label'=>'coloring', 'price'=>15, 'time'=>15]);
        $this->insert('additional_type', ['id'=>1, 'additional_id'=>1, 'type_id'=>2]);
        $this->insert('additional_type', ['id'=>2, 'additional_id'=>2, 'type_id'=>2]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('type', []);
        $this->delete('visit', []);
        $this->delete('additional_services', []);
        $this->delete('additional_type', []);
        $this->delete('code', []);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231019_071907_fake_data cannot be reverted.\n";

        return false;
    }
    */
}
