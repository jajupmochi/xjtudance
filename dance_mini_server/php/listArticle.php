<?php
/*******************************************************************************
读取数据库中的文章列表，发送给小程序，用于index页面加载。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-07-21
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/
	
include('config.php');

if ($dance_release) {
	// 禁止直接从浏览器输入地址访问.PHP文件
	$fromurl="https://57247578.qcloud.la/"; // 跳转往这个地址。
	if( $_SERVER['HTTP_REFERER'] == "" )
	{
		header("Location:".$fromurl);
		exit;
	}
}

// 获取用户提交的数据
$skip = $_GET["skip"];

// 从数据库读取文章列表
$mongo = new MongoClient(); // 连接数据库
$db = $mongo->$dance_db; // 获取dance的数据库（xjtudance）
$collection = $db->diaries; // 选择名称为“diaries”集合
$cursor = $collection->find()->sort(array('dnumber' => -1))->skip($skip)->limit(10); // 查找文档并按照文章序号升序排列，限制查找数量为10
$dataArr = iterator_to_array($cursor); // 将cursor转换为array
$dataJson = json_encode($dataArr); // 将数组转换为JSON字符串（兼容中文）注：之前用了自定义的arr2json函数，但是并没有用，而且会出现换行符错误。这个转换好像只有在浏览器中显示才有用，小程序不需要

echo $dataJson; // 向小程序返回json格式的数据
?>