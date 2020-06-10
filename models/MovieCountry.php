<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "movie_country".
 *
 * @property int $movie_id
 * @property string $country
 *
 * @property Movie $movie
 */
class MovieCountry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movie_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['movie_id', 'country'], 'required'],
            [['movie_id'], 'integer'],
            [['country'], 'string', 'max' => 2],
            [['movie_id', 'country'], 'unique', 'targetAttribute' => ['movie_id', 'country']],
            [['movie_id'], 'exist', 'skipOnError' => true, 'targetClass' => Movie::className(), 'targetAttribute' => ['movie_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'movie_id' => 'Movie ID',
            'country' => 'Country',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovie()
    {
        return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
    }
}
