<?php
/*******************************************************************************
接受用户从小程序端提交的回复，储存到mongo数据库和备份数据库，需要时同步发表到兵
马俑BBS。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-17
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 从小程序端获取数据
$data = file_get_contents('php://input');
$data = json_decode($data, true);
$post2bmy = false;
$author = $data['author'];

$mongo = new MongoClient(); // 连接数据库
$db = $mongo->$dance_db;
setUserOnline(new MongoId($author), $db);
$collection_users = $db->users;

$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
$time = date("YmdHis").".".substr($micro[1], 0, 3);

if ($post2bmy) {
	/* $author_bmy = $collection_users->findOne(array('_id' => new MongoId($author)), array('bmy.id' => true, 'bmy.password' => true));
	$timeBmy = $sec[1].substr($micro[1], 0, 3);
	$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t={$timeBmy}&id={$author_bmy['bmy']['id']}&pw={$author_bmy['bmy']['password']}";
	$result = file_get_html($proxy_url);
	if(strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 密码错误!")) || strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 错误的使用者帐号!"))) // 判断账户密码是否正确
	{
		$save2db = false;
		echo json_encode(array("msg" => "ERR_WRONG_INFO"));
	} else { // 同步到兵马俑BBS
		// 将文章同步发表到兵马俑bbs dance版
		$sessionurl_t = myfind($result, "url=/", "/", 0); // 通过bmy的proxy_url获取sessionurl
		$_SESSION["sessionurl"] = $sessionurl_t[0];
		$title_bmy = $data['title'] == '' ? '无题' : $data['title']; // 向bmy post文章
		$credit = $collection_users->findOne(array('_id' => new MongoId($author)), array('degree.credit' => true));
		$content_bmy = $data['content'].wxminiWatermark4bmy($time, $db, credit2level($credit['degree']['credit'] + 5 * ($post2bmy ? 2 : 1))); // 添加微信小程序水印
		$postdata = "title=".urlencode(iconv("UTF-8", "GB2312//IGNORE", $title_bmy))."&text=".urlencode(iconv("UTF-8", "GB2312//IGNORE", $content_bmy));
		$url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/bbssnd?board=dance&th=-1&signature=1";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);    
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		curl_close($ch);
		
		$bmyurl = "";
		$proxy_url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/home?B=dance&S="; // 获取该文的bmyurl
		$result = file_get_html($proxy_url);
		$user_list = $result->find('td[class=tduser] a');
		$article_list = $result->find('.tdborder a');
		for ($offset = 19; $offset >= 0; $offset--) {
			if ($user_list[$offset]->innertext == $author_bmy['bmy']['id']) { // 寻找该作者最近发布的文章
				$article_f = myfind(substr($article_list[$offset]->href, 3), "?B=dance&F=", "&N=", 0);
				$bmyurl = $article_f[0];
				break;
			}
		}
		$save2db = true;
	} */
} else { // 不同步到兵马俑BBS	
	$bmyurl = "";
	$save2db = true;
}

if ($save2db == true) {
	// 将diary及相关数据储存到数据库
	// 拼接日记数据
	$doc_diary = array(
		"title" => $data['title'], // 标题
		"author" => new MongoId($author), // 作者
		"content" => $data['content'], // 正文
		"time" => $time, // 发信时间
		"updated" => $time, // 最近一次修改时间
		"upup" => 0, // 顶帖数
		"favori" => 0, // 收藏数
		"viewed" => 0, // 查看次数
		"father" => new MongoId($data['fatherId']), // ObjectId of 父帖，如值为""则说明没有父帖
		"mama" => new MongoId($data['mamaId']), // ObjectId of 对应主题帖，如为主帖则表示主帖和回复的最近修改时间
		"discuss" => 1, // 讨论人数，只有自己
		"reply" => array(), // 回帖
		"highlight" => "", // 精华区路径（未加精）
		"top" => false, // 是否置顶
		"location" => "", // 定位 ?????????????????????
		"tags" => array(), // 标签 ?????????????????????
		"from" => "wxmini", // 发表位置
		"bmyurl" => $bmyurl, // 兵马俑bbs链接 ??????????????????？？？？？？？？？？？？？？？？？
		"coiners" => array(), // 金主
		"device" => "", // 发帖设备 ??????????????????
		"ip" => "", // 发帖ip地址 ???????????????
		"ipv6" => false, // 是否是ipv6 ??????????????????????
		"shared" => 0 // 被分享到微信的次数
	);
	// 将数据存入数据库
	$collection_diaries = $db->diaries;
	$collection_diaries->insert($doc_diary);
	
	// 更新母帖（主题帖）
	$mamaDiaryInfo = $collection_diaries->findOne(array('_id' => new MongoId($data['mamaId'])), array('reply' => true, 'author' => true));
	$reply = array_merge($mamaDiaryInfo['reply'], array($doc_diary['_id']));
	$author_reply = array();
	foreach ($reply as $replyId) {
		$cur_author = $collection_diaries->findOne(array('_id' => $replyId), array('author' => true)); // 这可以改用where???????????????????????????
		$author_reply = array_merge($author_reply, array($cur_author['author']));
	}
	$author_reply = array_unique($author_reply); // 删除重复元素
	$discuss = count($author_reply);
	$discuss = in_array($mamaDiaryInfo['author'], $author_reply) ? $discuss : ($discuss + 1); // 参与讨论人数
	$collection_diaries->update(array('_id' => new MongoId($data['mamaId'])), 
		array('$set' => array('mama' => $time, 'discuss' => $discuss, 'reply' => $reply)));
	
	if ($data['fatherId'] != $data['mamaId']) {
		// 更新父帖
		$fatherDiaryInfo = $collection_diaries->findOne(array('_id' => new MongoId($data['fatherId'])), array('reply' => true, 'author' => true));
		$reply = array_merge($fatherDiaryInfo['reply'], array($doc_diary['_id']));
		$author_reply = array();
		foreach ($reply as $replyId) {
			$cur_author = $collection_diaries->findOne(array('_id' => $replyId), array('author' => true)); // 这可以改用where???????????????????????????
			$author_reply = array_merge($author_reply, array($cur_author['author']));
		}
		$author_reply = array_unique($author_reply); // 删除重复元素
		$discuss = count($author_reply);
		$discuss = in_array($fatherDiaryInfo['author'], $author_reply) ? $discuss : ($discuss + 1); // 参与讨论人数
		$collection_diaries->update(array('_id' => new MongoId($data['fatherId'])), 
			array('$set' => array('discuss' => $discuss, 'reply' => $reply)));
		
		$father_nickname = $collection_users->findOne(array('_id' => $fatherDiaryInfo['author']), array('nickname' => true));
		$doc_diary['title'] = "@".$father_nickname['nickname']." ".$doc_diary['title']; // 添加@父帖作者昵称字段
	}

	// 更新users数据库
	$userInfo = $collection_users->findOne(array('_id' => new MongoId($author)), array('degree.credit' => true, 'diaries.posts' => true));
	$credit = $userInfo['degree']['credit'] + 5 * ($post2bmy ? 2 : 1); // 发文5分，如同步到兵马俑BBS则乘以2
	$level = credit2level($credit); // 根据积分修改等级
	$diary_posts = array_merge($userInfo['diaries']['posts'], array($doc_diary['_id'])); // 发表日记列表
	$collection_users->update(array('_id' => new MongoId($author)),
		array('$set' => array('degree.level' => $level, 'degree.credit' => $credit, 
		'individualized.post2bmy' => $post2bmy, 'diaries.posts' => $diary_posts)));
		
	// 更新数据库globaldata
	$diary_num = $collection_diaries->count(); // 文章总数
	$collection_global = $db->globaldata;
	$collection_global->update(array("name" => "dance"), array('$set' => 
		array("diary_num" => $diary_num)));
			
	// 在备份数据库中插入数据
	/* $db = $mongo->$dance_db_backup;
	$collection = $db->diaries;
	$collection->insert($doc_diary); */
	
	$author = $collection_users->findOne(array('_id' => new MongoId($author)), 
		array('_id' => true, 'id_dance' => true, 'nickname' => true, 'avatar_url' => true, 'degree' => true));
	$doc_diary['author'] = $author;
	echo json_encode(array($doc_diary)); // 将数据放到一个数组对象里便于小程序端拼接
}

?>