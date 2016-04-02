<?php

namespace bad5anta\ar;

use Yii;
use yii\base\Arrayable;
use yii\base\Exception;
use yii\base\Model;
use yii\data\DataProviderInterface;
use bad5anta\ar\ActiveRecord;


class Serializer extends \yii\rest\Serializer
{
    public $itemEnvelope;


    /**
     * Serializes a data provider.
     * @param DataProviderInterface $dataProvider
     * @return array the array representation of the data provider.
     */
    protected function serializeDataProvider($dataProvider)
    {
        $models = $this->serializeModels($dataProvider->getModels());

        if (($pagination = $dataProvider->getPagination()) !== false) {
            $this->addPaginationHeaders($pagination);
        }

        if ($this->request->getIsHead()) {
            return null;
        } elseif ($this->collectionEnvelope === null) {
            return $models;
        } else {
            $result = $models;
            if ($pagination !== false) {
                return array_merge($result, $this->serializePagination($pagination));
            } else {
                return $result;
            }
        }
    }

    /**
     * Serializes a model object.
     * @param Arrayable $model
     * @return array the array representation of the model
     */
    protected function serializeModel($model)
    {
        $a = [];
        if ($this->request->getIsHead()) {
            return null;
        } else {
            list ($fields, $expand) = $this->getRequestedFields();
            if($model instanceof ActiveRecord){
                $toa = $model->toArrayEmber($fields, $expand);
                $model = $toa['data'];
                $a = $toa['relations'];
            }
            foreach ($a as $k => $item) {
                \bad5anta\ar\ArrayHelper::arrayUniqueMultiple($a[$k]);
            }
            return ArrayHelper::merge([
                $this->itemEnvelope => $model
            ],$a);
        }
    }

    /**
     * Serializes a set of models.
     * @param array $models
     * @return array the array representation of the models
     */
    protected function serializeModels(array $models)
    {
        list ($fields, $expand) = $this->getRequestedFields();
        $rel = [];
        $a = [];
        foreach ($models as $i => $model) {
            if ($model instanceof \bad5anta\ar\ActiveRecord) {
                $toa = $model->toArrayEmber($fields, $expand);
                $models[$i] = $toa['data'];
                array_push($rel, $toa['relations']);
            } elseif  ($model instanceof Arrayable) {
                $models[$i] = $model->toArray($fields, $expand);
            } elseif (is_array($model)) {
                $models[$i] = ArrayHelper::toArray($model);
            }
        }


        if($rel){
            foreach ($rel as $item) {
                foreach ($item as $key => $itemNested) {
                    if(!isset($a[$key])){
                        $a[$key] = [];
                    }
                    $a[$key] = ArrayHelper::merge($a[$key],$itemNested);
                }
            }
            foreach ($a as $k => $item) {
                \bad5anta\ar\ArrayHelper::arrayUniqueMultiple($a[$k]);
            }
        }

        if($this->collectionEnvelope){
            return ArrayHelper::merge([
                $this->collectionEnvelope => $models
            ],$a);
        }
        return $models;
    }
}