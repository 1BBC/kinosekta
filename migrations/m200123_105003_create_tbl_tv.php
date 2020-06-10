<?php

use yii\db\Migration;

/**
 * Class m200123_105003_create_tbl_tv
 */
class m200123_105003_create_tbl_tv extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('tv', [
            'id'                    => $this->primaryKey(),
            't_created'             => $this->integer(11)->defaultValue(null),
            't_updated'             => $this->integer(11)->defaultValue(null),
            'tmd_id'                => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'kp_id'                 => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'r_kp'                  => $this->smallInteger()->unsigned()->defaultValue(null),
            'r_imdb'                => $this->smallInteger()->unsigned()->defaultValue(null),
            'imdb_id'               => $this->integer()->unsigned()->defaultValue(null)->unique(),
            'first_air_date'        => $this->date()->defaultValue(null),
            'title'                 => $this->string()->defaultValue(null)->notNull(),
            'orig_title'            => $this->string()->defaultValue(null),
            'overview'              => $this->text(),
            'external_ids'          => $this->string(),
            'episode_run_time'      => $this->tinyInteger()->unsigned()->defaultValue(null),
            'poster'                => $this->char(27)->notNull(),
            'popularity'            => $this->integer()->unsigned()->defaultValue(0),
            'images'                => $this->string(),
            'video'                 => $this->char(11),


            'is_action_adventure'        => $this->boolean(),
            'is_animation'               => $this->boolean(),
            'is_comedy'                  => $this->boolean(),
            'is_crime'                   => $this->boolean(),
            'is_documentary'             => $this->boolean(),
            'is_drama'                   => $this->boolean(),
            'is_family'                  => $this->boolean(),
            'is_kids'                    => $this->boolean(),
            'is_mystery'                 => $this->boolean(),
            'is_reality'                 => $this->boolean(),
            'is_science_fiction_fantasy' => $this->boolean(),
            'is_soap'                    => $this->boolean(),
            'is_talk'                    => $this->boolean(),
            'is_war_politics'            => $this->boolean(),
            'is_western'                 => $this->boolean(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('tv');
    }
}
