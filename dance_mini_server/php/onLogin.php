<?php
	/**
	* 从小程序端获取用户的登录凭证（code）,调用微信接口获取用户的唯一标识（openid） 
	* 及本次登录的会话密钥（session_key）,并返回给小程序。
	*/
	
	$code = $_GET["code"];
	// 从数据库读取小程序的appid和密匙，下面调用微信api时需要使用
	$mongo = new MongoClient();
	$db = $mongo->xjtudance;
	$collection = $db->globaldata;
	$contents = $collection->findOne(array('name' => 'wxmini'), array('contents' => true));
	$appid = $contents["contents"]["appid"];
	$secret = $contents["contents"]["secret"];
	
	// 调用微信接口获取用户的openid及本次登录的session_key
	$api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
	$str = httpGet($api);
	
	// 将获取值返回给小程序
	echo $str;

	// httpGet函数
	function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}
?>