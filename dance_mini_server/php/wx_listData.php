<?php
/*******************************************************************************
读取数据库中的数据列表，发送给小程序。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-08
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/
	
include_once('config.php');

if ($dance_release) {
	// 禁止直接从浏览器输入地址访问.PHP文件
	$fromurl="https://xjtudance.top/"; // 跳转往这个地址。
	if( $_SERVER['HTTP_REFERER'] == "" )
	{
		header("Location:".$fromurl);
		exit;
	}
}

// 从小程序端获取数据
$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (array_key_exists('collection_name', $data)) {
	$collection_name = $data['collection_name'];
	$skip = $data['skip'];
	$limit = $data['limit'];
	$list_order = $data['list_order'];
	$query = $data['query'];
	
	// 获取查询条件
	$where = ($query == '') ? array() : $query;
	if (array_key_exists('_id', $where) && is_string($where['_id'])) {
		$where['_id'] = new MongoId($where['_id']);
	}

	// 获取需要返回的values
	$which = array();
	$getValues = $data['getValues'];
	if ($getValues != '') {
		$values = explode('/', $getValues);	
		foreach ($values as $value) {
			$which = array_merge($which, array($value => true));
		}
	}

	// 从数据库读取列表
	$db = db::getMongoDB();
	$collection = $db->$collection_name;
	$doc = $collection->find($where, $which)->
		sort(array($list_order => -1))->skip($skip)->limit($limit);
	$arr = iterator_to_array($doc); // 将cursor转换为array

	echo json_encode($arr);
} else {
	echo json_encode(array('msg' => 'MISS_COLLECTION_NAME'));
}
?>