<?php

namespace bad5anta\ar\actions;

use Yii;

class OptionsAction extends \yii\base\Action
{
    /**
     * @var array the HTTP verbs that are supported by the collection URL
     */
    public $collectionOptions = ['GET', 'POST', 'HEAD', 'OPTIONS'];
    /**
     * @var array the HTTP verbs that are supported by the resource URL
     */
    public $resourceOptions = ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];


    /**
     * Responds to the OPTIONS request.
     * @param string $id
     */
    public function run($id = null)
    {
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
            Yii::$app->getResponse()->setStatusCode(405);
        }
        $options = $id === null ? $this->collectionOptions : $this->resourceOptions;
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Methods', implode(', ', $options));
    }
}
