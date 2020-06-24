<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "movie".
 *
 * @property int $id
 * @property string|null $t_created
 * @property string|null $t_updated
 * @property int|null $tmd_id
 * @property int|null $kp_id
 * @property int|null $imdb_id
 * @property int|null $r_kp
 * @property int|null $r_imdb
 * @property string|null $release_date
 * @property int|null $runtime
 * @property string $title
 * @property string|null $orig_title
 * @property string|null $tagline
 * @property string|null $overview
 * @property string|null $external_ids
 * @property string $images;
 * @property string $video;
 * @property int|null $popularity
 * @property int|null $is_action
 * @property int|null $is_adventure
 * @property int|null $is_animation
 * @property int|null $is_comedy
 * @property int|null $is_crime
 * @property int|null $is_documentary
 * @property int|null $is_drama
 * @property int|null $is_family
 * @property int|null $is_fantasy
 * @property int|null $is_history
 * @property int|null $is_horror
 * @property int|null $is_music
 * @property int|null $is_mystery
 * @property int|null $is_romance
 * @property int|null $is_science_fiction
 * @property int|null $is_tv_movie
 * @property int|null $is_thriller
 * @property int|null $is_war
 * @property int|null $is_western
 *
 * @property MovieCountry[] $movieCountries
 * @property MoviePeople[] $moviePeoples
 * @property People[] $peoples
 */
class Movie extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movie';
    }

    public static function getGenresArr()
    {
        return [
            28      => 'is_action',
            12      => 'is_adventure',
            16      => 'is_animation',
            35      => 'is_comedy',
            80      => 'is_crime',
            99      => 'is_documentary',
            18      => 'is_drama',
            10751   => 'is_family',
            14      => 'is_fantasy',
            36      => 'is_history',
            27      => 'is_horror',
            10402   => 'is_music',
            9648    => 'is_mystery',
            10749   => 'is_romance',
            878     => 'is_science_fiction',
            10770   => 'is_tv_movie',
            53      => 'is_thriller',
            10752   => 'is_war',
            37      => 'is_western',
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
            [['t_created', 't_updated', 'release_date', 'video', 'images'], 'safe'],
            [['tmd_id', 'kp_id', 'imdb_id', 'runtime', 'popularity', 'is_action', 'is_adventure', 'is_animation', 'is_comedy', 'is_crime', 'is_documentary', 'is_drama', 'is_family', 'is_fantasy', 'is_history', 'is_horror', 'is_music', 'is_mystery', 'is_romance', 'is_science_fiction', 'is_tv_movie', 'is_thriller', 'is_war', 'is_western', 'r_kp', 'r_imdb'], 'integer'],
            [['title', 'tmd_id', 'imdb_id'], 'required'],
            [['overview'], 'string'],
            [['title', 'orig_title', 'tagline', 'external_ids'], 'string', 'max' => 255],
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
            'imdb_id' => 'Imdb ID',
            'r_imdb' => 'Rating IMDB',
            'r_kp' => 'Rating KP',
            'release_date' => 'Release Date',
            'runtime' => 'Runtime',
            'title' => 'Title',
            'orig_title' => 'Orig Title',
            'tagline' => 'Tagline',
            'overview' => 'Overview',
            'external_ids' => 'External Ids',
            'popularity' => 'Popularity',
            'is_action' => 'Action',
            'is_adventure' => 'Adventure',
            'is_animation' => 'Animation',
            'is_comedy' => 'Comedy',
            'is_crime' => 'Crime',
            'is_documentary' => 'Documentary',
            'is_drama' => 'Drama',
            'is_family' => 'Family',
            'is_fantasy' => 'Fantasy',
            'is_history' => 'History',
            'is_horror' => 'Horror',
            'is_music' => 'Music',
            'is_mystery' => 'Mystery',
            'is_romance' => 'Romance',
            'is_science_fiction' => 'Science Fiction',
            'is_tv_movie' => 'Tv Movie',
            'is_thriller' => 'Thriller',
            'is_war' => 'War',
            'is_western' => 'Western',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovieCountries()
    {
        return $this->hasMany(MovieCountry::className(), ['movie_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoviePeoples()
    {
        return $this->hasMany(MoviePeople::className(), ['movie_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeoples()
    {
        return $this->hasMany(People::className(), ['id' => 'people_id'])->viaTable('movie_people', ['movie_id' => 'id']);
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

    public function beforeDelete()
    {
        $folder = (int) ($this->id / 1000);

        $poster = Yii::getAlias('@webroot') . '/i/f/p/' . $folder . '/' . $this->id . '.jpg';
        if (file_exists($poster)) {
            unlink($poster);
        }

        $baseImgPath = Yii::getAlias('@webroot') . '/i/f/s/' . $folder . '/' . $this->id . '-';

        for ($i = 1; $i <= $this->images; $i++) {
            $fullImgPath = $baseImgPath . $i . '.jpg';

            if (file_exists($fullImgPath)) {
                unlink($fullImgPath);
            }
        }

        Yii::$app->cache->delete('movie' . $this->id);

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        Yii::$app->cache->delete('movie' . $this->id);

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
