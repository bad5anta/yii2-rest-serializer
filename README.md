# REST Serializer for Yii2

Serializer for responses with non-nested relations.

## Installation

Either run 

```
php composer.phar require --prefer-dist bad5anta/yii2-rest-serializer "*"
```

or add

```
"bad5anta/yii2-rest-serializer": "*"
```

to the require section of your composer.json.

## Usage

Use `bad5anta\ar\ActiveRecord` instead of default `yii\db\ActiveRecord`:

```
...
class Custom extends \bad5anta\ar\ActiveRecord {

    public static $joins = ['relationModels'];

    public static $dynamicFields = [
        'relationModels' => [
        	'primaryKey' => 'relationModelID'
        ],
    ];

    // or use extraFields for getting data via "expand" param
    public function fields() {
        return \bad5anta\ar\ArrayHelper::merge(
        	parent::fields(),
        	array_keys(static::$dynamicFields)
        );
    }
    ...

    public function getRelationModels() {
        return $this->hasMany(RelationModel::className(), ['customID' => 'customID'])->viaTable('custom2relationModel', ['relationModelID' => 'relationModelID']);
    }

```

and `bad5anta\ar\Serializer` instead of default `yii\rest\Serializer`:

```
...
class CustomController extends \bad5anta\ar\ActiveController {

    public $serializer = [
        'class' => '\bad5anta\ar\Serializer',
        'collectionEnvelope' => 'customs',
        'itemEnvelope' => 'custom', // for enveloping every single record
    ];
```

and add url rule to config from default Yii2 REST recipe.

Now visiting http://api.example.com/customs will give you answer:

```
{
    "customs": [
        {
            "customID": 1,
            "name": "Some name",
            "relationModels": [10, 12]
        },
        {
            "customID": 2,
            "name": "Some another name",
            "relationModels": [10, 11]
        }
    ],
    "relationModels": [
        {
            "relationModelID": 10,
            "name": "Some relation name"
        },
        {
            "customID": 11,
            "name": "Some another relation name"
        },
        {
            "customID": 12,
            "name": "Last relation name"
        }
    ]
}
```

It works with one-to-one relations either.

Be careful with cross relations: it may cause infinite loop run. To avoid this you can use `extraFields()` function instead
of `fields()` or unset `$dynamicFields` in controller before finding records.