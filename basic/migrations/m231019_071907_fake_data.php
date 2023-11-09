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
        $hardcodedPasswords = [
            password_hash('pass1', PASSWORD_BCRYPT),
            password_hash('pass2', PASSWORD_BCRYPT),
            password_hash('pass3', PASSWORD_BCRYPT),
            password_hash('pass4', PASSWORD_BCRYPT),
            password_hash('pass5', PASSWORD_BCRYPT),
            password_hash('pass6', PASSWORD_BCRYPT),
            password_hash('pass7', PASSWORD_BCRYPT),
            password_hash('pass8', PASSWORD_BCRYPT),
            password_hash('pass9', PASSWORD_BCRYPT),
            password_hash('pass10', PASSWORD_BCRYPT),
        ];

        for ($i = 0; $i < 10; $i++) {
            $data = [
                'id' => $i + 1,
                'password' => $hardcodedPasswords[$i],
                'name' => 'John',
                'last_name' => 'Doe',
                'phone' => 48111111111
            ];

            $this->insert('user', $data);
        }
        $this->update('user',['admin'=>1],['id'=>1]);
        $this->insert('barber', ['id'=>1, 'name'=>'barber1','last_name'=>'barber1', 'user_id'=>1]);
        $this->insert('barber', ['id'=>2, 'name'=>'barber2','last_name'=>'barber2', 'user_id'=>2]);
        $this->insert('type', ['id'=>1, 'type'=>'combo', 'time'=>45,'price'=>50]);
        $this->insert('type', ['id'=>2, 'type'=>'wlosy', 'time'=>30,'price'=>35]);
        $this->insert('type', ['id'=>3, 'type'=>'broda', 'time'=>30,'price'=>30]);
        $this->insert('type', ['id'=>4, 'type'=>'wolne', 'time'=>0,'price'=>0]);
        $this->insert('type', ['id'=>5, 'type'=>'razor', 'time'=>0,'price'=>5]);
        $this->insert('type', ['id'=>6, 'type'=>'coloring', 'time'=>0,'price'=>15]);
        $this->insert('type', ['id'=>7, 'type'=>'buzz cut', 'time'=>30,'price'=>20]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('user', []);
        $this->delete('barber', []);
        $this->delete('type', []);
        $this->delete('visit', []);
        $this->delete('ban', []);
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
