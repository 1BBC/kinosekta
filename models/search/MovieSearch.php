<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Movie;

/**
 * MovieSearch represents the model behind the search form of `app\models\Movie`.
 */
class MovieSearch extends Movie
{
    public $startDate;
    public $endDate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 't_created', 't_updated', 'tmd_id', 'kp_id', 'imdb_id', 'r_kp', 'r_imdb', 'runtime', 'popularity', 'is_action', 'is_adventure', 'is_animation', 'is_comedy', 'is_crime', 'is_documentary', 'is_drama', 'is_family', 'is_fantasy', 'is_history', 'is_horror', 'is_music', 'is_mystery', 'is_romance', 'is_science_fiction', 'is_tv_movie', 'is_thriller', 'is_war', 'is_western'], 'integer'],
            [['release_date', 'title', 'orig_title', 'tagline', 'overview', 'external_ids', 'images', 'video', 'startDate', 'endDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Movie::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);
//        $this->setAttribute('startDate', $params['startDate']);
//        $this->setAttribute('endDate', $params['endDate']);
//        print_r($this);die();
//        $this->startDate = '2020-01-01';
//        $this->endDate = '2020-01-02';

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            't_created' => $this->t_created,
            't_updated' => $this->t_updated,
            'tmd_id' => $this->tmd_id,
            'kp_id' => $this->kp_id,
            'imdb_id' => $this->imdb_id,
            'r_kp' => $this->r_kp,
            'r_imdb' => $this->r_imdb,
            'release_date' => $this->release_date,
            'runtime' => $this->runtime,
            'popularity' => $this->popularity,
            'is_action' => $this->is_action,
            'is_adventure' => $this->is_adventure,
            'is_animation' => $this->is_animation,
            'is_comedy' => $this->is_comedy,
            'is_crime' => $this->is_crime,
            'is_documentary' => $this->is_documentary,
            'is_drama' => $this->is_drama,
            'is_family' => $this->is_family,
            'is_fantasy' => $this->is_fantasy,
            'is_history' => $this->is_history,
            'is_horror' => $this->is_horror,
            'is_music' => $this->is_music,
            'is_mystery' => $this->is_mystery,
            'is_romance' => $this->is_romance,
            'is_science_fiction' => $this->is_science_fiction,
            'is_tv_movie' => $this->is_tv_movie,
            'is_thriller' => $this->is_thriller,
            'is_war' => $this->is_war,
            'is_western' => $this->is_western,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'orig_title', $this->orig_title])
            ->andFilterWhere(['like', 'tagline', $this->tagline])
            ->andFilterWhere(['like', 'overview', $this->overview])
            ->andFilterWhere(['like', 'external_ids', $this->external_ids])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'video', $this->video]);

//        print_r('text' . $this->tmd_id);die();
        if (!empty($this->startDate) && !empty($this->endDate)) {
//            die();
            $query->andFilterWhere(['between', 'release_date', $this->startDate, $this->endDate]);
        }

        return $dataProvider;
    }
}
