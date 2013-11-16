<?php

class ModelLogicImageMigrating 
{    
    /**
     * 获取下一批待迁移相片
     * @param boolean $new 迁移新相片还是老相片
     * @param string $count 单批提交多少张
     * @return array 待迁移相片列表
     */
    public function getImagesToBeMigrating($new = true, $count = 1000)
    {
        $count = $count > 1000 ? 1000 : $count;
        // 新老数据分界线
        $startDate = Yii::app()->params['imageMigrateStartDate'];
        $oldNewBoundary = dechex(strtotime($startDate)).'0000000000000000';
        
        $modelCloudPicture = new ModelDataCloudPicture();
        $modelImageMigrating = new ModelDataImageMigrating();
        $query = array();
        if ($new) {
            // 读取新上传相片，也就是ID位于[$oldNewBoundary, "fefefefc0000000000000000")区间的相片
            $lastDocs = $modelImageMigrating->query(array(
                '_id' => array(
                    '$gte' => new MongoId($oldNewBoundary),
                    '$lt' => new MongoId('fefefefc0000000000000000')
                )
            ), array(), array('_id' => -1), 1);
            if ($lastDocs) {
                // 从上次处理完的位置开始
                $lastDoc = array_shift($lastDocs);
                $query['_id'] = array(
                    '$gt' => $lastDoc['_id'],
                    '$lt' => new MongoId('fefefefc0000000000000000')
                );
            } else {
                // 第一次处理
                $query['_id'] = array(
                    '$gte' => new MongoId($oldNewBoundary),
                    '$lt' => new MongoId('fefefefc0000000000000000')
                );
            }
        } else {
            // 读取下一批老相片，也就是ID位于["fefefefc0000000000000000", 正无穷大)和[0, $oldNewBoundary)区间的相片
            $lastDocs = $modelImageMigrating->query(array(
                '_id' => array(
                    '$lt' => new MongoId($oldNewBoundary)
                )
            ), array(), array('_id' => -1), 1);
            if ($lastDocs) {
                //[0, $oldNewBoundary) 处理完成之后开始处理["fefefefc0000000000000000", 正无穷大)区间
                $lastDocsCopy = $lastDocs;
                $lastDoc = array_shift($lastDocsCopy);
                $query['_id'] = array(
                    '$gt' => $lastDoc['_id'],
                    '$lt' => new MongoId($oldNewBoundary)
                );
                $remain = $modelCloudPicture->query($query, array(), array('_id' => 1),1);
                $current = $modelCloudPicture->query(array("_id"=>$lastDoc['_id']));
                //[0, $oldNewBoundary)已经处理完成且没有查询错误才会继续处理[0, $oldNewBoundary)区间
                if (empty($remain) && $current) {
                    unset($lastDocs);
                }
            }
            if ($lastDocs) {
                // 从上次处理完的位置开始，位于[0, $oldNewBoundary)区间
                $lastDoc = array_shift($lastDocs);
                 $query['_id'] = array(
                    '$gt' => $lastDoc['_id'],
                    '$lt' => new MongoId($oldNewBoundary)
                );
            } else {
                $lastDocs = $modelImageMigrating->query(array(
                    '_id' => array(
                        '$gte' => new MongoId('fefefefc0000000000000000')
                    )
                ), array(), array('_id' => -1), 1);
                if ($lastDocs) {
                    // 从上次处理完的位置开始，位于["fefefefc0000000000000000", 正无穷大)区间
                    $lastDoc = array_shift($lastDocs);
                    $query['_id'] = array(
                        '$gt' => $lastDoc['_id']
                    );
                } else {
                    // 第一次处理，先处理["fefefefc0000000000000000", 正无穷大)区间
                    $query['_id'] = array(
                        '$gte' => new MongoId('fefefefc0000000000000000')
                    );
                }
            }
        }
        //@todo for test
        if (isset($_GET['userId'])) {
            $query['userId'] = new MongoId($_GET['userId']);
            //unset($query['_id']);
            //$count = 500;
        }
        $cursor = $modelCloudPicture->find($query)
            ->sort(array('_id' => 1))
            ->timeout(0);
        
        $migratingDocs = array();
        $etagErrCnt = 0;
        $secret = Yii::app()->params['virtualUserSecret'];
        foreach ($cursor as $doc) {
            // 得到相片上传者的virtualId
            $userId = (string)$doc['userId'];
            //userId做对称加密
            $virtualId = SecurityHelper::encryptDESBase64($userId, $secret);
            // 如果为有声相片，设置标识
            if (isset($doc['ptype']) && $doc['ptype'] == 104) {
                $hasAudio = true;
            } else {
                $hasAudio = false;
            }
            $migratingDocs[] = array(
                '_id' => $doc['_id'],
                'file_id' => $doc['remoteId'],
                'user' => $doc['userId'],
                'virtual_id' => $virtualId,
                'status' => 0,
                'has_audio' => $hasAudio,
                'create_time' => new MongoDate(),
                'crc32' => $doc['crc32'],
            );
            if (count($migratingDocs) >= $count) {
                break;
            }
        }
        unset($doc);
        // 写入迁移数据库 
        if (!empty($migratingDocs)) {
            $modelImageMigrating->batchAdd($migratingDocs, true, 10000);
        } 
        return $migratingDocs;
    }
    
    /**
     * 减小getImagesToBeMigrating的加锁维度
     * @param array $fileIdArr array(fileId)
     */
    public function getEtagAndSave(array $fileIdArr) {
        if (empty($fileIdArr)) {
            return array();
        }
        //从qbox获取图片etag校验码
        $etagRets = Qbox6Helper::rsBatchStat($fileIdArr);
        $modelImageMigrating = new ModelDataImageMigrating();
        $etagErrCnt = $succCnt = $failCnt = 0;
        $result = array();
        foreach ($fileIdArr as $k => $fileId) {
            if (empty($etagRets[$k]['data']['hash'])) {
                $etagErrCnt++;
            }
            $etag = @$etagRets[$k]['data']['hash'];
            $result[$fileId] = $etag;
            if ($etag) {
                $bret = $modelImageMigrating->modify(
                    array('file_id' => $fileId),
                    array(
                        'etag' => $etag,
                    )
                );
                $bret ? $succCnt++ : $failCnt++;
            }
        }
        //上报etagErrCnt;
        if ($etagErrCnt > 0) {
            ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_QBOX_ETAG_ERROR_COUNT, $etagErrCnt);
        }
        return $result;
    }
    
    /**
     * 提交待迁移相片到酷盘
     * @param string $count 单次提交多少张
     * @return int 实际提交的相片数
     */
    public function submitImagesToBeMigrating($count = 50)
    {
        //因为是需要同步的操作，这个地方需要加锁，需要循环取
        while (TRUE) {
            $lock = ConcurrencyControlHelper::lock();
            if ($lock) {
                $migratingDocs = $this->getImagesToBeMigrating(true, $count);
                ConcurrencyControlHelper::release();
                break;
            } else {
                sleep(rand(0.5, 1));
                Yii::log("waiting lock", CLogger::LEVEL_INFO);
            }
        }
        if (empty($migratingDocs)) {
            throw new ModelLogicNoTaskException("no task");
        }
        $fileIdArr = array();
        foreach ($migratingDocs as $doc) {
            $fileIdArr[] = $doc['file_id'];
        }
        //获取etag
        $etagArr = $this->getEtagAndSave($fileIdArr);
        foreach ($migratingDocs as &$doc) {
            $doc['etag'] = $etagArr[strval($doc['file_id'])];
        }
        unset($doc);
        Yii::log(__FUNCTION__." fetch picture count: ".count($migratingDocs)." first one: ".json_encode(@$migratingDocs[0]), CLogger::LEVEL_INFO);
        ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_NEW_LAST_MIGRATED_ID, (string)@$migratingDocs[0]['_id']);
        ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_NEW_MIGRATED_COUNT, count($migratingDocs));
        // 发送待迁移相片列表给酷盘
        $modelKanbox = new ModelDataKanbox();
        $ret = $modelKanbox->sendPhotosToBeMigrated($migratingDocs);
        Yii::log(__FUNCTION__." submit kupan picture count: ".$ret, CLogger::LEVEL_INFO);
        ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_NEW_MIGRATED_SUBMIT_RESULT, $ret);
        return $ret;
    }
    
    /**
     * 保存迁移结果
     * @param array $result
     * array(
     *   array(
     *     id => ..., 相片ID
     *     status => ..., 迁移结果，1 – 为成功，2 – 失败。原图下载成功就算成功
     *   ),
     *   ...
     * )
     * @return boolean
     */
    public function saveResultOfMigrating($result)
    {
        $modelImageMigrating = new ModelDataImageMigrating();
        $succCnt = $failCnt = 0;
        foreach ($result as $doc) {
            $bret = $modelImageMigrating->modify(
                array('file_id' => strval(($doc['id']))),
                array(
                    'status' => (int) $doc['status'],
                    'finished_time' => new MongoDate(),
                )
            );
            $bret ? $succCnt++ : $failCnt++;
        }
        return $succCnt;
    }
    
    private function _logProfile($label, $profile)
    {
        $durations = array();
        foreach ($profile as $item) {
            $durations[$item['name']] = $item['duration'];
        }
        Yii::log("$label: total-".array_sum($durations)." detail-".json_encode($durations), 
            CLogger::LEVEL_PROFILE);
    }
}