<?php

class ImageMigratingHelper
{
    const ZABBIX_SERVER_NAME                     = 'imgproc';
    //最近提交的新图片id
    const ZABBIX_KEY_NEW_LAST_MIGRATED_ID        = 'migrating.new.last.id';
    //新图片迁移个数
    const ZABBIX_KEY_NEW_MIGRATED_COUNT          = 'migrating.new.count';
    //新图片提交结果
    const ZABBIX_KEY_NEW_MIGRATED_SUBMIT_RESULT  = 'migrating.new.submit.result';
    //老图片迁移最新ID
    const ZABBIX_KEY_OLD_LAST_MIGRATED_ID        = 'migrating.old.last.id';
    //老图片迁移个数
    const ZABBIX_KEY_OLD_MIGRATED_COUNT          = 'migrating.old.count';
    //返回迁移成功的个数
    const ZABBIX_KEY_MIGRATED_SUCC_COUNT          = 'migrating.succ.count';
    //Qbox Etag获取错误个数
    const ZABBIX_KEY_QBOX_ETAG_ERROR_COUNT       = 'migrating.qbox.etag.error.count';

    public static $startTime  = 0;
    public static $endTime    = 0;


    private static function curTime()
    {
        $t = microtime();
        $t = explode(' ', $t);
        $t = $t[0]+$t[1];
        return $t;
    }

    /**
     * 记时器设置开始时间
     */
    public static function start()
    {
        self::$startTime    = self::curTime();
    }

    /**
     * 记时器设置结束时间并计算耗时
     */
    public static function end()
    {
        self::$endTime    = self::curTime();
        $lasts    = self::$endTime - self::$startTime;
        self::$startTime = self::$endTime = 0;
        return $lasts;
    }
}
