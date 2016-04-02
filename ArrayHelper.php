<?php

namespace bad5anta\ar;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
	public static function arrayUniqueMultiple(&$array)
    {
        $newArray = [];
        foreach($array as $item){
            if(!in_array($item, $newArray))
                $newArray[] = $item;
        }
        $array = $newArray;
    }
}