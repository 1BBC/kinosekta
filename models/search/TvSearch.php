<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tv;

/**
 * TvSearch represents the model behind the search form of `app\models\Tv`.
 */
class TvSearch extends Tv
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 't_created', 't_updated', 'tmd_id', 'kp_id', 'r_kp', 'r_imdb', 'imdb_id', 'episode_run_time', 'popularity', 'is_action_adventure', 'is_animation', 'is_comedy', 'is_crime', 'is_documentary', 'is_drama', 'is_family', 'is_kids', 'is_mystery', 'is_reality', 'is_science_fiction_fantasy', 'is_soap', 'is_talk', 'is_war_politics', 'is_western'], 'integer'],
            [['first_air_date', 'title', 'orig_title', 'overview', 'external_ids', 'images', 'video'], 'safe'],
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
        $query = Tv::find();

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
            'r_kp' => $this->r_kp,
            'r_imdb' => $this->r_imdb,
            'imdb_id' => $this->imdb_id,
            'first_air_date' => $this->first_air_date,
            'episode_run_time' => $this->episode_run_time,
            'popularity' => $this->popularity,
            'is_action_adventure' => $this->is_action_adventure,
            'is_animation' => $this->is_animation,
            'is_comedy' => $this->is_comedy,
            'is_crime' => $this->is_crime,
            'is_documentary' => $this->is_documentary,
            'is_drama' => $this->is_drama,
            'is_family' => $this->is_family,
            'is_kids' => $this->is_kids,
            'is_mystery' => $this->is_mystery,
            'is_reality' => $this->is_reality,
            'is_science_fiction_fantasy' => $this->is_science_fiction_fantasy,
            'is_soap' => $this->is_soap,
            'is_talk' => $this->is_talk,
            'is_war_politics' => $this->is_war_politics,
            'is_western' => $this->is_western,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'orig_title', $this->orig_title])
            ->andFilterWhere(['like', 'overview', $this->overview])
            ->andFilterWhere(['like', 'external_ids', $this->external_ids])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'video', $this->video]);

        return $dataProvider;
    }
}
