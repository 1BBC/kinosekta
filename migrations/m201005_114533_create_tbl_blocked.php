<?php

use yii\db\Migration;

/**
 * Class m201005_114533_create_tbl_blocked
 */
class m201005_114533_create_tbl_blocked extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('blocked', [
            'id'              => $this->primaryKey(),
            't_created'       => $this->integer(11)->defaultValue(null),
            'kp_id'           => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'imdb_id'         => $this->integer()->unsigned()->defaultValue(null)->unique(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('blocked');
    }
}
