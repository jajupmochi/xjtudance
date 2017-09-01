<?php
/*******************************************************************************
数据库函数
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-29
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

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

?>