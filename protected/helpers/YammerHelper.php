<?php
require_once realpath(dirname(__DIR__)).'/vendors/yammer/oauth.php';
class YammerHelper
{
    
    public static function postMessage($token, $content, $groupId, $repliedId='') {
        $message = array(
            'body' => $content,
            'group_id' => $groupId,
            'broadcast' => TRUE,
            'replied_to_id' => $repliedId,
        );
        $yammer = new YammerPHP();
        $yammer->setOAuthToken($token);
        $ret = $yammer->post("messages.json", $message);
        return $ret;
    }
    
    public static function getMessage($token, $groupId, $replied_to_id ) {
        $message = array(
            'group_id' => $groupId,
            'replied_to_id ' => $replied_to_id,
        );
        $yammer = new YammerPHP();
        $yammer->setOAuthToken($token);
        $ret = $yammer->get("messages.json", $message);
        return $ret;
    }
    
    public static function getUrl($token, $url) {
        $yammer = new YammerPHP();
        $yammer->setOAuthToken($token);
        $ret = $yammer->request($url);
        return $ret;
    }
    
    public static function getAuthorizationUrl() {
        $yammer = new YammerPHP();
        $url = $yammer->getAuthorizationUrl();
        return $url;
    }
    
    public static function getAccessToken($code) {
        $yammer = new YammerPHP();
        $token = $yammer->getAccessToken($code);
        return $token;
    }
    
    public static function test()
    {
        $yammer = new YammerPHP();
        $url = $yammer->getAccessToken('4ST8HNqGGfDEzl8g9E6Q');
        print_r($url);
    }
}