<?php  
/*******************************************************************************
图像处理函数
Version: 0.1 ($Rev: 2 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-09-12
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

/** 
* 下载远程图片保存到本地 
* 参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
* 当保存文件名称为空时则使用远程文件原来的名称
* @param string $url 文件url
* @param string $save_dir 保存文件目录
* @param string $filename 保存文件名称
* @param integer $type 获取文件的方式
* @return string 图片文件保存路径
* @access public
* @note 该函数来源于网络：http://blog.csdn.net/blueinsect314/article/details/29861399
*/
function saveImage($url, $save_dir = '', $filename = '', $type = 0) {
	if(trim($url) == '') { // url为空
		return array('errMsg' => "URL_NOT_SET");
	}
	if(trim($save_dir) == '') { // 保存路径为空，默认保存路径以年/月/日为目录
		$save_dir1 = "data/images/".date('Y')."/".date('m')."/".date('d')."/";
		$save_dir = $_SERVER['DOCUMENT_ROOT']."/aishangsalsa/".$save_dir1;
	}
	if(trim($filename) == '') { // 保存文件名为空
		$ext = strrchr($url, '.');
//        if ($ext != '.jpg' && $ext != '.png' && $ext != '.gif') { // 不是图片文件
//            return array('file_name' => '', 'save_path' => '', 'errMsg' => "FILE_NOT_IMAGE");
//        }
		$ext = ($ext == '' || $ext == '.') ? '.jpg' : $ext; // 后缀为空默认保存为.jpg
		$sec = explode(' ', microtime()); // get t value
		$micro = explode('.', $sec[0]);
		date_default_timezone_set("Asia/Shanghai");
		$filename = time().substr($micro[1], 0, 3).$ext; // 以时间命名
	}
	if(0 !== strrpos($save_dir, '/')) { // 在保存路径前加"/"
		// $save_dir .= '/';
	}
	// 创建保存目录
	if(!is_dir($save_dir) && !mkdir($save_dir, 0777, true)) {
		return array('errMsg' => "PATH_NOT_EXIST");
	}
	// 获取远程文件所采用的方法
	if($type) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$img = curl_exec($ch);
		curl_close($ch);
	} else {
		ob_start();
		readfile($url);
		$img = ob_get_contents();
		ob_end_clean();
	}
	// 保存文件
	file_put_contents($save_dir.$filename, $img);
	unset($img, $url);
	return $save_dir1.$filename;
}
	
?>  
