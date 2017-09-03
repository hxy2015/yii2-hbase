
Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist hxy2015/yii2-hbase
```

or add

```json
"hxy2015/yii2-hbase": "~1.0"
```

to the require section of your composer.json.

Configuration
-------------

To use this extension, you have to configure the Connection class in your application configuration:

```php
return [
    //....
    'components' => [
        'hbase' => [
            'class' => 'hxy2015\hbase\Connection',
            'host' => 'localhost',
            'port' => '8080',
        ],
    ]
];
```
