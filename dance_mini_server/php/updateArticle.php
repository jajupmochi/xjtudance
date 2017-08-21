<?php
/*******************************************************************************
修改文章。接受用户从小程序端提交的文章，储存到mongo数据库和备份数据库，同步发表
到兵马俑BBS。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-07-23
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 获取用户提交的数据
$id = $_GET["id"]; //文章id
$openid = $_GET["openid"];
$formId = $_GET["formId"];
$title = $_GET["title"];
$content = $_GET["content"];
$from = $_GET["source"];

// 更新数据库
$mongo = new MongoClient();
$db = $mongo->$dance_db;
$collection = $db->diaries;
/* // 拼接数据
$document = array(
"title" => $title, // 标题
"content" => $content, // 正文内容
"author" => "", // 作者
"time" => "", // 发信时间
"from" => $from, // 来源：包括bbs（兵马俑bbs）、bbswap（bbs wap版）、wxmini（微信小程序）
"bbsurl" => "" // 兵马俑bbs链接
); */
$collection->update(array('_id' => new MongoId($id)), array('$set' => array
("title" => $title, "content" => $content)));	// 更新文档
	
echo "the diary is updated to database successfully.\n"; // 成功存入数据库则返回成功
		
// 在备份数据库中插入数据
$db = $mongo->$dance_db_backup;
$collection = $db->diaries;
$collection->update(array('_id' => new MongoId($id)), array('$set' => array
("title" => $title, "content" => $content)));

echo "the diary is updated to backup database successfully.\n"; // 成功存入备份数据库则返回成功
	
/* if ($dance_release) {
	// 将文章同步发表到兵马俑bbs dance版
	// get t value (获取当前时间)
	$sec = explode(" ", microtime());
	$micro = explode(".", $sec[0]);
	$time = $sec[1].substr($micro[1], 0, 3);
	
	// 通过bmy的proxy_url获取sessionurl
	$proxy_url = "http://bbs.xjtu.edu.cn/BMY/bbslogin?ipmask=8&t=".$time."&id=jiaodadance&pw=lovedance123";
	$result = file_get_html($proxy_url);
	$sessionurl_t = myfind($result, "url=/", "/", 0);
	$_SESSION["sessionurl"] = $sessionurl_t[0];
	
	// 向bmy post文章
	$postdata = "title=".urlencode(iconv("UTF-8", "GB2312//IGNORE", $title))."&text=".urlencode(iconv("UTF-8", "GB2312//IGNORE", $content));
	$url = "http://bbs.xjtu.edu.cn/".$_SESSION["sessionurl"]."/bbssnd?board=dance&th=-1&signature=1";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL, $url);    
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$result = curl_exec($ch);
	curl_close($ch);
	
	echo "the diarie is posted to bmybbs (board dance) successfully.\n"; // 成功同步到兵马俑BBS
} */
?>