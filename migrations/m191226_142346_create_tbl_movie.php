<?php

use yii\db\Migration;

/**
 * Class m191226_142346_create_tbl_movie
 */
class m191226_142346_create_tbl_movie extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('movie', [
            'id'                    => $this->primaryKey(),
            't_created'             => $this->integer(11)->defaultValue(null),
            't_updated'             => $this->integer(11)->defaultValue(null),
            'tmd_id'                => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'kp_id'                 => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'imdb_id'               => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'r_kp'                  => $this->smallInteger()->unsigned()->defaultValue(null),
            'r_imdb'                => $this->smallInteger()->unsigned()->defaultValue(null),
            'release_date'          => $this->date()->defaultValue(null),
            'runtime'               => $this->tinyInteger()->unsigned()->defaultValue(null),
            'title'                 => $this->string()->defaultValue(null)->notNull(),
            'orig_title'            => $this->string()->defaultValue(null),
            'tagline'               => $this->string()->defaultValue(null),
            'overview'              => $this->text(),
            'external_ids'          => $this->string(),
//            'poster'                => $this->char(27)->notNull(),
            'popularity'            => $this->smallInteger()->unsigned()->defaultValue(0),
            'images'                => $this->integer()->defaultValue(null),
            'video'                 => $this->char(11),


            'is_action'          => $this->boolean(),
            'is_adventure'       => $this->boolean(),
            'is_animation'       => $this->boolean(),
            'is_comedy'          => $this->boolean(),
            'is_crime'           => $this->boolean(),
            'is_documentary'     => $this->boolean(),
            'is_drama'           => $this->boolean(),
            'is_family'          => $this->boolean(),
            'is_fantasy'         => $this->boolean(),
            'is_history'         => $this->boolean(),
            'is_horror'          => $this->boolean(),
            'is_music'           => $this->boolean(),
            'is_mystery'         => $this->boolean(),
            'is_romance'         => $this->boolean(),
            'is_science_fiction' => $this->boolean(),
            'is_tv_movie'        => $this->boolean(),
            'is_thriller'        => $this->boolean(),
            'is_war'             => $this->boolean(),
            'is_western'         => $this->boolean(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('movie');
    }
}
