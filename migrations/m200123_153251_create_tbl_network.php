<?php

use yii\db\Migration;

/**
 * Class m200123_153251_create_tbl_network
 */
class m200123_153251_create_tbl_network extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('network', [
            'id'              => $this->primaryKey(),
            'tmd_id'          => $this->integer()->unsigned()->notNull()->unique(),
            'name'            => $this->string(100),
            'logo_path'       => $this->char(27)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('network');
    }
}
