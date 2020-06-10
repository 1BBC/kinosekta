<?php

use yii\db\Migration;

/**
 * Class m200123_153631_create_tbl_tv_network
 */
class m200123_153631_create_tbl_tv_network extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tv_network', [
            'tv_id'      => $this->integer()->notNull(),
            'network_id'  => $this->integer()->notNull(),
            'PRIMARY KEY(tv_id, network_id)',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        // creates index for column `tv_id`
        $this->createIndex(
            'idx-tv_network-tv_id',
            'tv_network',
            'tv_id'
        );

        // add foreign key for table `tv_id`
        $this->addForeignKey(
            'fk-tv_network-tv_id',
            'tv_network',
            'tv_id',
            'tv',
            'id',
            'CASCADE'
        );

        // creates index for column `network_id`
        $this->createIndex(
            'idx-tv_network-network_id',
            'tv_network',
            'network_id'
        );

        // add foreign key for table `network_id`
        $this->addForeignKey(
            'fk-tv_network-network_id',
            'tv_network',
            'network_id',
            'network',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `tv`
        $this->dropForeignKey(
            'fk-tv_network-tv_id',
            'tv_network'
        );

        // drops index for column `tv_id`
        $this->dropIndex(
            'idx-tv_network-tv_id',
            'tv_network'
        );

        // drops foreign key for table `network`
        $this->dropForeignKey(
            'fk-tv_network-network_id',
            'tv_network'
        );

        // drops index for column `network_id`
        $this->dropIndex(
            'idx-tv_network-network_id',
            'tv_network'
        );

        $this->dropTable('tv_network');
    }
}
