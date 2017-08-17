<?php
/*******************************************************************************
接受用户从小程序端提交的兵马俑id和密码，判断是否正确。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-12
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 从小程序端获取数据
$data = file_get_contents("php://input");
$data = json_decode($data, true);
$author = $data["author"];
$bmy_id = $data["bmy_id"];
$bmy_password = $data["bmy_password"];

$mongo = new MongoClient();
$db = $mongo->$dance_db;
setUserOnline(new MongoId($author), $db);

$sec = explode(" ", microtime()); // get t value
$micro = explode(".", $sec[0]);
$time = $sec[1]. substr($micro[1], 0, 3);
$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t=".$time."&id=".$bmy_id."&pw=".$bmy_password;
$result = file_get_html($proxy_url);
if(strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 密码错误!")) || strstr($result, iconv("UTF-8", "GB2312//IGNORE", "错误! 错误的使用者帐号!"))) // 判断账户密码是否正确
{
	echo json_encode(array("msg" => "ERR_WRONG_INFO"));
} else {	
	// 查找数据库diaries中来自兵马俑bbs该用户的文章
	$collection_diaries = $db->diaries;
 	$regex = new MongoRegex('/^'.$bmy_id.'（/');
	$diary_list = $collection_diaries->find(array('author' => $regex), 
		array('_id' => true))->sort(array('dnumber' => -1)); // 该用户来自兵马俑BBS的文章的ObjectId列表
	$last_diary = reset(iterator_to_array($diary_list));
	$last_diary_id = $last_diary['_id'];
	$last_diary = $collection_diaries->findOne(array('_id' => $last_diary_id), array('author' => true));
	$bmy_nickname = myfind($last_diary['author'], '（', '）', 0); // 兵马俑BBS昵称
	$diary_ids = array();
	foreach ($diary_list as $diary) {
		$diary_ids = array_merge($diary_ids, array($diary['_id']));
	}
	$collection_diaries->update(array('author' => $regex), 
		array('$set' => array('author' => new MongoId($author))),
		array('multiple' => true)); // 将数据库diaries中的相关文章作者改为当前作者ObjectId

	// 更新数据库users
	$collection_users = $db->users;
	$diary_posts = $collection_users->findOne(array("_id" => new MongoId($author)), array("diaries.posts" => true));
	$diary_posts = array_merge($diary_posts["diaries"]["posts"], $diary_ids); // 发表日记列表
	$credit = $collection_users->findOne(array("_id" => new MongoId($author)), array("degree.credit" => true));
	$credit = $credit["degree"]["credit"] + 200; // 连接兵马俑bbs积分加50
	$level = credit2level($credit); // 根据积分修改等级
	$collection_users->update(array("_id" => new MongoId($author)),
		array('$set' => array('degree.level' => $level, 'degree.credit' => $credit, 
		"diaries.posts" => $diary_posts, 
		'bmy.id' => $bmy_id, 'bmy.nickname' => $bmy_nickname[0], 'bmy.password' => $bmy_password)));

	echo json_encode(array('degree_level' => $level, 'degree_credit' => $credit, 'bmy_nickname' => $bmy_nickname[0]));
}

?>