<?php  
/*******************************************************************************
网络函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-13
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

/**
* 设置用户在线标识，修改相应操作
* @param $_id 用户ObjectId
* @param &$db 保存登录信息的数据库
* @access public
* @note 该函数在每次用户联系服务器（包括登录）或发送心跳包时执行
*/
function setUserOnline($_id, &$db) {
 	$sec = explode(" ", microtime());	// get t value (获取当前时间)
	$micro = explode(".", $sec[0]);
	date_default_timezone_set("Asia/Shanghai");
	$time = date("YmdHis").".".substr($micro[1], 0, 3);
	
	$collection_users = $db->users;
	$collection_users->update(array("_id" => $_id),
		array('$set' => array('web.lastvisit' => $time, 'web.online' => true)));
	
	$collection_global = $db->globaldata;
	$user_online = $collection_global->findOne(array('name' => 'dance'), array('user_online' => true));
	if (!in_array($_id, $user_online['user_online'])) {
		$user_online = array_merge($user_online["user_online"], array($_id));
		$collection_global->update(array('name' => 'dance'), array('$set' => 
			array('user_online' => $user_online)));
	}
}

/**
* httpPost函数
* @param $data 发送数据
* @param string $url url
* @return 网络返回数据
* @access public
* @note 该函数来源于微信公众平台jssdk
*/
function httpPost($data, $url) 
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // 设置请求方式POST
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查，FALSE表示阻止对证书的合法性的检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 如果访问的url有发送跳转请求，将继续获取跳转后网址的内容
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 设置cURL参数，要求结果保存到字符串中还是输出到屏幕上, 0为直接输出屏幕，非0则不输出
	$tmpInfo = curl_exec($ch); // 执行操作，这里就是返回的结果
	if (curl_errno($ch)) { // 返回一个包含当前会话错误信息的数字编号
		return curl_errno($ch);
	}
	curl_close($ch);
	return $tmpInfo;
}

/**
* httpGet函数
* @param string $url url
* @return 网络返回数据
* @access public
* @note 该函数来源于微信公众平台jssdk
*/
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
