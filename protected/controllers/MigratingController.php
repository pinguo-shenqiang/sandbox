<?php
/**
 * 照片迁移相关流程
 * @author wangzhong@camera360.com
 * @version 2013-9-13
 *
 */
class MigratingController extends Controller
{
    
    protected function ipRules() {
        $auth = FALSE;
        $clientIp = Yii::app()->request->userHostAddress;
        $allowIps = array('127.0.0.1','182.148.112.202','112.124.5.195','119.6.200.164');
        if (in_array($clientIp, $allowIps)) {
            $auth = TRUE;
        }
        if (!$auth) {
            $ipArr = explode(".", $clientIp);
            array_pop($ipArr);
            if (implode(".", $ipArr) == "42.120.145") {
                $auth = TRUE;
            }
        }
        return $auth;
    }
    
    /**
     * 记录日志
     */
    private function wLog($content, $method)
    {
        Yii::log($content, 'error', 'application.controllers.migrating.' . $method);
        exit();
    }
    /**
     * echo结果
     * 该函数不会终止程序
     */
    private function echoResult($data = null, $error = '', $errno = 0)
    {
        $this->layout = false;
        header('Content-type: application/json');
        echo json_encode(array('data' => $data, 'error' => $error, 'errno' => $errno));
    }
    /**
     * 酷盘向C360提交新相片迁移结果
     * @param JSON:[
     * {
                id: …, // 相片ID
                status: …, // 迁移结果，1 – 为成功，2 – 失败。原图下载成功就算成功。
            },
     * ]
     */
    public function actionMigrated()
    {
        //ip filter
        if (!$this->ipRules()) {
            $this->echoResult(null,'current ip is not allowed to access this api',-1);
            Yii::log("unauth ip visitor ".Yii::app()->request->userHostAddress, CLogger::LEVEL_WARNING);
            return TRUE;
        }
        $succCnt = 0;
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $data = $GLOBALS['HTTP_RAW_POST_DATA'];
            $imageMigratedArray = json_decode($data, TRUE);
            if (! is_array($imageMigratedArray) || empty($imageMigratedArray)) {
                $errno = -1;
                $error = "empty data";
            } else {
                //@todo 检测数据合法性
                $imageMigratingLogic = new ModelLogicImageMigrating();
                $succCnt = $imageMigratingLogic->saveResultOfMigrating($imageMigratedArray);
                $errno = 0;
                $error = "succ";
                ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_MIGRATED_SUCC_COUNT, count($imageMigratedArray));
                Yii::log(__FUNCTION__." got migrated: ".$succCnt ." count: ".count($imageMigratedArray), CLogger::LEVEL_INFO);
            }
        } else {
            $errno = -1;
            $error = "empty data";
        }
        $this->echoResult($succCnt, $error, $errno);
    }
    
    /**
     * 向酷盘提供老照片迁移列表
     */
    public function actionToBeMigrated() {
        //ip filter
        if (!$this->ipRules()) {
            $this->echoResult(null,'current ip is not allowed to access this api',-1);
            Yii::log("unauth ip visitor ".Yii::app()->request->userHostAddress, CLogger::LEVEL_WARNING);
            return TRUE;
        }
        $imageMigratingLogic = new ModelLogicImageMigrating();
        //因为是需要同步的操作，这个地方需要加锁，需要循环取
        while (TRUE) {
            $lock = ConcurrencyControlHelper::lock();
            if ($lock) {
                $imagesToBeMigratedArray = $imageMigratingLogic->getImagesToBeMigrating(false, 50);
                ConcurrencyControlHelper::release();
                break;
            } else {
                sleep(rand(0.5, 1));
                Yii::log("waiting lock", CLogger::LEVEL_INFO);
            }
        }
        $fileIdArr = array();
        foreach ($imagesToBeMigratedArray as $doc) {
            $fileIdArr[] = $doc['file_id'];
        }
        //获取etag
        $etagArr = $imageMigratingLogic->getEtagAndSave($fileIdArr);
        $idcs = QboxHelper::idcInfoMulti('cdn.360in.com', $fileIdArr);
        $photos = array();
        foreach ($imagesToBeMigratedArray as $imagesToBeMigrated) {
            $photo = array();
            
            $fileId = $imagesToBeMigrated['file_id'];
            if (isset($idcs[$fileId])) {
                $original = PhotoHelper::urlByIdc($fileId, $idcs[$fileId]);
            } else {
                $original = PhotoHelper::url($fileId);
            }
            $photo['id'] = (string)$imagesToBeMigrated["_id"];
            $photo['file_id'] = $fileId;
            $photo['virtual_id'] = $imagesToBeMigrated['virtual_id'];
            $photo['original'] = $original;
            $photo['etag'] = $etagArr[$photo['file_id']];
            if ($imagesToBeMigrated['has_audio']) {
                $photo['audio'] = PhotoHelper::audioUrl($imagesToBeMigrated['file_id']);
            }
            $photos[] = $photo;
        }
        Yii::log(__FUNCTION__." to be migrated count: ".count($photos)." first one ".@json_encode($photos[0]), CLogger::LEVEL_INFO);
        ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_OLD_LAST_MIGRATED_ID, (string)@$photos[0]['id']);
        ZabbixHelper::sendItem(ImageMigratingHelper::ZABBIX_SERVER_NAME, ImageMigratingHelper::ZABBIX_KEY_OLD_MIGRATED_COUNT, count($photos));
        $this->echoResult($photos,'succ',0);
    }
    
    public function actionTestMigrated() {
        $result = array();
        $result[] = array('id'=>'7f5007da37c9463f0f020fe01b8e2c91','status'=>1);
        $result[] = array('id'=>'4849ea8f939d905c4800d843ddd03596','status'=>1);
        $data = json_encode($result);
        $ch = curl_init();
        $url = "http://imgproctest.camera360.com/migrating/migrated";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        	"Content-Type: application/json; charset=utf-8", 
        	"Accept: application/json")
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);
        print_r($result);exit;
    }
    
    /**
     * 获取相关信息
     */
    public function actionStatus() {
        //ip filter
        if (!$this->ipRules()) {
            $this->echoResult(null,'current ip is not allowed to access this api',-1);
            Yii::log("unauth ip visitor ".Yii::app()->request->userHostAddress, CLogger::LEVEL_WARNING);
            return TRUE;
        }
        $startDate = Yii::app()->params['imageMigrateStartDate'];
        $oldNewBoundary = dechex(strtotime($startDate)).'0000000000000000';
        $modelImageMigrating = new ModelDataImageMigrating();
        $modelCloudPicture = new ModelDataCloudPicture();
        $lastDocs = $modelImageMigrating->query(array(
            '_id' => array(
                '$gte' => new MongoId($oldNewBoundary),
                '$lt' => new MongoId('fefefefc0000000000000000')
            )
        ), array(), array('_id' => -1), 1);
        $lastDoc = array_shift($lastDocs);
        echo "current process id ".$lastDoc['_id']." time ".date("Y-m-d H:i:s", $lastDoc["_id"]->getTimestamp())."\n";
        //新照片堆积量
        $count = $modelCloudPicture->count(array(
        	'_id' => array(
                '$gte' => $lastDoc["_id"],
                '$lt' => new MongoId('fefefefc0000000000000000')
            )
        ));
        echo "current accumulation: ".$count."\n";
    }
}
