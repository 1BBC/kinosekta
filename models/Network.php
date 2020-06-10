<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "network".
 *
 * @property int $id
 * @property int $tmd_id
 * @property string|null $name
 * @property string $logo_path
 *
 * @property TvNetwork[] $tvNetworks
 * @property Tv[] $tvs
 */
class Network extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tmd_id', 'logo_path'], 'required'],
            [['tmd_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['logo_path'], 'string', 'max' => 27],
            [['tmd_id'], 'unique'],
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
            'name' => 'Name',
            'logo_path' => 'Logo Path',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTvNetworks()
    {
        return $this->hasMany(TvNetwork::className(), ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTvs()
    {
        return $this->hasMany(Tv::className(), ['id' => 'tv_id'])->viaTable('tv_network', ['network_id' => 'id']);
    }
}
