<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "people".
 *
 * @property int $id
 * @property int $tmd_id
 * @property int $imdb_id
 * @property string|null $name
 * @property string|null $orig_name
 * @property string|null $birthday
 * @property string|null $deathday
 * @property string|null $place_of_birth
 * @property int|null $popularity
 * @property string|null $biography
 * @property int|null $gender
 *
 * @property MoviePeople[] $moviePeoples
 */
class People extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tmd_id', 'imdb_id'], 'required'],
            [['tmd_id', 'imdb_id', 'popularity', 'gender'], 'integer'],
            [['birthday', 'deathday'], 'safe'],
            [['biography'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['orig_name'], 'string', 'max' => 50],
            [['place_of_birth'], 'string', 'max' => 255],
            [['tmd_id'], 'unique'],
            [['imdb_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tmd_id' => 'Tmd ID',
            'imdb_id' => 'Imdb ID',
            'name' => 'Name',
            'orig_name' => 'Orig Name',
            'birthday' => 'Birthday',
            'deathday' => 'Deathday',
            'place_of_birth' => 'Place Of Birth',
            'popularity' => 'Popularity',
            'biography' => 'Biography',
            'gender' => 'Gender',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoviePeoples()
    {
        return $this->hasMany(MoviePeople::className(), ['people_id' => 'id']);
    }

    public function getGenderLabel()
    {
        if (empty($this->gender))
            return null;

        $arr = array('Not specified', 'Female', 'Male');

        return $arr[$this->gender];
    }
}
