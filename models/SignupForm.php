<?php

namespace app\models;


use app\helpers\MailHelper;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class SignupForm
 *
 * @package app\models
 */
class SignupForm extends Model
{
    public $email;
    public $firstname;
    public $lastname;
    public $password;
    public $password_repeat;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname'], 'string', 'max' => 255],
            [['password'], 'string', 'min' => 6],
            ['email', 'filter', 'filter' => 'trim'],
            [['email', 'firstname', 'lastname'], 'required'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'E-mail'),
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'Password' => Yii::t('app', 'Password'),
        ];
    }

    /**
     * Signs user up.
     *
     * @param Invitation $invitation
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    public function signup(Invitation $invitation)
    {
        if (!$this->validate()) {
            return null;
        }
        $dbTransaction = Yii::$app->db->beginTransaction();
        // Save user
        $user = new User();
        $user->username = $this->email;
        $user->email = $this->email;
        $user->status = User::STATUS_INACTIVE;
        $user->setPassword($this->password);
        if (!$user->save()) {
            $dbTransaction->rollBack();
            throw new Exception("User was not saved for email $this->email");
        };

        // Save user profile
        $userProfile = new UserProfile();
        $userProfile->user_id = $user->id;
        $userProfile->first_name = $this->firstname;
        $userProfile->last_name = $this->lastname;
        if (!$userProfile->save()) {
            $dbTransaction->rollBack();
            throw new Exception("UserProfile was not saved for email \"$this->email\"");
        }

        // TODO Assign role to user

        // Update invitation object
        $invitation->use_date = time();
        $invitation->user_id = $user->id;
        $invitation->status = Invitation::STATUS_REGISTERED;
        if (!$invitation->save()) {
            $dbTransaction->rollBack();
            throw new Exception("Invitation was not updated. Token: $invitation->token");
        }

        MailHelper::acceptInvitation($invitation, $user);

        $dbTransaction->commit();
        return $user;
    }

}