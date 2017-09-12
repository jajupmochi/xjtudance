<?php
/*******************************************************************************
接受用户从小程序端提交的报到信息，储存到mongo数据库，需要时同步发表到兵马俑BBS。
Version: 0.1 ($Rev: 3 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-12
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 获取用户微信openid
$mongo = new MongoClient();
$db = $mongo->$dance_db;
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
	$bmy_title = "salsa舞友 ".$_POST['nickname'];
} else {
	$bmy_id = $user_info['bmy']['id'];
	$bmy_password = $user_info['bmy']['password'];
	$bmy_title = "salsa舞友 ".$_POST['nickname'];
}
$credit = ($user_info == null ? 0: $user_info['degree']['credit']) + 400;
$bmy_content = wxminiBaodao($bmy_id, $_POST['nickname'], $_POST['gender'], 
	$_POST['major'], $_POST['hometown'], 
	$_POST['selfIntro']).
	wxminiWatermark4bmy($time, $db, credit2level($credit));

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
$collection_diaries = $db->diaries;
if ($user_info == null) {
	// user数据	
	$diary_posts = array();
 	$doc_user = array(
		"id_dance" => "", // salsa的id
		"nickname" => $_POST['nickname'], // 昵称
		"password" => "", // 密码
		"avatar_url" => "", //$avatar_url, // 头像图片url，使用微信的??????????????????????
		"gender" => $_POST['gender'], // 性别
		"created" => $time, // 账号建立时间
		"degree" => array(
			"level" => credit2level($credit), // 等级
			"credit" => $credit, // 首次登录积分
			"last_attend" => "" // 上次签到时间
		),
		"person_info" => array(
			"realname" => $_POST['realname'], // 真实姓名
			"eggday" => $_POST['eggday'], // 生日
			"grade" => "", // 年级
			"major" => $_POST['major'], // 专业班级
			"hometown" => $_POST['hometown'], // 家乡
			"address" => "", // 所在地（经度 + 纬度）?????????????????????????????
			"QQ" => $_POST['QQ'], // QQ号
			"contact" => $_POST['contact'], // 联系方式
			"height" => "" // 身高
		),
		"web" => array(
			"duration" => 0, // 上站时间/秒
			"visit_time" => $time, // 本次访问时间
			"visit_from" => "wxmini", // 访问位置
			"lastvisit" => $time, // 上次访问时间
			"ip" => "", // 访问使用的ip地址 ??????????????
			"net_type" => "", // 网络类型
			"online" => true, // 是否在线
			"visited" => 1 // 访问次数
		),
		"individualized" => array(
			"status" => "", // 状态 ?????????????????
			"langue" => "", // 语言，使用微信的
			"contentsize" => 5, // 内容/字体大小 ????????????
			"frequent" => array(), // 用户常用
			"notify" => true, // 是否消息提醒
			"post2bmy" => true // 是否将文章同步到兵马俑
		),
		"diaries" => array(
			"posts" => $diary_posts, // 发表文章
			"upup" => array(), // 顶帖文章
			"favori" => array(), // 收藏文章
			"viewd" => array(), // 已查看文章
			"drafts" => array(), // 草稿
			"list_order" => "mama" // 排序方式默认为最近一次修改时间
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
			"openid_mini" => $str['openid'], // 与salsa微信小程序对应的用户openid
			"id" => "" // 微信id
		),
		"dance" => array(
			"baodao" => $time, // 报到时间，为空时未报到
			"baodao_bmyurl" => $bmyurl, // 报到对应的兵马俑BBS报到帖
			"ball_tickets" => array(), // 舞会门票
			"danceLevel" => "", // 初入salsa时的舞蹈水平
			"knowdancefrom" => $_POST['knowdancefrom'], // 从哪里知道salsa????????????????
			"selfIntro" => $_POST['selfIntro'], // 自我介绍
			"photos" => array($photo_path) // 照片地址
		),
		"activities" => array(
			"my_acts" => array(), // 发起活动
			"in_acts" => array() // 参与活动
		),
		"feedbacks" => array(), // 反馈
		"messages" => array() // 消息
	);
	$collection_users->insert($doc_user);
	$user_id = $doc_user['_id']; // 用户_id
} else {	//不为空，更新数据
	// 删除上次报到的信息
	// 删除兵马俑BBS对应报到帖
 	if (array_key_exists('baodao_bmyurl', $user_info['dance'])) {
		$bmyurl_old = $user_info['dance']['baodao_bmyurl'];
		$proxy_url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/del?B=dance&F=".$bmyurl_old;
		$result = file_get_html($proxy_url);
	}
	// 删除对应diary
	if (array_key_exists('baodao_diaryId', $user_info['dance'])) {
		$diaryId_old = $user_info['dance']['baodao_diaryId'];
		$collection_diaries->remove(array('_id' => $diaryId_old), array('justOne' => true));
		$diary_posts = $user_info['diary']['posts']; // 删除user中对应的post信息
		$key_id = array_search($diaryId_old, $diary_posts);
		if ($key_id != false) {
			array_splice($diary_posts, $key_id, 1);
		}
	}
	// 删除照片
	if (array_key_exists('photos', $user_info['dance'])) {
		$photos = $user_info['dance']['photos'];
		foreach ($photos as $photo) {
			if (is_string($photo)) {
				unlink($_SERVER['DOCUMENT_ROOT']."/".$photo);
			}
		}
	} 
	
	$doc_user = array(
		"person_info.realname" => $_POST['realname'], // 真实姓名
		"nickname" => $_POST['nickname'], // 昵称
		"gender" => $_POST['gender'], // 性别
		"degree.level" => credit2level($credit), // 等级
		"degree.credit" => $credit,
		"person_info.eggday" => $_POST['eggday'], // 生日
		"person_info.major" => $_POST['major'], // 专业班级
		"person_info.hometown" => $_POST['hometown'], // 家乡
		"person_info.QQ" => $_POST['QQ'], // QQ号
		"person_info.contact" => $_POST['contact'], // 联系方式
		"web.visit_from" => "wxmini", // 访问位置
		"web.lastvisit" => $time, // 上次访问时间
		"dance.baodao" => $time, // 报到时间，为空时未报到
		"dance.baodao_bmyurl" => $bmyurl, // 报到对应的兵马俑BBS报到帖
		"dance.knowdancefrom" => $_POST['knowdancefrom'], // 从哪里知道salsa????????????????
		"dance.selfIntro" => $_POST['selfIntro'], // 自我介绍
		"dance.photos" => array($photo_path) // 照片地址?????????????????????????????????????????
	);
	$collection_users->update(array('wechat.openid_mini' => $str['openid']), array('$set' => $doc_user));
	$doc_user = $collection_users->findOne(array('wechat.openid_mini' => $str['openid']));
	$user_id = $doc_user['_id']; // 用户_id
}

// diary数据
$id = ($bmy_id == 'jiaodadance' ? '小dance代发' : $bmy_id);
$content = "您的id是:\n".$id.
	"\n\n昵称呢?:\n".$_POST['nickname'].
	"\n\n性别:\n".$_POST['gender'].
	"\n\n专业班级:\n".$_POST['major'].
	"\n\n家乡:\n".$_POST['hometown'].
	"\n\n再介绍一下自己啦:\n".$_POST['selfIntro'].
	"\n\n打开微信小程序\"aishangsalsa\"查看美照啦~";
$doc_diary = array(
	"title" => $bmy_title, // 标题
	"author" => $user_id, // 作者
	"content" => $content, // 正文
	"time" => $time, // 发信时间
	"updated" => $time, // 最近一次修改时间
	"upup" => 0, // 顶帖数
	"favori" => 0, // 收藏数
	"viewed" => 0, // 查看次数
	"father" => "", // ObjectId of 父帖，如值为""则说明没有父帖
	"mama" => $time, // ObjectId of 对应主题帖，如为主帖则表示主帖和回复的最近修改时间
	"discuss" => 1, // 讨论人数，只有自己
	"reply" => array(), // 回帖
	"highlight" => "萨友报名/".date('Y')."年萨友报名集", // 精华区路径
	"top" => false, // 是否置顶
	"location" => "", // 定位 ?????????????????????
	"tags" => array('萨友报名'), // 标签 ?????????????????????
	"from" => "wxmini", // 发表位置
	"bmyurl" => $bmyurl, // 兵马俑bbs链接 ??????????????????？？？？？？？？？？？？？？？？？
	"coiners" => array(), // 金主
	"device" => "", // 发帖设备 ??????????????????
	"ip" => "", // 发帖ip地址 ???????????????
	"ipv6" => false, // 是否是ipv6 ??????????????????????
	"shared" => 0 // 被分享到微信的次数
);
$collection_diaries->insert($doc_diary);
$diary_id = $doc_diary["_id"];
$diary_posts[] = $diary_id; // 发表日记列表
$collection_users->update(array('_id' => $user_id), array('$set' => 
	array('diaries.posts' => $diary_posts, 'dance.baodao_diaryId' => $diary_id))); // 报到对应的diary id

// global数据
$collection_global = $db->globaldata;
$global_info = $collection_global->findOne(array('name' => 'dance'), array('baodao_num' => true, 'book' => true)); // ?????????????????????????报到人数这直接加一，没有考虑一个人多次报到的情况，且未更新visited和user_online数据
$diary_num = $collection_diaries->count(); // 文章总数
$user_num = $collection_users->count(); // 用户总数
$baodaoY = date('Y')."年萨友报名集";
/*  			 $baodaos = $global_info['book']['虫虫报到'][$baodaoY]; ?????????????????????????????????????????????此处未考虑到没有如果已经有book的情况，
			if (!is_array($baodaos)) {
				$book = array(
					'虫虫报到' => array(
						$baodaoY => array(
							$bmy_title => $diary_id)));
			}  */
$collection_global->update(array('name' => 'dance'), array('$set' => 
	array('diary_num' => $diary_num, 'user_num' => $user_num, 
	'baodao_num' => $global_info['baodao_num'] + 1)));

// 报名成功模板消息
$formId = $_POST['formId'];
$templateId = 'E5MPQmFpqHGLMoCgbhq5UK5e_63F3EWDvbzoyF3FfLw';
$time = explode('.', $time);
$time = $time[0];
$time = substr_replace($time, '-', 4, 0);
$time = substr_replace($time, '-', 7, 0);
$time = substr_replace($time, ' ', 10, 0);
$time = substr_replace($time, ':', 13, 0);
$time = substr_replace($time, ':', 16, 0);
$templateData = <<<END
{
  "touser": "{$str['openid']}",  
  "template_id": "{$templateId}", 
  "page": "/pages/salsaInfo/salsaInfo",          
  "form_id": "{$formId}",         
  "data": {
      "keyword1": {
          "value": "欢迎加入爱尚salsa", 
          "color": "#FF8C00"
      }, 
      "keyword2": {
          "value": "{$time}", 
          "color": "#173177"
      }, 
      "keyword3": {
          "value": "进入小程序，扫码加入爱尚salsa微信群", 
          "color": "#173177"
      }
  },
  "emphasis_keyword": "keyword1.DATA" 
}
END;
// access_token每天只能获取2000次，有效期目前为2个小时，需定时刷新
$getTokenApi = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
$resultStr = httpGet($getTokenApi);
$arr = json_decode($resultStr, true);
$token = $arr["access_token"];
// 发送模板消息的api
$templateApi = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$token}";
$res = httpPost($templateData, $templateApi);

// 返回报到用户提醒给版务
$banbans = $collection_users->find(array('rights.banban.is' => true), array('_id' => true, 'messages' => true));
$baodao_info = array(
	'realname' => $_POST['realname'], // 真实姓名
	'nickname' => $_POST['nickname'], // 昵称
	'gender' => $_POST['gender'], // 性别
	'eggday' => $_POST['eggday'], // 生日
	'major' => $_POST['major'], // 专业班级
	'hometown' => $_POST['hometown'], // 家乡
	'QQ' => $_POST['QQ'], // QQ号
	'contact' => $_POST['contact'], // 联系方式
	'knowdancefrom' => $_POST['knowdancefrom'], // 从哪里知道salsa????????????????
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
}

// 返回user数据
echo json_encode($doc_user);
?>