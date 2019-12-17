<?php

namespace app\modules\api\controllers;

use app\controllers\BaseRestGuestController;
use app\models\ChangeBalanceModel;
use app\models\GetBalanceModel;
use Yii;
use yii\filters\VerbFilter;

/**
 * Rest controller for the `api` module
 */
class BalanceController extends BaseRestGuestController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'get' => ['get'],
                    'change' => ['post'],
                ],
            ],
        ]);
    }

    /**
     * Получение баланса
     * @return array
     */
    public function actionGet()
    {
        $model = new GetBalanceModel();
        $model->load(Yii::$app->request->get(), '');
        $result = $model->getResult();

        if (isset($result['errors'])) {
            Yii::$app->response->statusCode = 400;
        }

        return $result;
    }
    
    /**
     * Изменение баланса
     * @return array
     */
    public function actionChange()
    {
        $model = new ChangeBalanceModel();
        $model->load(Yii::$app->request->post(), '');
        $model->save();
        $result = $model->getResult();

        if (isset($result['errors'])) {
            Yii::$app->response->statusCode = 400;
        }

        return $result;
    }
}
