<?php

use yii\db\Migration;

/**
 * Class m200108_133947_create_tbl_movie_country
 */
class m200108_133947_create_tbl_movie_country extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('movie_country', [
            'movie_id'   => $this->integer()->notNull(),
            'country'    => $this->char(2)->notNull(),
            'PRIMARY KEY(movie_id, country)',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        // creates index for column `movie_id`
        $this->createIndex(
            'idx-movie_country-movie_id',
            'movie_country',
            'movie_id'
        );

        // add foreign key for table `movie_id`
        $this->addForeignKey(
            'fk-movie_country-movie_id',
            'movie_country',
            'movie_id',
            'movie',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `movie_country`
        $this->dropForeignKey(
            'fk-movie_country-movie_id',
            'movie_country'
        );

        // drops index for column `movie_id`
        $this->dropIndex(
            'idx-movie_country-movie_id',
            'movie_country'
        );

        $this->dropTable('movie_country');
    }
}
