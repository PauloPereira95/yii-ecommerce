<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_adresses}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m231222_103511_create_user_adresses_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_adresses}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->ineteger(11)->notNull(),
            'address' => $this->string(255)->notNull(),
            'city' => $this->string(255)->notNull(),
            'state' => $this->string(255)->notNull(),
            'country' => $this->string(255)->notNull(),
            'zipcode' => $this->string(255),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-user_adresses-user_id}}',
            '{{%user_adresses}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-user_adresses-user_id}}',
            '{{%user_adresses}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-user_adresses-user_id}}',
            '{{%user_adresses}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-user_adresses-user_id}}',
            '{{%user_adresses}}'
        );

        $this->dropTable('{{%user_adresses}}');
    }
}
