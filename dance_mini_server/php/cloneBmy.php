<?php
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<?php

include('config.php');

set_time_limit(0); // 设置超时时间为无限

$stime = microtime(true);

ob_end_clean();
ob_implicit_flush(1);

// 参数设置
$article_num = 12798; // 文章总数
$article_start = 12799; // 起始文章
$mongo = new MongoClient(); // 数据库

// 获取dance版第一篇文章
$sessionurl_g = "BMYAHADSPJYXLKWEMUBMUHOEIRMEEXFFPUUX_B"; // 匿名登录兵马俑的sessionurl
$proxy_url = "http://bbs.xjtu.edu.cn/".$sessionurl_g."/home?B=dance&S=".$article_start; // dance版文章列表
$result = file_get_html($proxy_url);
$article_list = $result->find('.tdborder a'); // 第一页文章列表
$article_f = myfind(substr($article_list[0]->href, 3), "?B=dance&F=", "&N=", 0);
$article_f = $article_f[0]; // 文章url中的F属性
$article_num++;
$proxy_url = "http://bbs.xjtu.edu.cn/BMY/con?B=dance&F=".$article_f."&N=".$article_num."&T=0"; // 文章url
$result = file_get_html($proxy_url); // 文章页面内容
//echo $article_num.": ";
//echo "\n".$article_f."\n".iconv("GB2312", "UTF-8//IGNORE", $result)."\n";

while (strpos($result->innertext, iconv("UTF-8", "GB2312//IGNORE", "下篇")) !== false) {
	
	ob_flush();
	flush();
	
	saveDiaryFromBmyResult($article_f, $result, $mongo, $dance_db, $dance_db_backup, $article_num); // 将文章数据存入数据库
	
	// 获取下篇文章内容
	$article_f = myfind($result, iconv("UTF-8", "GB2312//IGNORE", "本讨论区 </a>"), 
		iconv("UTF-8", "GB2312//IGNORE", "title=\"下篇"), 0);
	$article_f = myfind($article_f[0], "F=", "&amp;N=", 0);
	$article_f = $article_f[0];
	$article_num++;
	$proxy_url = "http://bbs.xjtu.edu.cn/BMY/con?B=dance&F=".$article_f."&N=".$article_num."&T=0"; // 文章url
	$result = file_get_html($proxy_url); // 文章页面内容
		
	if ($article_num % 100 == 0) {
		echo "文章编号：".$article_num."， ";//."\n".$article_f."\n".iconv("GB2312", "UTF-8//IGNORE", $result)."\n";

		$etime = microtime(true);
		$ttime = $etime - $stime;
		echo " 页面执行时间：{$ttime}秒<br>\n";		
	}
	
}

saveDiaryFromBmyResult($article_f, $result, $mongo, $dance_db, $dance_db_backup, $article_num); // 存储最后一篇
	
// 更新数据库globaldata
$db = $mongo->$dance_db;
$collection_global = $db->globaldata;
$collection_diaries = $db->diaries;
$diary_num = $collection_diaries->count(); // 文章总数
$sec = explode(' ', microtime()); // get t value
$micro = explode('.', $sec[0]);
$time = date("YmdHis").".".substr($micro[1], 0, 3);
$collection_global->update(array("name" => "dance"), array('$set' => 
	array("diary_num" => $diary_num, 'last_bmyclone' => $time)));

$etime = microtime(true);
$ttime = $etime - $stime;
echo "<br />页面执行时间：{$ttime}秒";
	
function saveDiaryFromBmyResult($article_f, $result, &$mongo, $dance_db, $dance_db_backup, $article_num) {
	// 获取文章各项数据
	$result_inner = $result->find('.bordertheme', 0)->innertext;
	$title = myfind($result_inner, iconv("UTF-8", "GB2312//IGNORE", "标 &nbsp;题: "), "\n<br>", 0); // 标题
	$author_id = myfind($result_inner, iconv("UTF-8", "GB2312//IGNORE", "发信人: "), " (", 0); // 作者id
	$author_nickname = myfind($result_inner, $author_id[0]." (", "),", 0); // 作者昵称
	$beforetime = myfind($result_inner, iconv("UTF-8", "GB2312//IGNORE", "发信站: "), "(", 0);
	$timeTemp = myfind($result_inner, $beforetime[0]."(", ")", 0);
	$time = str_replace(iconv("UTF-8", "GB2312//IGNORE", "&nbsp;"), "", $timeTemp[0]); // 发表时间
	$beforecontent = myfind($result_inner, $timeTemp[0], "<br>");
	$content = myfind($result_inner, $beforecontent[0]."<br>\n", "\n<br><div class=\"con_sig\">--", 0); // 正文
		
	// 正文特殊字符和格式处理
	$quote = myfind($content[0], iconv("UTF-8", "GB2312//IGNORE", "\n<br>【 在 "), 
		"\n<br></font>", 0); // 正文中引用之前文章的部分
	if ($quote[0] !== "") { // 去掉正文中引用之前文章的部分
		$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "\n<br>【 在 ").$quote[0]."\n<br></font>", "", $content[0]);
	} else {
		$content = $content[0];
	}
	$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "&quot;"), "\"", $content);
	$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "&amp;"), "&", $content);
	$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "&lt;"), "<", $content);
	$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "&gt;"), ">", $content);
	$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "&nbsp;"), " ", $content);
	$content = str_replace(iconv("UTF-8", "GB2312//IGNORE", "<br>"), "", $content); // html换行符<br>直接去掉
		
	$time_utf8 = iconv("GB2312", "UTF-8//IGNORE", $time); // 将时间转换为数据库储存形式
	$time_explode = explode(' ', $time_utf8);
	$time_year = $time_explode[4];
	$time_hms = $time_explode[3];
	$hms_explode = explode(':', $time_hms);
	$time_hour = $hms_explode[0];
	$time_min = $hms_explode[1];
	$time_sec = $hms_explode[2];
	$time_day = $time_explode[2];
	$time_day = (strlen($time_day) == 1 ? ("0".$time_day) : $time_day);
	$time_mon = '01';
	switch ($hms_explode[1])
	{
	case strpos($hms_explode[1], 'Jan'):
	  $time_mon = '01';
	case strpos($hms_explode[1], 'Feb'):
	  $time_mon = '02';
	case strpos($hms_explode[1], 'Mar'):
	  $time_mon = '03';
	case strpos($hms_explode[1], 'Apr'):
	  $time_mon = '04';
	case strpos($hms_explode[1], 'May'):
	  $time_mon = '05';
	case strpos($hms_explode[1], 'Jun'):
	  $time_mon = '06';
	case strpos($hms_explode[1], 'Jul'):
	  $time_mon = '07';
	case strpos($hms_explode[1], 'Aug'):
	  $time_mon = '08';
	case strpos($hms_explode[1], 'Sep'):
	  $time_mon = '09';
	case strpos($hms_explode[1], 'Oct'):
	  $time_mon = '10';
	case strpos($hms_explode[1], 'Nov'):
	  $time_mon = '11';
	case strpos($hms_explode[1], 'Dec'):
	  $time_mon = '12';
	default:
	  $time_mon = '01';
	}
	$time4db = $time_year.$time_mon.$time_day.$time_hour.$time_min.$time_sec.".000";
	
		
	// 拼接数据
	$doc_diary = array(
		"dnumber" => $article_num, // diary序号
		"title" => iconv("GB2312", "UTF-8//IGNORE", $title[0]), // 标题
		"author" => $author_id[0]."（".
			iconv("GB2312", "UTF-8//IGNORE", $author_nickname[0])."）", // 作者
		"content" => iconv("GB2312", "UTF-8//IGNORE", $content), // 正文内容
		"time" => $time4db, // 发信时间
		"updated" => $time4db, // 最近一次修改时间
		"upup" => 0, // 顶帖数
		"favori" => 0, // 收藏数
		"viewed" => 0, // 查看次数
		"father" => "", // 父帖
		"mama" => $time4db,
		"discuss" => 0, // 讨论人数
		"reply" => array(), // 回帖id
		"highlight" => "", // 精华区路径
		"top" => false, // 置顶
		"location" => "", // 定位
		"tags" => array(), // 标签
		"from" => "bmy", // 来源
		"bmyurl" => $article_f, // bmybbs原文链接
		"coiners" => array(), // 打赏用户id
		"device" => "", // 发帖设备
		"ip" => "", // 发帖ip地址
		"ipv6" => false // 是否是ipv6
	);
	// 将数据存入数据库和备份数据库
	$db = $mongo->$dance_db;
	$collection_diaries = $db->diaries;
	$collection_diaries->insert($doc_diary);
	
	// echo "the diarie is saved to database successfully.<br>\n";
/* 		$db = $mongo->$dance_db_backup;
	$collection = $db->diaries;
	$collection->insert($document);
	echo "the diarie is saved to backup database successfully.\n"; */
}

?> 