<?php

use yii\db\Migration;

/**
 * Class m200108_115856_create_tbl_people
 */
class m200108_115856_create_tbl_people extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('people', [
            'id'              => $this->primaryKey(),
            'tmd_id'          => $this->integer()->unsigned()->notNull()->unique(),
            'imdb_id'         => $this->integer()->unsigned()->notNull()->unique(),
            'name'            => $this->string(100),
            'orig_name'       => $this->string(50),
            'birthday'        => $this->date()->defaultValue(null),
            'deathday'        => $this->date()->defaultValue(null),
            'place_of_birth'  => $this->string()->defaultValue(null),
            'popularity'      => $this->smallInteger()->unsigned()->defaultValue(0),
            'biography'       => $this->text()->defaultValue(null),
            'gender'          => $this->smallInteger()->unsigned()->defaultValue(null),
//            'profile_path'    => $this->char(27)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('people');
    }
}
