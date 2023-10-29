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
                'email' => 'user' . $i . '@wp.pl',
                'phone' => rand(100000000, 999999999)
            ];

            $this->insert('user', $data);
        }
        $this->update('user',['admin'=>1],['id'=>1]);
        $this->insert('barber', ['id'=>1, 'name'=>'barber1','last_name'=>'barber1', 'user_id'=>1]);
        $this->insert('barber', ['id'=>2, 'name'=>'barber2','last_name'=>'barber2', 'user_id'=>2]);
        $this->insert('type', ['id'=>1, 'type'=>'combo', 'time'=>45]);
        $this->insert('type', ['id'=>2, 'type'=>'wlosy', 'time'=>30]);
        $this->insert('type', ['id'=>3, 'type'=>'broda', 'time'=>30]);
        $this->insert('type', ['id'=>4, 'type'=>'wolne', 'time'=>0]);
        $this->insert('price', ['id'=>1, 'type_id'=>1, 'price'=>90]);
        $this->insert('price', ['id'=>2, 'type_id'=>2, 'price'=>50]);
        $this->insert('price', ['id'=>3, 'type_id'=>3, 'price'=>40]);
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
        $this->delete('price', []);
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
