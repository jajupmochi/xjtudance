<?php
/*******************************************************************************
接受用户从小程序端提交的日记，储存到mongo数据库和备份数据库，需要时同步发表到兵
马俑BBS。
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-14
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

include('config.php');

// 从小程序端获取数据
/* $data = file_get_contents('php://input');
$data = json_decode($data, true);
echo $data; */
/* $post2bmy = $data['post2bmy'];
$author = $data['author']; */

ob_start(); // 打开缓冲区（处理上传的二进制文件）
readfile($_FILES['file']['tmp_name']);
$img = ob_get_contents();
ob_end_clean();
// 获取文件大小
$size = strlen($img);
file_put_contents('/data/release/xjtudance/test/data/test.png', $img);
echo $img;

//echo substr(sprintf('%o', fileperms('/data/release/xjtudance/test/')), -4);
?>