<?php
/*******************************************************************************
从小程序端获取用户的登录凭证（code），调用微信接口获取用户的唯一标识（openid）及
本次登录的会话密钥（session_key），据此从数据库获取用户信息并更新数据库信息；如
无此用户，则返回null。
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-06
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 从小程序端获取数据
$data = file_get_contents("php://input");
$data = json_decode($data, true);
$code = $data["code"];
$net_type = $data["web.net_type"];

// 从数据库读取小程序的appid和密匙，下面调用微信api时需要使用
$mongo = new MongoClient();
$db = $mongo->$dance_db;
$collection = $db->globaldata;
$contents = $collection->findOne(array('name' => 'wxmini'), array('appid' => true, 'secret' => true));
$appid = $contents["appid"];
$secret = $contents["secret"];

// 调用微信接口获取用户的openid及本次登录的session_key
$api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
$str = json_decode(httpGet($api), true); // 第二个参数为true时返回array而非object

// 从数据库拉取用户信息
$db = $mongo->$dance_db;
$collection = $db->users;
$ret_keys = array("_id" => true, "nickname" => true, "password" => true, "avatar_url" => true, "gender" => true, "degree" => true, "web.net_type" => true, "web.online" => true, "individualized" => true, "diaries.upup" => true, "diaries.favori" => true, "diaries.viewd" => true, "diaries.list_order" => true, "rights" => true, "wechat" => true, "messages" => true); // 返回键值
$user_info = $collection->findOne(array('wechat.openid_mini' => $str["openid"]), $ret_keys);

if ($user_info !== null) {
	// 更新user数据
	$credit = $user_info["degree"]["credit"] + 1; // 登录积分加1
	$level = credit2level($credit); // 根据积分修改等级
	$sec = explode(" ", microtime());
	$micro = explode(".", $sec[0]);
	date_default_timezone_set("Asia/Shanghai");
	$visit_time = date("YmdHis").".".substr($micro[1], 0, 3); // 访问时间
	$visited = $collection->findOne(array("wechat.openid_mini" => $str["openid"]), array("web.visited" => true));
	$visited = $visited["web"]["visited"] + 1; // 访问次数加1
	$collection->update(array("wechat.openid_mini" => $str["openid"]), 
		array('$set' => array("degree.level" => $level, "degree.credit" => $credit, 
		"web.visit_time" => $visit_time, "web.visit_from" => "wxmini", 
		"web.net_type" => $net_type, "web.online" => true, "web.visited" => $visited)));
	
	$user_info = $collection->findOne(array('wechat.openid_mini' => $str["openid"]), $ret_keys);
	
	// 更新数据库globaldata
	$collection = $db->globaldata;
	$visited = $collection->findOne(array("name" => "dance"), array("visited" => true));
	$visited = $visited["visited"] + 1; // 访问量
	$user_online = $collection->findOne(array("name" => "dance"), array("user_online" => true));
	$user_online = array_merge($user_online["user_online"], array($user_info["_id"])); // 在线用户
	$collection->update(array("name" => "dance"), array('$set' => 
		array("visited" => $visited, "user_online" => $user_online)));
}

echo json_encode($user_info);

?>