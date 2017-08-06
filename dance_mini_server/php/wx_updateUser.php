<?php
/*******************************************************************************
更新用户信息。从小程序端获取用户id和要更新的信息，更新数据库中对应信息。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-05
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 从小程序端获取数据
$data = file_get_contents("php://input");
$data = json_decode($data, true);
$_id = $data["_id"];

// 向数据库中写入对应信息
$mongo = new MongoClient();
$db = $mongo->$dance_db;
$collection = $db->users;

foreach($data as $key => $content) {
	if ($key != "_id") { // _id不可修改
		$collection->update(array('_id' => new MongoId($_id), 
			$key => array('$exists' => true)), // 键值存在时才能修改
			array('$set' => array($key => $content)));
	}
}

echo "the user's data is updated successfully.\n";

/* // 返回更新后的用户信息
$user_info = $collection->findOne(array('_id' => new MongoId($_id)));
echo json_encode($user_info); */

?>