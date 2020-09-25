<?php
/**
 * Created By Nika Gelashvili
 * Date: 23.09.20
 * Time: 17:25
 */

namespace app\modules\v1\users\controllers;


use app\modules\v1\users\resources\UserDepartmentResource;
use app\modules\v1\users\resources\UserResource;
use app\modules\v1\users\resources\UserRoleResource;
use app\rest\ActiveController;
use app\modules\v1\users\models\search\UserDepartmentSearch;
use app\rest\ValidationException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class EmployeeController extends ActiveController
{
    public $modelClass = UserDepartmentResource::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['index', 'create', 'delete'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'create', 'delete'],
                    //TODO roles
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['view'], $actions['index']);

        return $actions;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $userDepartmentSearch = new UserDepartmentSearch();
        return $userDepartmentSearch->search(\Yii::$app->request->get());
    }

    /**
     * @return array
     * @throws ValidationException
     * @throws \yii\db\Exception
     */
    public function actionUpdateUserData()
    {
        $request = \Yii::$app->request;
        $dbTransaction = \Yii::$app->db->beginTransaction();

        $user = UserResource::find()
            ->byId($request->post('id'))
            ->with(['userProfile', 'userDepartments'])
            ->one();

        if (!$user) {
            $dbTransaction->rollBack();
            throw new ValidationException(\Yii::t('app', 'Invalid params provided'));
        }

        $userProfile = $user->userProfile;

        if (!$user->load($request->post(), '') || !$user->save()) {
            $dbTransaction->rollBack();
            return $this->validationError($user->getFirstErrors());
        }

        if (!$userProfile->load($request->post(), '') || !$userProfile->save()) {
            $dbTransaction->rollBack();
            return $this->validationError($userProfile->getFirstErrors());
        }

        $user->updateRoles($request->post('roles'));
        $user->updateUserDepartments($request->post('userDepartments'), $dbTransaction);

        $dbTransaction->commit();
    }

    public function actionGetDropdown()
    {
        $data = [
            'userRoles' => [],
            'userPositions' => [],
        ];

        $data['userRoles'] = UserRoleResource::getUserRoles();

        $positionOptions = UserDepartmentResource::find()
            ->select(['position'])
            ->distinct()
            ->asArray()
            ->all();

        $data['userPositions'] = ArrayHelper::getColumn($positionOptions, 'position');

        return $data;
    }
}