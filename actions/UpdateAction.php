<?php

namespace bad5anta\ar\actions;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\debug\models\search\Debug;
use yii\debug\models\search\Log;
use yii\helpers\StringHelper;
use yii\web\ServerErrorHttpException;

class UpdateAction extends ActiveAction
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;


    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), lcfirst(StringHelper::basename(get_class($model))));
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
