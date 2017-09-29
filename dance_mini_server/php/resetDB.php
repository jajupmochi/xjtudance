<?php
/*******************************************************************************
重置数据库。（注意，暂未添加重置前删除数据库操作，此操作将删除数据库所有数据，请
谨慎执行。）
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/xjtudance/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-06
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include_once('config.php');

$mongo = new MongoClient();
$db = $mongo->$dance_db;

$diary_num = $db->diaries->count();
$user_num = $db->users->count();

// 拼接globaldata数据
$doc_dance = array(
	"name" => "dance",
	"diary_num" => $diary_num, // 文章总数
	"user_num" => $user_num, // 用户总数
	"baodao_num" => 0, // 报到用户总数
	"visited" => 0, // 访问量
	"user_online" => array(), // 在线用户
	"user_silenced" => array(), // 被禁言用户
	"last_bmyclone" => "", // 上次兵马俑同步时间
	"last_bmyupdate" => "", // 上次兵马俑更新时间
	"tops" => array(), // 置顶帖
	"banbans" => array(), // 在任斑斑
	"banbans_all" => array(), // 所有斑斑
	"wingdance" => array(), // 在任客服
	"wingdance_all" => array(), // 所有客服
	"littlesounds" => array(), // 在任小音箱
	"littlesounds_all" => array(), // 所有小音箱
	"feedbacks" => array(), // 反馈
	"templates" => array(), // 模板
	"activities" => array(), // 活动
	"ball_tickets" => array(), // 舞会门票
	"book" => array() // 精华区
);

// 请手动填写小程序appid和小程序密匙
$doc_wxmini = array(
	"name" => "wxmini",
	"appid" => "", // 小程序appid
	"secret" => "", // 小程序密匙
	"shared" => 0 // 分享次数
);
$db->globaldata->insert($doc_dance);
$db->globaldata->insert($doc_wxmini);

?>