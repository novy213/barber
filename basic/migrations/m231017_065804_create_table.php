<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%}}`.
 */
class m231017_065804_create_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'phone' => $this->integer()->notNull(),
            'admin' => $this->boolean()->defaultValue(0),
            'access_token' => $this->string()
        ]);
        $this -> alterColumn('user','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('visit', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'date' => $this->string()->notNull(),
            'barber_id' => $this->integer()->notNull(),
            'price' => $this->float()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'hair' => $this->boolean()->defaultValue(0)->notNull(),
            'time' => $this->float()->notNull(),
            'additional_info' => $this->string(),
            'user_id' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('visit','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('barber', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
        ]);
        $this -> alterColumn('barber','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('type', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'type' => $this->string()->notNull(),
            'time' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('type','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('price', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'type_id' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('price','id', $this->integer().' AUTO_INCREMENT');
        $this->addForeignKey(
            'fk-price-type',
            'price',
            'type_id',
            'type',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-visit-barber',
            'visit',
            'barber_id',
            'barber',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-visit-type',
            'visit',
            'type_id',
            'type',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-visit-user',
            'visit',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-visit-barber', 'visit');
        $this->dropForeignKey('fk-visit-type', 'visit');
        $this->dropForeignKey('fk-visit-user', 'visit');
        $this->dropForeignKey('fk-price-type', 'price');
        $this->dropTable('user');
        $this->dropTable('visit');
        $this->dropTable('type');
        $this->dropTable('barber');
        $this->dropTable('price');
    }
}
