<?php

define('WWW_DIR', realpath(__DIR__.'/../../../..'));

define('PINGUO_LIB', realpath(WWW_DIR.'/lib'));

set_include_path(get_include_path().':'.PINGUO_LIB);

YiiBase::setPathOfAlias('yii-ext', PINGUO_LIB.'/yii-ext');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath' => realpath(WWW_DIR.'/innertools/protected'),
	'runtimePath' => realpath(WWW_DIR.'/runtime/innertools'),
	'name' => 'Camera360 InnerTools',
	'sourceLanguage' => 'en_us',
	'language' => 'zh_cn',

	// preloading 'log' component
	'preload' => array('log'),

	// autoloading model and component classes
	'import' => array(
		'application.components.*',
		'application.helpers.*',
		'application.models.*',
		'application.models.data.*',
		'application.models.logic.*',
		'application.models.form.*',
		'yii-ext.components.*',
		'yii-ext.helpers.*',
		'yii-ext.models.*',
		'yii-ext.models.data.*',
		'yii-ext.models.logic.*',
	),

	'modules' => array(
	),

	// application components
	'components' => array(
		'user' => array(
			'allowAutoLogin' => true,
		),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
			'rules' => array(
				'<controller:\w+>/<action:\w+>/<id:\w{32}>' => '<controller>/<action>',
				'<id:\w{32}>' => 'audioImage/index',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			),
		),
		'dbTools' => array(
			'class' => 'MongoConnection',
			'server' => 'mongodb://cloud-bak1:27017',
			'options' => array(
				'connect' => false,
				'readPreference' => MongoClient::RP_PRIMARY,
				'connectTimeoutMS' => 1000,
			),
		),
		'cacheSession' => array(
			'class' => 'RedisCache',
			'servers' => array(
				array('host' => 'cloud-bak1', 'port' => 6379),
			),
			'db' => 0,
			'options' => array(
			),
		),
		'errorHandler' => array(
			//'errorAction' => 'site/error',
		),
		'log' => array(
            'class' => 'CLogRouter',
			'routes' => array(
				'file'  =>  array(
					'class' => 'CFileLogRoute',
					'levels' => 'error, warning, info',
					'maxFileSize' => 10 * 1024,
					'maxLogFiles' => 1024,
				),
				'profile' => array(
					'class' => 'CFileLogRoute',
					'levels' => 'profile',
					'maxFileSize' => 10 * 1024,
					'maxLogFiles' => 100,
					'logFile' => 'profile.log',
				),
			),
		),
		'session' => array(
			'class' => 'CCacheHttpSession',
			'cacheID' => 'cacheSession',
			'timeout' => 8 * 3600,
		),
		'uploader'=>array(
			'class'=>'Upload'
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params' => array(
	),
);
