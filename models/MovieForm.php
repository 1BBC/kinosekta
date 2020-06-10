<?php

namespace app\models;

use Yii;
use yii\base\Model;

class MovieForm extends Model
{
    public $tmd_id;
    public $kp_id;
    public $imdb_id;
    public $type;
    public $year;
    public $runtime;
    public $title;               
    public $orig_title;
    public $tagline;
    public $overview;
    public $budget;
    public $revenue;
    public $production_countries;
    public $external_ids;
    public $similar_movies;

    public $is_action;
    public $is_adventure;
    public $is_animation;
    public $is_comedy;
    public $is_crime;
    public $is_documentary;
    public $is_drama;
    public $is_family;
    public $is_fantasy;
    public $is_history;
    public $is_horror;
    public $is_music;
    public $is_mystery;
    public $is_romance;
    public $is_science_fiction;
    public $is_tv_movie;
    public $is_thriller;
    public $is_war;
    public $is_western;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
