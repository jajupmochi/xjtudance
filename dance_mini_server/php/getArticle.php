<?php
/*******************************************************************************
接受用户从小程序端提交的文章id，从数据库中读取文章内容。
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin <jajupmochi@gmail.com>
Updated: 2017-07-22
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 获取文章id
$id = $_GET["id"];

// 删除数据库中对应文章
$mongo = new MongoClient(); // 连接数据库
$db = $mongo->$dance_db; // 获取dance的数据库（xjtudance），如果数据库在mongoDB中不存在，mongoDB会自动创建
$collection = $db->diaries; // 选择名称为“diaries”集合，如果集合在mongoDB中不存在，mongoDB会自动创建（collection相当于mysql中的table）
$cursor = $collection->findOne(array('_id' => new MongoId($id))); // 查找记录	
echo json_encode($cursor);
?>