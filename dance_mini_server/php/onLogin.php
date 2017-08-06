<?php
/*******************************************************************************
从小程序端获取用户的登录凭证（code）,调用微信接口获取用户的唯一标识（openid）及
本次登录的会话密钥（session_key）,并返回给小程序。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-07-21
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

$code = $_GET["code"];
// 从数据库读取小程序的appid和密匙，下面调用微信api时需要使用
$mongo = new MongoClient();
$db = $mongo->xjtudance;
$collection = $db->globaldata;
$contents = $collection->findOne(array('name' => 'wxmini'), array('appid' => true, 'secret' => true));
$appid = $contents["appid"];
$secret = $contents["secret"];

// 调用微信接口获取用户的openid及本次登录的session_key
$api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
$str = httpGet($api);

// 将获取值返回给小程序
echo $str;
?>