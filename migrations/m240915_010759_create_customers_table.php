<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customers}}`.
 */
class m240915_010759_create_customers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%customers}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'cpf' => $this->string()->notNull(),
            'sex' => "ENUM('M', 'F') NOT NULL",
            'zipcode' => $this->string(),
            'address' => $this->string(),
            'number' => $this->string(),
            'neighborhood' => $this->string(),
            'city' => $this->string(),
            'state' => $this->string(),
            'complement' => $this->string(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);

        $this->createIndex('{{%idx-customers-cpf}}', '{{%customers}}', 'cpf', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%customers}}');
    }
}
