<?php

namespace app\modules\v1\users\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * Remember me during 30 day
     */
    const REMEMBER_ME_DURATION_TIME = 3600 * 24 * 30;

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
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username or email'),
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember Me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword(string $attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getActiveUser();

            if (User::find()->byEmailOrUsername($this->username)->notActive()->one()) {
                $this->addError('password', Yii::t('app', 'Your account must be activated by admin'));
            }

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        $isLogin = Yii::$app->user->login($this->getActiveUser(), $this->rememberMe ? self::REMEMBER_ME_DURATION_TIME : 0);
        if (!$isLogin) {
            return false;
        }
        $user = Yii::$app->user->identity;
        $user->generateAccessToken();

        return $user->save();
    }

    /**
     * Finds active user by [[username]] or [[email]]
     *
     * @return User|null
     */
    public function getActiveUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()
                ->byEmailOrUsername($this->username)
                ->active()
                ->one();
        }

        return $this->_user;
    }
}
