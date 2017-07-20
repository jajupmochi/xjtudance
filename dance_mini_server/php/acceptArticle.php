<?php
	/**
	* 接受用户从小程序端提交的文章，并储存到mongo数据库。
	*/
	
	// 获取用户提交的数据
	$openid = $_GET["openid"];
	$formId = $_GET["formId"];
	$title = $_GET["title"];
	$content = $_GET["content"];
	$from = $_GET["source"];
	
	// 将数据存入数据库
	$mongo = new MongoClient(); // 连接数据库
	$db = $mongo->xjtudance; // 获取名称为“xjtudance” 的数据库，如果数据库在mongoDB中不存在，mongoDB会自动创建
	$collection = $db->articles; // 选择名称为“articles”集合，如果集合在mongoDB中不存在，mongoDB会自动创建（collection相当于mysql中的table）
	// 拼接数据
	$document = array(
	"title" => $title, // 标题
	"content" => $content, // 正文内容
	"author" => "", // 作者
	"time" => "", // 发信时间
	"from" => $from, // 来源：包括bbs（兵马俑bbs）、bbswap（bbs wap版）、wxmini（微信小程序）
	"bbsurl" => "" // 兵马俑bbs链接
	);
    $collection->insert($document);	// 插入文档（一条文档即一条记录，相当于mysql中的item）
			
	// 在备份数据库中插入数据
	$db = $mongo->xjtudance_backup;
	$collection = $db->articles;
	$collection->insert($document);

	echo "success"; // 成功存入数据库则返回成功
?>