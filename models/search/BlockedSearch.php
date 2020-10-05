<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Blocked;

/**
 * BlockedSearch represents the model behind the search form of `app\models\Blocked`.
 */
class BlockedSearch extends Blocked
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 't_created', 'kp_id', 'imdb_id'], 'integer'],
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
        $query = Blocked::find();

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
            'kp_id' => $this->kp_id,
            'imdb_id' => $this->imdb_id,
        ]);

        return $dataProvider;
    }
}
