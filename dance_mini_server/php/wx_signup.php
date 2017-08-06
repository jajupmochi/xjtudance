<?php
/*******************************************************************************
当用户不存在时创建用户。从小程序端获取用户的登录凭证（code），调用微信接口获取用
户的唯一标识（openid）；使用该id和从小程序端获得的用户信息（userInfo）在数据库中
创建新用户，并将用户信息返回小程序端。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-04
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 从小程序端获取数据
$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
$code = $data["code"];
$avatar_url = $data["avatar_url"];
$nickname = $data["nickname"];
$gender = $data["gender"];
$langue = $data["individualized.langue"];
$net_type = $data["web.net_type"];

/* $retMsg = getImage("http://202.117.1.8:8080/dance/M.1500531262.A/743/FormatFactorymmexport1499527624772.jpg");
echo json_encode($retMsg); */

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
$openid =  $str["openid"];

// get t value (获取当前时间)
$sec = explode(" ", microtime());
$micro = explode(".", $sec[0]);
date_default_timezone_set("Asia/Shanghai");
$time = date("YmdHis").".".substr($micro[1], 0, 3);

// 拼接用户数据
$doc_user = array(
	"id_dance" => "", // dance的id
	"nickname" => $nickname, // 昵称，使用微信的
	"password" => "", // 密码
	"avatar_url" => $avatar_url, // 头像图片url，使用微信的??????????????????????
	"gender" => ($gender == 1) ? "gentleman" : "lady", // 性别，使用微信的
	"created" => $time, // 账号建立时间（UNIX纪元的微秒值）
	"degree" => array(
		"level" => 1, // 等级
		"credit" => 10, // 首次登录积分
		"last_attend" => "" // 上次签到时间
	),
	"person_info" => array(
		"eggday" => "", // 生日
		"grade" => "", // 年级
		"major" => "", // 专业
		"hometown" => "", // 家乡
		"address" => "", // 所在地（经度 + 纬度）?????????????????????????????
		"QQ" => "", // QQ号
		"contact" => "" // 联系方式
	),
	"web" => array(
		"duration" => 0, // 上站时间/秒
		"visit_time" => $time, // 本次访问时间
		"visit_from" => "wxmini", // 访问位置
		"lastvisit" => $time, // 上次访问时间
		"ip" => "", // 访问使用的ip地址 ??????????????
		"net_type" => $net_type, // 网络类型
		"online" => true, // 是否在线
		"visited" => 1 // 访问次数
	),
	"individualized" => array(
		"status" => "", // 状态 ?????????????????
		"langue" => $langue, // 语言，使用微信的
		"contentsize" => 5, // 内容/字体大小 ????????????
		"frequent" => array(), // 用户常用
		"notify" => true // 是否消息提醒
	),
	"diaries" => array(
		"posts" => array(), // 发表文章
		"upup" => array(), // 顶帖文章
		"favori" => array(), // 收藏文章
		"viewd" => array(), // 已查看文章
		"drafts" => array(), // 草稿
		"list_order" => "updated" // 排序方式默认为最近一次修改时间
	),
	"social" => array(
		"like" => array(), // ta喜欢的用户
		"liked" => array(), // 喜欢ta的用户
		"friends" => array(), // 朋友
		"blacklist" => array() // 黑名单
	),
	"letters" => array(), // 私信
	"coins" => array(
		"get" => 0, // 收入
	    "give" => 0, // 支出
	    "cashed" => 0, // 已提现金额
	    "remains" => 0, // 余额
	    "getnum" => 0, // 被打赏次数
	    "givenum" => 0, // 打赏次数
		"getlist" => array(), // 被打赏记录
		"givelist" => array() // 打赏记录
	),
	"rights" => array(
		"silenced" => "", // 禁言结束时间，为空时未被禁言
		"banban" => array(
			"is" => false, // 是否是斑斑
			"apply" => "" // 申请帖/申请卸任帖，为""时表示没申请
		),
		"wingdance" => array(
			"is" => false, // 是否是客服人员
			"apply" => "" // 申请帖/申请卸任帖，为""时表示没申请
		),
		"littlesound" => array(
			"is" => false, // 是否是小音箱
			"apply" => "" // 申请帖/申请卸任帖，为""时表示没申请
		)
	),
	"bmy" => array(
		"id" => "", // 兵马俑id
		"nickname" => "", // 兵马俑昵称
		"password" => "" // 兵马俑登录密码
	),
	"wechat" => array(
		"openid_mini" => $openid // 与dance微信小程序对应的用户openid
	),
	"dance" => array(
		"baodao" => "", // 报到时间，为空时未报到
		"ball_tickets" => array() // 舞会门票
	),
	"activities" => array(
		"my_acts" => array(), // 发起活动
		"in_acts" => array() // 参与活动
	),
	"feedbacks" => array(), // 反馈
	"messages" => array() // 消息
);
// 向数据库储存用户信息
$db = $mongo->$dance_db;
$collection = $db->users;
$collection->insert($doc_user); // 此句自动在$doc_user中加入对应的ObjectId
$_id = $doc_user["_id"]; // 用户_id
	
// 更新数据库globaldata
$user_num = $db->users->count(); // 用户总数
$collection = $db->globaldata;
$visited = $collection->findOne(array("name" => "dance"), array("visited" => true));
$visited = $visited["visited"] + 1; // 访问量
$user_online = $collection->findOne(array("name" => "dance"), array("user_online" => true));
$user_online = array_merge($user_online["user_online"], array($_id)); // 在线用户
$collection->update(array("name" => "dance"), array('$set' => 
	array("user_num" => $user_num, "visited" => $visited, "user_online" => $user_online)));

// 返回数据
$ret_keys = array_flip(array("_id", "nickname", "password", "avatar_url", 
	"gender", "degree", "individualized", "rights", "wechat", "messages")); // 要返回的键值
$ret_info = array_merge(array_intersect_key($doc_user, $ret_keys),  // 取交集方式返回需要的值
	array(
		"web" => array(
			"net_type" => $net_type,
			"online" => true
		),
		"diaries" => array(
			"upup" => array(),
			"favori" => array(),
			"viewd" => array(),
			"list_order" => "updated"
		)
	)
);
echo json_encode($ret_info);

?>