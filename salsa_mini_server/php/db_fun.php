<?php
/*******************************************************************************
数据库函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-05
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/


/**
* 将文件中保存的json格式数据读取到Mongo数据库中
* @param string $dir 保存数据库的文件目录
* @return array 成功提示或错误信息
* @access public
* @note 此函数会在保存文件到数据库时覆盖同名db，请提前备份数据库。
*/
function getMongoDataFromFile($dir = '') {
 	if(trim($dir) == '') { // 保存路径为空的默认保存路径
		$dir = "/data/release/xjtudance/data/mongodb-backup";
	}
	if(!is_dir($dir)) {
		return array('errMsg' => "PATH_NOT_EXIST");
	}
	
	$mongo = new Mongo();
	$db_names = scandir($dir);
	$db_names = array_diff($db_names, array('..', '.')); // 去除'..'和'.'这两个文件夹
	foreach ($db_names as $db_name) { // 数据库循环
		if ($db_name != 'admin' && $db_name != 'local') {
			$curr_db = $mongo->$db_name;
			$curr_db->drop();
			$curr_db = $mongo->$db_name;
			$db_path = $dir."/".$db_name;
			$collection_names = scandir($db_path);
			$collection_names = array_diff($collection_names, array('..', '.'));
			foreach ($collection_names as $collection_name) { // collection循环
				$collection_path = $db_path."/".$collection_name;
				$collection_name = explode('.', $collection_name);
				$collection_name = $collection_name[0];
				
				$curr_collection = $curr_db->$collection_name;
				$content = file_get_contents($collection_path); // 读取文件
				$content_json = json_decode($content, true); 
				foreach ($content_json as $document) { // 循环保存每条信息
					$_id = $document['_id']['$id'];
					$document['_id'] = new MongoId($_id);
					restoreMongoId($document);
					$curr_collection->insert($document);
				} 
			}
		}

	}
	return array('msg' => "FILE_SAVED_SUCCESS");
}

/**
* 将数据库内容以json格式保存到文件中
* @param string $save_dir 保存文件目录
* @return array 成功提示或错误信息
* @access public
*/
function saveMongoDB2File($save_dir = '') {
	if(trim($save_dir) == '') { // 保存路径为空的默认保存路径
		$save_dir = "/data/release/xjtudance/data/mongodb-backup";
	}
	
	$mongo = new Mongo();
	$db_list = $mongo->listDBs();
	$db_list = $db_list['databases'];
	foreach ($db_list as $db_name) {
		$dir_db = $save_dir."/".$db_name['name']; // 创建保存目录，每个数据库一个目录
		if(!is_dir($dir_db) && !mkdir($dir_db, 0777, true)) {
			return array('errMsg' => "PATH_NOT_EXIST");
		}
		$curr_db = $mongo->$db_name['name'];
		$collection_list = $curr_db->getCollectionInfo();
		foreach ($collection_list as $collection_name) { // 保存collection，每个collection一个文件
			$curr_collection = $curr_db->$collection_name['name'];
			$cursor = $curr_collection->find();
			$content = json_encode(iterator_to_array($cursor));
			file_put_contents($dir_db."/".$collection_name['name'].".dancedb", $content);
		}
	}
	return array('msg' => "FILE_SAVED_SUCCESS");
}

/**
* 将从json文件中读取得到的数据库中的$id转换为MongoId
* @param array $array 需要转换的数组的引用
* @access public
*/
function restoreMongoId(&$array) {
	//static $recursive_counter = 0; // 限制递归调用深度，最多可递归到10层array数据，超过报警
	//if (++ $recursive_counter > 10) { // 每次递归调用加1
	//	die('possible deep recursion attack!</br>可能受到了深层递归调用攻击！');
	//}
	if (array_key_exists('$id', $array)) {
		$array = new MongoId($array['$id']);
	} else {
		foreach ($array as &$value) {
			if (is_array($value)) {
				restoreMongoId($value);
			}
		}
	}
	//$recursive_counter --; // 递归返回后减1
}

?>