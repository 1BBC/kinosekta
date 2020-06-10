<?php

use yii\db\Migration;

/**
 * Class m200108_121529_create_tbl_movie_people
 */
class m200108_121529_create_tbl_movie_people extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('movie_people', [
            'movie_id'   => $this->integer()->notNull(),
            'people_id'  => $this->integer()->notNull(),
            'department' => $this->tinyInteger()->notNull(),
            'role'       => $this->string(60)->defaultValue(null),
            'UNIQUE(movie_id, people_id, department, role)',
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        // creates index for column `movie_id`
        $this->createIndex(
            'idx-movie_people-movie_id',
            'movie_people',
            'movie_id'
        );

        // add foreign key for table `movie_id`
        $this->addForeignKey(
            'fk-movie_people-movie_id',
            'movie_people',
            'movie_id',
            'movie',
            'id',
            'CASCADE'
        );

        // creates index for column `people_id`
        $this->createIndex(
            'idx-movie_people-people_id',
            'movie_people',
            'people_id'
        );

        // add foreign key for table `people_id`
        $this->addForeignKey(
            'fk-movie_people-people_id',
            'movie_people',
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
        // drops foreign key for table `movie`
        $this->dropForeignKey(
            'fk-movie_people-movie_id',
            'movie_people'
        );

        // drops index for column `movie_id`
        $this->dropIndex(
            'idx-movie_people-movie_id',
            'movie_people'
        );

        // drops foreign key for table `people`
        $this->dropForeignKey(
            'fk-movie_people-people_id',
            'movie_people'
        );

        // drops index for column `people_id`
        $this->dropIndex(
            'idx-movie_people-people_id',
            'movie_people'
        );

        $this->dropTable('movie_people');
    }
}
