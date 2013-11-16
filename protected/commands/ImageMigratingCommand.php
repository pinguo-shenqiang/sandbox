<?php
/**
 * 提交新图片到酷盘
 * @author wangzhong@camera360.com
 * @version  2013-9-13
 *
 */
class ImageMigratingCommand extends CConsoleCommand
{
    public function init()
    {
        RedisConnection::$reuseConnection = false;
    }
    protected function beforeAction($action, $params)
    {
        $ret = parent::beforeAction($action, $params);
        Yii::beginProfile($this->getName() . '.' . $action);
        return $ret;
    }
    protected function afterAction($action, $params, $exitCode = 0)
    {
        Yii::endProfile($this->getName() . '.' . $action);
        return parent::afterAction($action, $params, $exitCode);
    }
    /**
     * 提交新图片到酷盘
     * @param boolean $daemon
     */
    public function actionSubmitImagesToBeMigrating($daemon = false, $numberOfWorkers = 1)
    {
        $handler = function ()
        {
            $imageMigratingLogic = new ModelLogicImageMigrating();
            $imageMigratingLogic->submitImagesToBeMigrating();
        };
        if ($daemon) {
            ProcessHelper::masterWorkers($handler, $numberOfWorkers, 'submit_images_to_be_migrating');
        } else {
            try {
                $handler();
            } catch (ModelLogicNoTaskException $e) {
                echo "no task\n";
                return 0;
            } catch (Exception $e) {
                echo "failed: " . $e->getMessage() . "\n";
                return - 1;
            }
            echo "succeed.\n";
            return 0;
        }
    }
    /**
     * 初始化获取老站片接口数据
     */
    public function actionInitToBeMigrating()
    {
        $startDate = Yii::app()->params['imageMigrateStartDate'];
        $oldNewBoundary = dechex(strtotime($startDate)) . '0000000000000000';
        $firstBoundary = "100000000000000000000000";
        $modelImageMigrating = new ModelDataImageMigrating();
        $lastDocs = $modelImageMigrating->findOne(
            array('_id' => new MongoId($firstBoundary))
        );
        if (empty($lastDocs)) {
            $initData = array(
            	'_id' => new MongoId($firstBoundary), 
                'status' => 1,
            	'create_time' => new MongoDate()
            );
            $modelImageMigrating->add($initData);
            echo "inited\n";
        }  else {
            echo "alreay inited\n";
        }
    }
}