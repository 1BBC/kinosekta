<?php

use yii\db\Migration;

/**
 * Class m200123_105028_create_tbl_tv_people
 */
class m200123_105028_create_tbl_tv_people extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tv_people', [
            'tv_id'      => $this->integer()->notNull(),
            'people_id'  => $this->integer()->notNull(),
            'role'       => $this->string(60),
            'PRIMARY KEY(tv_id, people_id, role)',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        // creates index for column `tv_id`
        $this->createIndex(
            'idx-tv_people-tv_id',
            'tv_people',
            'tv_id'
        );

        // add foreign key for table `tv_id`
        $this->addForeignKey(
            'fk-tv_people-tv_id',
            'tv_people',
            'tv_id',
            'tv',
            'id',
            'CASCADE'
        );

        // creates index for column `people_id`
        $this->createIndex(
            'idx-tv_people-people_id',
            'tv_people',
            'people_id'
        );

        // add foreign key for table `people_id`
        $this->addForeignKey(
            'fk-tv_people-people_id',
            'tv_people',
            'people_id',
            'people',
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
            'fk-tv_people-tv_id',
            'tv_people'
        );

        // drops index for column `tv_id`
        $this->dropIndex(
            'idx-tv_people-tv_id',
            'tv_people'
        );

        // drops foreign key for table `people`
        $this->dropForeignKey(
            'fk-tv_people-people_id',
            'tv_people'
        );

        // drops index for column `people_id`
        $this->dropIndex(
            'idx-tv_people-people_id',
            'tv_people'
        );

        $this->dropTable('tv_people');
    }
}
