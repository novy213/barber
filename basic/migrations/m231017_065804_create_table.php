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
            'phone' => $this->bigInteger()->notNull(),
            'admin' => $this->boolean()->defaultValue(0),
            'notification' => $this->integer()->defaultValue(60),
            'verified' => $this->boolean()->defaultValue(0),
            'access_token' => $this->string()
        ]);
        $this -> alterColumn('user','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('visit', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'date' => $this->string()->notNull(),
            'barber_id' => $this->integer()->notNull(),
            'price' => $this->float()->notNull(),
            'type_id' => $this->integer()->notNull(),
            'coloring' => $this->boolean()->defaultValue(0)->notNull(),
            'razor' => $this->boolean()->defaultValue(0)->notNull(),
            'time' => $this->float()->notNull(),
            'additional_info' => $this->string(),
            'user_id' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('visit','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('barber', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('barber','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('type', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'type' => $this->string()->notNull(),
            'time' => $this->integer()->notNull(),
            'price'=>$this->integer()->notNull(),
        ]);
        $this -> alterColumn('type','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('additional_services', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'type' => $this->string()->notNull(),
            'price'=>$this->integer()->notNull(),
        ]);
        $this -> alterColumn('additional_services','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('additional_type', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'additional_id' => $this->integer()->notNull(),
            'type_id'=>$this->integer()->notNull(),
        ]);
        $this -> alterColumn('additional_services','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('ban', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'user_id' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('ban','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('code', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'code' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('code','id', $this->integer().' AUTO_INCREMENT');
        $this->addForeignKey(
            'fk-additional-add',
            'additional_type',
            'additional_id',
            'additional_services',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-additional-type',
            'additional_type',
            'type_id',
            'type',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-code-user',
            'code',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-barber-user',
            'barber',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-ban-user',
            'ban',
            'user_id',
            'user',
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
        $this->dropForeignKey('fk-barber-user', 'barber');
        $this->dropForeignKey('fk-ban-user', 'ban');
        $this->dropForeignKey('fk-visit-barber', 'visit');
        $this->dropForeignKey('fk-visit-type', 'visit');
        $this->dropForeignKey('fk-visit-user', 'visit');
        $this->dropForeignKey('fk-code-user', 'code');
        $this->dropForeignKey('fk-additional-type', 'additional_type');
        $this->dropForeignKey('fk-additional-add', 'additional_type');
        $this->dropTable('ban');
        $this->dropTable('additional_services');
        $this->dropTable('additional_type');
        $this->dropTable('code');
        $this->dropTable('user');
        $this->dropTable('visit');
        $this->dropTable('type');
        $this->dropTable('barber');
    }
}
