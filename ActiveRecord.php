<?php

namespace bad5anta\ar;

use Yii;
use yii\web\Link;
use yii\web\Linkable;

class ActiveRecord extends \yii\db\ActiveRecord
{
    public static $joins = [];

    public static $dynamicFields = [

    ];

    public function toArrayEmber(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [];
        $relations = [];
        $dynamics = static::$dynamicFields;
        foreach ($this->resolveFields($fields, $expand) as $field => $definition) {
            if (in_array($field, array_keys($dynamics))) {
                $def = $this->$definition;
                if (is_array($def) && $def) {
                    $def = ArrayHelper::toArray($def);
                    $data[$field] = ArrayHelper::getColumn($def, $dynamics[$field]['primaryKey']);
                    $relations[$field] = $def;
                } else if (is_object($def)) {
                    $def = ArrayHelper::toArray($def);
                    $data[$field] = $def[$dynamics[$field]['primaryKey']];
                    $relations[$field][] = $def;
                } else {
                    $data[$field] = [];
                }
            } else {
                $data[$field] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $field);
            }
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }


        $response = [
            'data' => $data,
            'relations' => $relations,
        ];

        return $response;
    }
}