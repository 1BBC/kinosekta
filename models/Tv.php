<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tv".
 *
 * @property int $id
 * @property int|null $t_created
 * @property int|null $t_updated
 * @property int|null $tmd_id
 * @property int|null $kp_id
 * @property int|null $imdb_id
 * @property int|null $r_kp
 * @property int|null $r_imdb
 * @property string|null $first_air_date
 * @property string $title
 * @property string|null $orig_title
 * @property string|null $overview
 * @property string|null $external_ids
 * @property int|null $episode_run_time
 * @property string $poster
 * @property int|null $popularity
 * @property string|null $images
 * @property string|null $video
 * @property int|null $is_action_adventure
 * @property int|null $is_animation
 * @property int|null $is_comedy
 * @property int|null $is_crime
 * @property int|null $is_documentary
 * @property int|null $is_drama
 * @property int|null $is_family
 * @property int|null $is_kids
 * @property int|null $is_mystery
 * @property int|null $is_reality
 * @property int|null $is_science_fiction_fantasy
 * @property int|null $is_soap
 * @property int|null $is_talk
 * @property int|null $is_war_politics
 * @property int|null $is_western
 *
 * @property TvPeople[] $tvPeoples
 * @property TvNetwork[] $tvNetworks
 */
class Tv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tv';
    }

    public static function getGenresArr()
    {
        return [
            10759   => 'is_action_adventure'       ,
            16      => 'is_animation'              ,
            35      => 'is_comedy'                 ,
            80      => 'is_crime'                  ,
            99      => 'is_documentary'            ,
            18      => 'is_drama'                  ,
            10751   => 'is_family'                 ,
            10762   => 'is_kids'                   ,
            9648    => 'is_mystery'                ,
            10764   => 'is_reality'                ,
            10765   => 'is_science_fiction_fantasy',
            10766   => 'is_soap'                   ,
            10767   => 'is_talk'                   ,
            10768   => 'is_war_politics'           ,
            37      => 'is_western'                ,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['t_created', 't_updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['t_updated'],
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
            [['t_created', 't_updated', 'tmd_id', 'kp_id', 'imdb_id', 'episode_run_time', 'popularity', 'is_action_adventure', 'is_animation', 'is_comedy', 'is_crime', 'is_documentary', 'is_drama', 'is_family', 'is_kids', 'is_mystery', 'is_reality', 'is_science_fiction_fantasy', 'is_soap', 'is_talk', 'is_war_politics', 'is_western', 'r_kp', 'r_imdb'], 'integer'],
            [['first_air_date'], 'safe'],
            [['title', 'poster'], 'required'],
            [['overview'], 'string'],
            [['title', 'orig_title', 'external_ids', 'images'], 'string', 'max' => 255],
            [['poster'], 'string', 'max' => 27],
            [['video'], 'string', 'max' => 11],
            [['tmd_id'], 'unique'],
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
            't_updated' => 'T Updated',
            'tmd_id' => 'Tmd ID',
            'kp_id' => 'Kp ID',
            'r_imdb' => 'Rating IMDB',
            'r_kp' => 'Rating KP',
            'imdb_id' => 'Imdb ID',
            'first_air_date' => 'First Air Date',
            'title' => 'Title',
            'orig_title' => 'Orig Title',
            'overview' => 'Overview',
            'external_ids' => 'External Ids',
            'episode_run_time' => 'Episode Run Time',
            'poster' => 'Poster',
            'popularity' => 'Popularity',
            'images' => 'Images',
            'video' => 'Video',
            'is_action_adventure' => 'Is Action Adventure',
            'is_animation' => 'Is Animation',
            'is_comedy' => 'Is Comedy',
            'is_crime' => 'Is Crime',
            'is_documentary' => 'Is Documentary',
            'is_drama' => 'Is Drama',
            'is_family' => 'Is Family',
            'is_kids' => 'Is Kids',
            'is_mystery' => 'Is Mystery',
            'is_reality' => 'Is Reality',
            'is_science_fiction_fantasy' => 'Is Science Fiction Fantasy',
            'is_soap' => 'Is Soap',
            'is_talk' => 'Is Talk',
            'is_war_politics' => 'Is War Politics',
            'is_western' => 'Is Western',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTvPeoples()
    {
        return $this->hasMany(TvPeople::className(), ['tv_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeoples()
    {
        return $this->hasMany(People::className(), ['id' => 'people_id'])->viaTable('tv_people', ['tv_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTvNetworks()
    {
        return $this->hasMany(TvNetwork::className(), ['tv_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworks()
    {
        return $this->hasMany(Network::className(), ['id' => 'network_id'])->viaTable('tv_network', ['tv_id' => 'id']);
    }

    public function setGenres(array $tmdbGenres)
    {
        $genresArr = self::getGenresArr();

        foreach ($tmdbGenres as $genre) {
            $attribute = $genresArr[$genre->id];
            $this->$attribute = 1;
        }
    }

    public function getGenresStr()
    {
        $arr = array();

        foreach (self::getGenresArr() as $attribute){
            if($this->$attribute == 1) {
                array_push($arr, $this->getAttributeLabel($attribute));
            }
        }

        return implode(',', $arr);
    }
}
