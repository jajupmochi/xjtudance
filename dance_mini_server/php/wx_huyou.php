<?php
/*******************************************************************************
接受用户从小程序端提交的忽悠信息，储存到mongo数据库，需要时同步发表到兵马俑BBS。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-07
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include_once('config.php');

// 获取用户微信openid
$db = db::getMongoDB();
$collection_global = $db->globaldata;
$contents = $collection_global->findOne(array('name' => 'wxmini'), array('appid' => true, 'secret' => true));
$appid = $contents["appid"];
$secret = $contents["secret"];
$api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$_POST['code']}&grant_type=authorization_code";
$str = json_decode(httpGet($api), true);

// 上传图片
$photo_path = saveImage($_FILES['photo']['tmp_name']); // tmp_name没有后缀，这里会把所有文件全部存为jpg????????????????????????????????

// 同步到兵马俑BBS
$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
date_default_timezone_set("Asia/Shanghai");
$time = date('YmdHis').".".substr($micro[1], 0, 3);
$timeBmy = $sec[1].substr($micro[1], 0, 3);

$collection_users = $db->users;
$user_info = $collection_users->findOne(array('wechat.openid_mini' => $str['openid']));
if ($user_info == null || $user_info['bmy']['id'] == '') { // 未绑定账户使用jiaodadance账户发帖
	$bmy_id = "jiaodadance";
	$bmy_password = "lovedance123";
} else {
	$bmy_id = $user_info['bmy']['id'];
	$bmy_password = $user_info['bmy']['password'];
}
$credit = ($user_info == null ? 0: $user_info['degree']['credit']) + 
	($_FILES['photo']['tmp_name'] == '' ? 10 : 20);
$bmy_title = "【忽悠】".$_POST['time'].$_POST['place'].$_POST['name'];
$bmy_content = $_POST['comment']."\n\n".wxminiWatermark4bmy($time, $db, credit2level($credit));

$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t={$timeBmy}&id={$bmy_id}&pw={$bmy_password}";
$result = file_get_html($proxy_url);
$sessionurl_t = myfind($result, "url=/", "/", 0); // 通过bmy的proxy_url获取sessionurl
$_SESSION["sessionurl"] = $sessionurl_t[0];
$postdata = "title=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $bmy_title))."&text=".urlencode(iconv("UTF-8", "GB18030//IGNORE", $bmy_content));
$url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/bbssnd?board=dance&th=-1&signature=1";
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_URL, $url);    
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$result = curl_exec($ch);
curl_close($ch);

$bmyurl = "";  // 获取该文的bmyurl
$proxy_url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/home?B=dance&S=";
$result = file_get_html($proxy_url);
$user_list = $result->find('td[class=tduser] a');
$article_list = $result->find('.tdborder a');
for ($offset = 19; $offset >= 0; $offset--) {
	if ($user_list[$offset]->innertext == $bmy_id) { // 寻找该作者最近发布的文章
		$article_f = myfind(substr($article_list[$offset]->href, 3), "?B=dance&F=", "&N=", 0);
		$bmyurl = $article_f[0];
		break;
	}
}

// 保存数据到数据库
// diary数据
$user_id = $user_info['_id']; // 用户_id
$doc_diary = array(
	"title" => $bmy_title, // 标题
	"author" => $user_id, // 作者
	"content" => $_POST['comment'], // 正文
	"time" => $time, // 发信时间
	"updated" => $time, // 最近一次修改时间
	"upup" => 0, // 顶帖数
	"favori" => 0, // 收藏数
	"viewed" => 0, // 查看次数
	"father" => "", // ObjectId of 父帖，如值为""则说明没有父帖
	"mama" => $time, // ObjectId of 对应主题帖，如为主帖则表示主帖和回复的最近修改时间
	"discuss" => 1, // 讨论人数，只有自己
	"reply" => array(), // 回帖
	"highlight" => "舞会忽悠/".date('Y')."年的舞会忽悠", // 精华区路径
	"top" => false, // 是否置顶
	"location" => "", // 定位 ?????????????????????
	"tags" => array('舞会忽悠'), // 标签 ?????????????????????
	"from" => "wxmini", // 发表位置
	"bmyurl" => $bmyurl, // 兵马俑bbs链接 ??????????????????
	"coiners" => array(), // 金主
	"device" => "", // 发帖设备 ??????????????????
	"ip" => "", // 发帖ip地址 ???????????????
	"ipv6" => false, // 是否是ipv6 ??????????????????????
	"shared" => 0 // 被分享到微信的次数
);
$collection_diaries = $db->diaries;
$collection_diaries->insert($doc_diary);
$diary_id = $doc_diary["_id"];

// activity数据
$doc_activity = array(
	"name" => $_POST['name'], // 活动名称
	"start_time" => $_POST['time'], // 开始时间
	"end_time" => "", // 结束时间
	"place" => $_POST['place'], // 地点
	"comment" => $_POST['comment'], // 活动说明
	"photos" => is_array($photo_path) ? array() : array($photo_path), // 照片地址????????????????????????????????????????????
	"start_time" => $_POST['time'], // 开始时间
	"tags" => array($_POST['tag']), // 标签 ?????????????????????
	"initator" => $user_id, // 发起人id
	"peopleNum" => $_POST['peopleNum'], // 设置的可参与人数
	"isOfficial" => true, // 是否正式活动？？？？？？？？？？？？？？？？？？？？？？？？？？没有区分
	"time" => $time, // 发信时间
	"updated" => $time, // 最近一次修改时间
	"participants" => array(), // 参加者列表
	"upup" => 0, // 顶帖数
	"favori" => 0, // 收藏数
	"viewed" => 0, // 查看次数
	"discuss" => 1, // 讨论人数，只有自己
	"reply" => array(), // 回复
	"from" => "wxmini", // 发表位置
	"device" => "", // 发帖设备 ??????????????????
	"shared" => 0, // 被分享到微信的次数
	"diaryId" => $diary_id // 对应的diary的id
);
$collection_activities = $db->activities;
$collection_activities->insert($doc_activity);
$activity_id = $doc_activity["_id"];

// user数据
$diary_posts = $user_info['diaries']['posts'];
$diary_posts = array_merge($diary_posts, array($diary_id)); // 发表日记列表
$my_acts = $user_info['activities']['my_acts'];
$my_acts = array_merge($my_acts, array($activity_id)); // 发表活动列表
$collection_users->update(array('_id' => $user_id), 
	array('$set' => array('degree.level' => credit2level($credit), 'degree.credit' => $credit, 
	'diaries.posts' => $diary_posts, 'activities.my_acts' => $my_acts)));

// global数据
$collection_global = $db->globaldata;
$global_info = $collection_global->findOne(array('name' => 'dance'), array('activities' => true, 'book' => true)); // ????????????????????? ????未更新visited和user_online数据
$activities = $global_info['activities'];
$activities = array_merge($activities, array($activity_id)); // 发表日记列表
$baodaoY = date('Y')."年的舞会忽悠";
/*  			 $baodaos = $global_info['book']['虫虫报到'][$baodaoY]; ?????????????????????????????????????????????此处未考虑到没有如果已经有book的情况，
			if (!is_array($baodaos)) {
				$book = array(
					'虫虫报到' => array(
						$baodaoY => array(
							$bmy_title => $diary_id)));
			}  */
$collection_global->update(array('name' => 'dance'), array('$set' => 
	array('activities' => $activities)));

// 返回活动提醒给版务
/* $banbans = $collection_users->find(array('rights.banban.is' => true), array('_id' => true, 'messages' => true));
$baodao_info = array(
	'nickname' => $_POST['nickname'], // 昵称
	'gender' => $_POST['gender'], // 性别
	'eggday' => $_POST['eggday'], // 生日
	'grade' => $_POST['grade'], // 年级
	'major' => $_POST['major'], // 专业
	'hometown' => $_POST['hometown'], // 家乡
	'QQ' => $_POST['QQ'], // QQ号
	'contact' => $_POST['contact'], // 联系方式
	'height' => $_POST['height'], // 身高
	'wxid' => $_POST['wechat_id'], // 微信id
	'danceLevel' => $_POST['danceLevel'], // 初入dance时的舞蹈水平
	'knowdancefrom' => $_POST['knowdancefrom'], // 从哪里知道dance????????????????
	'selfIntro' => $_POST['selfIntro'], // 自我介绍
	'photos' => $photo_path // 照片地址
);
foreach ($banbans as $banban) {
	$msgs = $banban['messages'];
	if (array_key_exists('baodaos', $msgs)) {
		$baodaos = $msgs['baodaos'];
		$baodaos[] = $baodao_info;
		$msgs['baodaos'] = $baodaos;
	} else {
		$msgs = array_merge($msgs, array('baodaos' => array($baodao_info)));
	}
	$collection_users->update(array('_id' => $banban['_id']), array('$set' => 
		array('messages' => $msgs)));
} */

// 返回activities数据
echo json_encode($doc_activity);
?>