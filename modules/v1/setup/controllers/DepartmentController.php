<?php
/**
 * User: zura
 * Date: 14.09.20
 * Time: 13:08
 */

namespace app\modules\v1\setup\controllers;


use app\modules\v1\setup\models\search\DepartmentSearch;
use app\modules\v1\setup\resources\DepartmentResource;
use app\rest\ActiveController;
use Yii;

/**
 * Class DepartmentController
 *
 * @author  Zura Sekhniashvili <zurasekhniashvili@gmail.com>
 * @package app\modules\v1\setup\controllers
 */
class DepartmentController extends ActiveController
{
    public $modelClass = DepartmentResource::class;

    /**
     * @return array
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     */
    public function actionIndex()
    {
        return (new DepartmentSearch())->search(Yii::$app->request->get());
    }
}
