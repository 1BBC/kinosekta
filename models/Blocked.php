<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "blocked".
 *
 * @property int $id
 * @property int|null $t_created
 * @property int|null $kp_id
 * @property int|null $imdb_id
 */
class Blocked extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blocked';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['t_created'],
                ],
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['t_created', 'kp_id', 'imdb_id'], 'integer'],
            [['kp_id'], 'unique'],
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
            't_created' => 'T Created',
            'kp_id' => 'Kp ID',
            'imdb_id' => 'Imdb ID',
        ];
    }

    public static function blocked($kp_id = null, $imdb_id = null)
    {
        if (empty($kp_id) && empty($imdb_id)) return false;

        $obj = new self();
        $obj->imdb_id = $imdb_id;
        $obj->kp_id   = $kp_id;
        $obj->save();

        return true;
    }
}
