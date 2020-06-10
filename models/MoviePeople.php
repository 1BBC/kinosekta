<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "movie_people".
 *
 * @property int $movie_id
 * @property int $people_id
 * @property int $department
 * @property string|null $role
 *
 * @property Movie $movie
 * @property People $people
 */
class MoviePeople extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movie_people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['movie_id', 'people_id', 'department'], 'required'],
            [['movie_id', 'people_id', 'department'], 'integer'],
            [['role'], 'string', 'max' => 60],
            [['movie_id', 'people_id', 'department', 'role'], 'unique', 'targetAttribute' => ['movie_id', 'people_id', 'department', 'role']],
            [['movie_id'], 'exist', 'skipOnError' => true, 'targetClass' => Movie::className(), 'targetAttribute' => ['movie_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'movie_id' => 'Movie ID',
            'people_id' => 'People ID',
            'department' => 'Department',
            'role' => 'Role',
        ];
    }

    /**
     * Gets query for [[Movie]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMovie()
    {
        return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }
}
