<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tv_people".
 *
 * @property int $tv_id
 * @property int $people_id
 * @property string $role
 *
 * @property People $people
 * @property Tv $tv
 */
class TvPeople extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tv_people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tv_id', 'people_id', 'role'], 'required'],
            [['tv_id', 'people_id'], 'integer'],
            [['role'], 'string', 'max' => 60],
            [['role'], 'safe'],
            [['tv_id', 'people_id', 'role'], 'unique', 'targetAttribute' => ['tv_id', 'people_id', 'role']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
            [['tv_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tv::className(), 'targetAttribute' => ['tv_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tv_id' => 'Tv ID',
            'people_id' => 'People ID',
            'role' => 'Role',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTv()
    {
        return $this->hasOne(Tv::className(), ['id' => 'tv_id']);
    }
}
