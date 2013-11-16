<?php
class YammerController extends Controller {
    public function actionAuth() {
        $url = YammerHelper::getAuthorizationUrl();
	    header("Location: $url");
	    Yii::app()->end();
    }
    
    public function actionCallback() {
        $code = Yii::app()->request->getParam('code');
        $infos = YammerHelper::getAccessToken($code);
        $token = $infos['access_token'];
        $dataYammerToken = new ModelDataYammerToken();
        $var = $dataYammerToken->add($token);
        $dataYammerUser = new ModelDataYammerUser();
        $var = $dataYammerUser->add($infos['user']);
        $dataYammerNetwork = new ModelDataYammerNetwork();
        $var = $dataYammerNetwork->add($infos['network']);
        echo "token got \n";
        var_dump($var);
        echo "<pre>".print_r($infos, TRUE)."</pre>";
    }
}