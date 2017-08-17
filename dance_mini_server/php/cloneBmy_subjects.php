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
$article_num = 0; // 文章总数
$article_start = 1; // 起始文章
$mongo = new MongoClient(); // 数据库

// 获取dance版第一篇文章
$sessionurl_g = "BMYAHADSPJYXLKWEMUBMUHOEIRMEEXFFPUUX_B"; // 匿名登录兵马俑的sessionurl
$proxy_url = "http://bbs.xjtu.edu.cn/".$sessionurl_g."/bbstdoc?board=dance&S=".$article_start; // dance版文章列表
$result = file_get_html($proxy_url);
$article_list = $result->find('a[href^=bbstcon]'); // 第一页文章列表
$article_f = myfind(substr($article_list[0]->href, 7), "?B=dance&F=", "&N=", 0);
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
		
	// 拼接数据
	$document = array(
		"dnumber" => $article_num, // diary序号
		"title" => iconv("GB2312", "UTF-8//IGNORE", $title[0]), // 标题
		"author" => $author_id[0]."（".
			iconv("GB2312", "UTF-8//IGNORE", $author_nickname[0])."）", // 作者
		"content" => iconv("GB2312", "UTF-8//IGNORE", $content), // 正文内容
		"time" => iconv("GB2312", "UTF-8//IGNORE", $time), // 发信时间
		"updated" => iconv("GB2312", "UTF-8//IGNORE", $time), // 最近一次修改时间
		"upup" => 0, // 顶帖数
		"favori" => 0, // 收藏数
		"viewed" => 0, // 查看次数
		"father" => "", // 父帖
		"discuss" => 0, // 讨论人数
		"reply" => array(
			"num" => 0, // 回复数
			"content" => array() // 回帖id
		),
		"highlight" => "", // 精华区路径
		"top" => false, // 置顶
		"location" => "", // 定位
		"tags" => array(), // 标签
		"from" => "bmy", // 来源
		"bmyurl" => $article_f, // bmybbs原文链接
		"coins" => array(
			"num" => 0, // 打赏数
			"coiners" => array() // 打赏用户id
		),
		"device" => "", // 发帖设备
		"ip" => "", // 发帖ip地址
		"ipv6" => false // 是否是ipv6
	);
		
	// 将数据存入数据库和备份数据库
	$db = $mongo->$dance_db;
	$collection = $db->diaries;
	$collection->insert($document);
	// echo "the diarie is saved to database successfully.<br>\n";
/* 		$db = $mongo->$dance_db_backup;
	$collection = $db->diaries;
	$collection->insert($document);
	echo "the diarie is saved to backup database successfully.\n"; */
}

?> 