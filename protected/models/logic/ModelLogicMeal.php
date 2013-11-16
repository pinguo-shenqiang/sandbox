<?php

class ModelLogicMeal
{    
    protected $token = '';
    
    //protected $groups = array('3195493'=>'Test地址', '3016675'=>'安卓');
    protected $groups = array(
    	'3016675' => array(
            'name' => '安卓',
            'ownerUid' => '1506640224',
            'reportMail' => array(
                'tanbingjie@camera360.com',
                'zengxiaojuan@camera360.com',
            ),
        ),
        '3043601' => array(
            'name' => '云组',
            'ownerUid' => '1506639682',
            'reportMail' => array(
                'hehailin@camera360.com',
            ),
        ),
    );
    
    protected $reportMail = array('wangzhong@camera360.com', 'konglingjun@camera360.com');
    
    public function __construct() {
        $tokenData = new ModelDataYammerToken();
        $token = $tokenData->query(array(), array(), array("_id"=>-1),1);
        $token = array_pop($token);
        $token = $token['token'];
        if (empty($token)) {
            $this->error("token is null");
        }
        $this->token = $token;
    }
    
    protected function getGroupToken($groupId) {
        $ownerUid = $this->groups[$groupId]['ownerUid'];
        $tokenData = new ModelDataYammerToken();
        $token = $tokenData->query(array('user_id'=>$ownerUid), array(), array("_id"=>-1),1);
        $token = array_pop($token);
        $token = $token['token'];
        if (empty($token)) {
            $this->error("group $groupId ".$this->groups[$groupId]['name']." owner: $ownerUid token is null");
        }
        $this->token = $token;
    }
    
    public function order() {
        //检测周末
        $weekend = date("l");
        if (in_array($weekend, array('Sunday', 'Saturday'))) {
            return FALSE;
        }
        $mealPublish = new ModelDataMealPublish();
        $date = date("Y-m-d");
        //检测今日是否已经发送
        $publishData = $mealPublish->query(array('date'=>$date));
        $publishedDate = array();
        foreach ($publishData as $v) {
            $publishedDate[$v['groupId']] = $v['date'];
        }
        $results = array();
        foreach ($this->groups as $group => $value) {
            if (isset($publishedDate[$group])) {
                echo "group $group published at ".$publishedDate[$group]."\n";
                continue;
            }
            $token = $this->getGroupToken($group);
            $ret = YammerHelper::postMessage($token, "亲们，今天订饭吗？ 回复'+1'自动统计。下午2点半截止哦。  ".date("Y-m-d H:i:s"), $group);
            if (empty($ret['messages'][0])) {
                $this->error("message publish error in group $group");
            }
            $message = $ret['messages'][0];
            $result = array();
            $result['messageId'] = $message['id'];
            $result['senderId'] = $message['sender_id'];
            $result['time'] = date("Y-m-d H:i:s", strtotime($message['created_at']));
            $result['date'] = date("Y-m-d", strtotime($message['created_at']));
            $result['groupId'] = $message['group_id'];
            $result['message'] = $message['body']['plain'];
            $result['threadId'] = $message['thread_id'];
            $bret = $mealPublish->add($result);
            if (!$bret) {
                $this->error("publish insert db error".print_r($result, TRUE));
            }
            $results[] = $result;
        }
        print_r($results);
    }
    
    
    public function report($abort = FALSE){
        //检测周末
        $weekend = date("l");
        if (in_array($weekend, array('Sunday', 'Saturday'))) {
            return FALSE;
        }
        $mealPublish = new ModelDataMealPublish();
        $date = date("Y-m-d");
        $publishData = $mealPublish->query(array('date'=>$date));
        $reportIndex = array();
        foreach ($this->groups as $groupId => $value) {
            foreach ($publishData as $v) {
                if ($v['groupId'] == $groupId) {
                    $reportIndex[$groupId] = $v;
                    break;
                }
            };
            if (empty($reportIndex[$groupId])) {
                $this->error("group $groupId has not published order message yet!");
            }
        }
        $reportResult = array();
        foreach ($reportIndex as $groupId => $publish) {
            $threadId = $publish['threadId'];
            $messageId = $publish['messageId'];
            $url = "https://www.yammer.com/api/v1/messages/in_thread/$threadId.json";
            $threadIdInfo = YammerHelper::getUrl($this->token, $url);
            if (empty($threadIdInfo['messages']) || empty($threadIdInfo['references'])) {
                $this->error("report error, empty message or references");
                continue;
            }
            $messages = $threadIdInfo['messages'];
            $messages = array_reverse($messages);
            $references = $threadIdInfo['references'];
            $user = $orders = array();
            foreach ($references as $v) {
                $user[$v['id']] = $v;
            }
            foreach ($messages as $v) {
                if ($v['id'] == $messageId) {
                    continue;
                }
                if (substr($v['body']['plain'], 0, 12) == "[rebot note]") {
                    continue;
                }
                if (strpos($v['body']['plain'], '+1') === FALSE) {
                    continue;
                }
                $processed = FALSE;
                $uid = $v['sender_id'];
                $plainMessage = $v['body']['plain'];
                //-1处理
                if ($plainMessage == '-1') {
                    $processed = TRUE;
                    unset($orders[$uid]);
                }
                //CC 或者 @ 的处理
                $parsedMessage = $v['body']['parsed'];
                //$parsedMessage = "cc [[user:1234567890]] cc [[user:9876543210]]";
                preg_match_all("/\[\[user:(\d{10})\]\]/", $parsedMessage, $matches);
                //$ccUid = substr($parsedMessag, strpos($parsedMessag, "[[user:")+7, 10);
                if (!empty($matches['1'])) {
                    foreach ($matches['1'] as $ccUid) {
                        if (is_numeric($ccUid)) {
                            $uid = $ccUid;
                            if (isset($user[$uid]) && !$processed) {
                                $order['user'] = $user[$uid]['full_name'];
                                $order['time'] = $v['created_at'];
                                $order['time'] = date("Y-m-d H:i:s", strtotime($order['time']));
                                $order['message'] = $v['body']['plain'];
                                if (empty($orders[$uid]) || $order['message']=='+1') {
                                    $orders[$uid] = $order;
                                }
                            }
                        }
                    }
                }
                elseif (isset($user[$uid]) && !$processed) {
                    $order['user'] = $user[$uid]['full_name'];
                    $order['time'] = $v['created_at'];
                    $order['time'] = date("Y-m-d H:i:s", strtotime($order['time']));
                    $order['message'] = $v['body']['plain'];
                    if (empty($orders[$uid]) || $order['message']=='+1') {
                        $orders[$uid] = $order;
                    }
                }
            }
            if (empty($orders)) {
                $this->error("group $groupId has no orders");
            }
            if ($abort) {
                $users = array();
                foreach ($orders as $order) {
                    $users[] = $order['user'];
                }
                $content = "[rebot note]截止".date("Y-m-d H:i:s")."一共有".count($orders)."人预定，具体名单为：".implode(',  ', $users)."，以上内容已经确定，之后预定没饭吃~";
                $bret = YammerHelper::postMessage($this->token, $content, $groupId, $messageId);
                if (!$bret) {
                    $this->error("abort message $content publish failed");
                }
            }
            $reportResult[$groupId] = $orders;
        }
        foreach ($reportResult as $groupId => $orders) {
            $groups = $this->groups;
            $count = count($orders);
            $users = array();
            foreach ($orders as $order) {
                $users[] = $order['user'];
            }
            $content = "[rebot note]截止".date("Y-m-d H:i:s")."\n";
            $content .= $groups[$groupId]['name']." 共计 $count 人预定。名单：".implode(',  ', $users)."\n\n\n";
        }
        $content .= "DEBUG INFO ".print_r($reportResult, TRUE);
        $this->emailReport($content);
        return TRUE;
    }
    
    protected function emailReport($message) {
        Yii::log($message, CLogger::LEVEL_INFO);
        echo "$message\n";
        $mailList = $this->reportMail;
        foreach ($this->groups as $value) {
            $mailList = array_merge($mailList, $value['reportMail']);
        }
        $mailMessage = iconv('utf-8', 'gb2312', $message);
        $mailSubject = iconv('utf-8', 'gb2312', 'Yammer 订饭结果');
        $mailStr = implode(',', $mailList);
        mail($mailStr, $mailSubject, $mailMessage);
        //mail('wangzhong@camera360.com,wangzhong@camera360.com', $mailSubject, $mailMessage);
    }
    
    protected function error($message) {
        Yii::log($message, CLogger::LEVEL_ERROR);
        echo "$message\n";
        //@TODO mailto wangzhong@camera360.com 
        $mailMessage = iconv('utf-8', 'gb2312', $message);
        mail("wangzhong@camera360.com", "Yammer robot meal notes ", $mailMessage);
        //mail("tanbingjie@camera360.com", "Yammer robot meal notes ", $mailMessage);
        //mail("konglingjun@camera360.com", "Yammer robot meal notes ", $mailMessage);
        //mail("zengxiaojuan@camera360.com", "Yammer robot meal notes ", $mailMessage);
    }
}