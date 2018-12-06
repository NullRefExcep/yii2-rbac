<?php

namespace nullref\rbac\search;

use nullref\rbac\ar\AuthRule;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AuthRuleSearch represents the model behind the search form about `nullref\ar\AuthRule`.
 */
class AuthRuleSearch extends AuthRule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['data',], 'safe'],
        ];
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
        $query = parent::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
