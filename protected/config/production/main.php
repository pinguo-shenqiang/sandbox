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
		'dbCloud' => array(
			'class' => 'MongoConnection',
			'server' => 'mongodb://wws11:27197,wws17:27198,wws13:27199',
			'options' => array(
				'connect' => false,
				'readPreference' => MongoClient::RP_PRIMARY,
				'connectTimeoutMS' => 1000,
			),
		),
		'dbCloudLogBackup' => array(
			'class' => 'MongoConnection',
            'server' => 'mongodb://wws18:27211,wws20:27212',
            'options' => array(
                    'replicaSet' => 'rs1',
                    'connect' => false,
                    'readPreference' => MongoClient::RP_SECONDARY,
                    'readPreferenceTags' => array('use:backup'),
                    'connectTimeoutMS' => 1000,
            ),
		),
		'dbFace' => array(
			'class' => 'MongoConnection',
			'server' => 'mongodb://wws19:27311,wws12:27312',
			'options' => array(
				'connect' => false,
				'readPreference' => MongoClient::RP_PRIMARY,
				'connectTimeoutMS' => 1000,
				'replicaSet' => 'faceRs1',
			),
		),
		'dbPhotoMeta' => array(
			'class' => 'MongoConnection',
			'server' => 'mongodb://wws19:27511,wws12:27512',
			'options' => array(
				'connect' => false,
				'readPreference' => MongoClient::RP_PRIMARY,
				'connectTimeoutMS' => 1000,
				'replicaSet' => 'photoMetaRs1',
			),
		),
		'dbImgProc' => array(
			'class' => 'MongoConnection',
			'server' => 'mongodb://wws19:27511,wws12:27512',
			'options' => array(
				'connect' => false,
				'readPreference' => MongoClient::RP_PRIMARY,
				'connectTimeoutMS' => 86400000,
                'socketTimeoutMS'=>86400000,
				'replicaSet' => 'photoMetaRs1',
			),
		),
		'dbFaceMQ' => array(
			'class' => 'RedisConnection',
			'servers' => array(
				array('host' => 'wws15', 'port' => 6211),
			),
			'db' => 1,
			'options' => array(
			),
		),
		'dbFaceRedisDB' => array(
			'class' => 'RedisConnection',
			'servers' => array(
				array('host' => 'wws15', 'port' => 6211),
			),
			'db' => 2,
			'options' => array(
			),
		),
		'dbSignatureMQ' => array(
			'class' => 'RedisConnection',
			'servers' => array(
				array('host' => 'wws15', 'port' => 6211),
			),
			'db' => 3,
			'options' => array(
			),
		),
		'dbSeparateMQ' => array(
			'class' => 'RedisConnection',
			'servers' => array(
				array('host' => 'cloud-stat1', 'port' => 6411)
			),
			'db' => 1,
			'options' => array(
			)
		),
		'dbAudioImageMQ' => array(
			'class' => 'RedisConnection',
			'servers' => array(
				array('host' => 'cloud-stat1', 'port' => 6411)
			),
			'db' => 2,
			'options' => array(
			)
		),
		'cacheSession' => array(
			'class' => 'RedisCache',
			'servers' => array(
				array('host' => 'wws5', 'port' => 6111),
				array('host' => 'wws7', 'port' => 6131),
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
		'faceBucket' => 'face',
		'photoBucket' => 'cdn.360in.com',
		'faceDetectService' => array(
			'servers' => array(
				array('host' => '118.26.231.139', 'port' => 9090),
			),
			'sendTimeout' => 60000,
			'recvTimeout' => 600000,
			'debug' => false
		),
		'faceRecognizeService' => array(
			'servers' => array(
				array('host' => 'cloud-imgpro3', 'port' => 9090),
			),
			'sendTimeout' => 60000,
			'recvTimeout' => 600000,
			'debug' => false
		),
		'signatureSignService' => array(
			'servers' => array(
				'beijing' => array('host' => 'cloud-imgpro1', 'port' => 9090),
				'ningbo' => array('host' => 'cloud-imgpro4', 'port' => 9090),
				'zhangmutou' => array('host' => 'cloud-imgpro5', 'port' => 9090),
			),
			'sendTimeout' => 10000,
			'recvTimeout' => 60000,
			'debug' => false
		),
		'signatureSimilarService' => array(
			'servers' => array(
				array('host' => 'wws16', 'port' => 9090),
			),
			'sendTimeout' => 10000,
			'recvTimeout' => 10000,
			'debug' => false
		),
		'signatureSameService' => array(
			'servers' => array(
				array('host' => 'wws16', 'port' => 9090),
			),
			'sendTimeout' => 10000,
			'recvTimeout' => 10000,
			'debug' => false
		),
		'audioimageService' => array(
			'servers' => array(
				'beijing' => array('host' => 'cloud-imgpro1', 'port' => 9091),
				'ningbo' => array('host' => 'cloud-imgpro4', 'port' => 9091),
				'zhangmutou' => array('host' => 'cloud-imgpro5', 'port' => 9091),
			),
			'sendTimeout' => 10000,
			'recvTimeout' => 60000,
			'debug' => false
		),
		'signatureIdentityVersion' => '1.0',
		'signatureSimilarityVersion' => '1.0',
		'adminEmail' => 'wangjiajun@camera360.com',
		'zabbixServer' => array(
			'host' => 'zabbixserver.camera360.com',
			'port' => 10051
		),
		'zabbixSenderPath' => '/home/worker/zabbix/bin/zabbix_sender',
		'siteUrl'	=> 'http://imgproc.camera360.com/',
		'uploadTempDir' => '/home/worker/data/nfs/imgproc/upload/',
		'downloadTempDir' => '/home/worker/data/www/runtime/imgproc/download/',
		'resourceVersion' => '20130930135634',
		'cloudImgUrl' => 'https://iovip.qbox.me/cdn.360in.com/',
		'qbox' => array(
			'audioimage' => array(
				'imagebucket' => 'audioimage_image',
				'audiobucket' => 'audioimage_audio',
			),
		),
		'apps' => array(
			'62704b1f9c3a8d5e' => array(
				'secret' => 'qBueSx_jJrDlgxHoTq1DZioRh0MHYvre'
			),
			'532acb06794e1df8' => array(
				'secret' => 'Lri2~S8wv823JtwAk8ZGi6Q3Am9uaBXQ'
			),
			'9ab6743c205e8f1d' => array(
				'secret' => 'nyTdT9dvI~5N0SfNqP55vld2I0GDHp~A'
			),
			'e4dc291b870a536f'=>array(
					'secret'  => 'P0Rqrm4OmlNXZF_yKzwWoqiBhEK8Gpgl'
			),
		),
		'virtualUserSecret' => 'v$V#nB(AjOEQ%@Y0%AuIdSn#(mKHVz', 
		'imageMigrateStartDate' => '2013-11-05', 
	),
);
