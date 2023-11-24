<?php

use yii\db\Migration;

/**
 * Class m231123_081451_add_label
 */
class m231123_081451_add_label extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('type', 'label', $this->string());
        $this->insert('type', ['id'=>1, 'type'=>'wlosy', 'label'=>"Strzyżenie męskie", 'time'=>30,'price'=>39]);
        $this->insert('type', ['id'=>2, 'type'=>'combo', 'label'=>"Combo", 'time'=>60,'price'=>75]);
        $this->insert('type', ['id'=>3, 'type'=>'combo_farb', 'label'=>"Combo + farbowanie", 'time'=>90,'price'=>110]);
        $this->insert('type', ['id'=>4, 'type'=>'broda', 'label'=>"Strzyżenie brody", 'time'=>30,'price'=>35]);
        $this->insert('type', ['id'=>5, 'type'=>'strzyzenie', 'label'=>"Strzyżenie maszynką na jedną długość", 'time'=>30,'price'=>25]);
        $this->insert('type', ['id'=>6, 'type'=>'buzzcut', 'label'=>"Buzz cut", 'time'=>30,'price'=>25]);
        $this->insert('type', ['id'=>7, 'type'=>'dayoff', 'label'=>"Dayoff", 'time'=>30,'price'=>0]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('type', 'label');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231123_081451_add_label cannot be reverted.\n";

        return false;
    }
    */
}
