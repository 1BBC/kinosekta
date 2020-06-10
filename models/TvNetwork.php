<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tv_network".
 *
 * @property int $tv_id
 * @property int $network_id
 *
 * @property Network $network
 * @property Tv $tv
 */
class TvNetwork extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tv_network';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tv_id', 'network_id'], 'required'],
            [['tv_id', 'network_id'], 'integer'],
            [['tv_id', 'network_id'], 'unique', 'targetAttribute' => ['tv_id', 'network_id']],
            [['network_id'], 'exist', 'skipOnError' => true, 'targetClass' => Network::className(), 'targetAttribute' => ['network_id' => 'id']],
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
            'network_id' => 'Network ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetwork()
    {
        return $this->hasOne(Network::className(), ['id' => 'network_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTv()
    {
        return $this->hasOne(Tv::className(), ['id' => 'tv_id']);
    }
}
