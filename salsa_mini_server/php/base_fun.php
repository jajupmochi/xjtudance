<?php  
/*******************************************************************************
常用函数（参考bmy_wap的配置方法）
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/aishangsalsa/aishangsalsa
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-08-04
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

/**
* 将用户积分转换为等级，等级区间按照斐波那契数列计算：0-1级1分，1-2级1分，
* 2-3级2分，3-4级3分，4-5级5分，5-6级8分，6-7级13分，7-8级21分，
* 8-9级34分，9-10级55分。等级称号包括：凭栏，亭亭，微步，广袖，流风，倾城，惊鸿，凝眉，拂云，断琴
* @param integer $credit 积分
* @return string 等级称号
* @access public
*/
function credit2level($credit) {
	$factor = 1000;
	switch ($credit)
	{
	case $credit < $factor:
	  return "凭栏"; // 昨夜西风凋碧树，独上高楼，望尽天涯路。
	case $credit >= $factor && $credit < 2 * $factor:
	  return "亭亭"; // 亭亭山上松，瑟瑟谷中风。
	case $credit >= 2 * $factor && $credit < 4 * $factor:
	  return "微步"; // 体迅飞凫，飘忽若神，凌波微步，罗袜生尘。
	case $credit >= 4 * $factor && $credit < 7 * $factor:
	  return "广袖"; // 寂寞嫦娥舒广袖，万里长空且为忠魂舞。
	case $credit >= 7 * $factor && $credit < 12 * $factor:
	  return "流风"; // 髣髴兮若轻云之蔽月，飘飖兮若流风之回雪。
	case $credit >= 12 * $factor && $credit < 20 * $factor:
	  return "倾城"; // 北方有佳人，绝世而独立，一顾倾人城，再顾倾人国。
	case $credit >= 20 * $factor && $credit < 33 * $factor:
	  return "惊鸿"; // 伤心桥下春波绿，曾是惊鸿照影来。
	case $credit >= 33 * $factor && $credit < 54 * $factor:
	  return "凝眉"; // 此情无计可消除，才下眉头，却上心头。
	case $credit >= 54 * $factor && $credit < 88 * $factor:
	  return "拂云"; // 舒卷意何穷，萦流复带空，有形不累物，无迹去随风。
	case $credit >= 88 * $factor:
	  return "绝琴"; // 子期死，伯牙谓世再无知音，乃破琴绝弦，终身不复鼓。
	default:
	  return "凭栏";
	}
}

/** 
* 下载远程图片保存到本地 
* 参数：文件url,保存文件目录,保存文件名称，使用的下载方式 
* 当保存文件名称为空时则使用远程文件原来的名称
* @note 该函数来源于网络：http://blog.csdn.net/blueinsect314/article/details/29861399
*/  
/* function getImage($url, $save_dir='', $filename='', $type = 0) {
    if(trim($url) == '') { // url为空
        return array('file_name' => '', 'save_path' => '', 'errMsg' => "URL_NOT_SET");
    }
    if(trim($save_dir) == '') { // 保存路径为空默认存在根目录下
        $save_dir = './';
    }
    if(trim($filename) == '') { // 保存文件名为空
        $ext = strrchr($url, '.');
        if($ext != '.jpg' && $ext != '.png' && $ext != '.gif') { // 不是图片文件
            return array('file_name' => '', 'save_path' => '', 'errMsg' => "FILE_NOT_IMAGE");
        }
        $filename = time().$ext; // 以时间命名
    }
    if(0 !== strrpos($save_dir, '/')) { // 在保存路径前加"/"
       // $save_dir .= '/';
    }
    // 创建保存目录
    if(!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return array('file_name' => '', 'save_path' => '', 'errMsg' => "PATH_NOT_EXIST");
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
    $fp2 = @fopen($save_dir.$filename, 'a');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    return array('file_name' => $filename, 'save_path' => $save_dir.$filename, 
		'file_size' => strlen($img), 'errMsg' => "SUCCESS");
}   */

/* // find elements by css selector
function find($selector, $idx=null) {
	$selectors = $this->parse_selector($selector);
	if (($count=count($selectors))===0) return array();
	$found_keys = array();

	// find each selector
	for ($c=0; $c<$count; ++$c) {
		if (($levle=count($selectors[0]))===0) return array();
		if (!isset($this->_[HDOM_INFO_BEGIN])) return array();

		$head = array($this->_[HDOM_INFO_BEGIN]=>1);

		// handle descendant selectors, no recursive!
		for ($l=0; $l<$levle; ++$l) {
			$ret = array();
			foreach($head as $k=>$v) {
				$n = ($k===-1) ? $this->dom->root : $this->dom->nodes[$k];
				$n->seek($selectors[$c][$l], $ret);
			}
			$head = $ret;
		}

		foreach($head as $k=>$v) {
			if (!isset($found_keys[$k]))
				$found_keys[$k] = 1;
		}
	}
} */

/**
* 找到字符串中的给定起止的部分
* @param string $content  要查找的字符串
* @param string $b 要匹配的开头
* @param string $e 要匹配的结尾
* @param integer $flag 是否需要转换字符集
* @return array 所有符合给定起止的字符串
* @access public
* @note 该函数来源于兵马俑bbs - https://github.com/bmybbs/bmybbs/blob/master/bmy_wap/base_fun.php
*/
function myfind($content, $b, $e, $flag)
{
	if ($flag === 1)
	{
		$content = iconv('gb2312', 'UTF-8', $content); // 将字符串的编码从UTF-8转到GB2312
		$b = iconv('gb2312', 'UTF-8', $b);
		$e = iconv('gb2312', 'UTF-8', $e);
	}
	$view = array();
	$i = 0;
	while (strpos($content, $b) !== false)
	{
		$content_new = substr($content, strpos($content, $b) + strlen($b));
		$view[$i] = substr($content_new, 0, strpos($content_new, $e)); 
		$content = substr($content_new, strlen($view[$i]));
		$i++;
	}
	return $view;
}

/**
* 使用特定function对数组中所有元素做处理
* @param string &$array 要处理的字符串
* @param string $function 要执行的函数
* @param boolean $apply_to_keys_also 是否也应用到key上
* @access public
* @note 该函数来源于网络
*/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false) // &使用引用而非拷贝方式操作数组，可直接修改数组
{
	static $recursive_counter = 0; // 限制递归调用深度，最多可递归到10层array数据，超过报警
	if (++ $recursive_counter > 10) { // 每次递归调用加1
		die('possible deep recursion attack!</br>可能受到了深层递归调用攻击！');
	}
	foreach ($array as $key => $value) {
		if (is_string($value)) {
			$array[$key] = $function($value); // 值value为字符串时，调用$function
		} else if (is_array($value)) {
			arrayRecursive($array[$key], $function, $apply_to_keys_also); // 值value为array时递归调用本函数
		}
		if ($apply_to_keys_also && is_string($key)) {
			$new_key = $function($key); // 键值$key为字符串时，调用$function
			if ($new_key != $key) {
				$array[$new_key] = $array[$key]; // 将原键的value赋予新键
				unset($array[$key]); // 删除原键key和值value
			}
		}
	}
	$recursive_counter --; // 递归返回后减1
}

/**
* 将数组转换为JSON字符串（兼容中文）
* @param array &$array 要转换的数组
* @return string 转换得到的json字符串
* @access public
* @note 该函数来源于网络
*/
function arr2json(&$array) {
	arrayRecursive($array, 'urlencode', true); // 将urlencode函数用到array中的所有层字符串数据（urlencode转码用于正常显示中文字符串）
	return urldecode(json_encode($array)); // 解码
}

?>  
