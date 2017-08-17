<?php
/*******************************************************************************
读取数据库中的日记列表，发送给小程序。
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-15
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

// 从小程序端获取数据
$data = file_get_contents('php://input');
$data = json_decode($data, true);
$skip = $data['skip'];
$limit = $data['limit'];
$list_order = $data['list_order'];
//$user_id = $data['user_id'];

$mongo = new MongoClient(); // 连接数据库
$db = $mongo->$dance_db; // 获取dance的数据库（xjtudance）
/* setUserOnline(new MongoId($user_id), $db); */

// 从数据库读取文章列表
$collection_diaries = $db->diaries; // 选择名称为“diaries”集合
$diaries_cur = $collection_diaries->find(array('father' => ''))->
	sort(array($list_order => -1))->skip($skip)->limit($limit); // 查找文档并按照日记修改日记降序排列，限制查找数量为
$diaries_arr = iterator_to_array($diaries_cur); // 将cursor转换为array

$collection_users = $db->users; // 添加所需的作者信息
foreach ($diaries_arr as &$diary) {
	if (!is_string($diary['author'])) { // author非字符串时，为来自小程序或来自兵马俑BBS且关联到用户的文章
		$author = $collection_users->findOne(array('_id' => $diary['author']), 
			array('_id' => true, 'id_dance' => true, 'nickname' => true, 'avatar_url' => true, 'degree' => true));
		$diary['author'] = $author;
	} else { // author非字符串时，为来自兵马俑BBS且未关联到用户的文章
		$degree = array('level' => '凭栏', 'credit' => 0);
		$diary['author'] = array('id_dance' => '', 'nickname' => $diary['author'], 
			'avatar_url' => 'https://57247578.qcloud.la/test/images/wanted-200.jpg',
			'degree' => $degree);
	}
	
}
unset($diary);

$dataJson = json_encode($diaries_arr); // 将数组转换为JSON字符串（兼容中文）注：之前用了自定义的arr2json函数，但是并没有用，而且会出现换行符错误。这个转换好像只有在浏览器中显示才有用，小程序不需要

echo $dataJson; // 向小程序返回json格式的数据
?>