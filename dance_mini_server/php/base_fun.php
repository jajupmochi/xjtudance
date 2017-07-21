<?php  
/*******************************************************************************
常用函数（参考bmy_wap的配置方法）
Version: 0.1 ($Rev: 1 $)
Website: https://github.com/jajupmochi/xjtudance
Author: Linlin Jia <jajupmochi@gmail.com>
Updated: 2017-07-21
Licensed under The GNU General Public License 3.0
Redistributions of files must retain the above copyright notice.
*******************************************************************************/

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
