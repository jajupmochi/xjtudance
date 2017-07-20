<?php
	/**
	* 读取数据库中的文章列表，发送给小程序，用于index页面加载。
	*/
	
	// 禁止直接从浏览器输入地址访问.PHP文件
/* 	$fromurl="https://57247578.qcloud.la/"; // 跳转往这个地址。
	if( $_SERVER['HTTP_REFERER'] == "" )
	{
		header("Location:".$fromurl);
		exit;
	} */
		
	// 获取用户提交的数据
	
	// 从数据库读取文章列表
	$mongo = new MongoClient(); // 连接数据库
	$db = $mongo->xjtudance; // 获取名称为“xjtudance” 的数据库
	$collection = $db->articles; // 选择名称为“articles”集合
	$cursor = $collection->find()->sort(array('_id' => -1)); // 查找文档并按照id升序排列
	$dataArr = iterator_to_array($cursor); // 将cursor转换为array
	$dataJson = arr2json($dataArr); // 将数组转换为JSON字符串（兼容中文）
	
	echo $dataJson; // 向小程序返回json格式的数据
	
	/**************************************************************
	 *
	 * 使用特定function对数组中所有元素做处理
	 * @param string &$array  要处理的字符串
	 * @param string $function 要执行的函数
	 * @return boolean $apply_to_keys_also  是否也应用到key上
	 * @access public
	 *
	 *************************************************************/
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
	
	/**************************************************************
	 *
	 * 将数组转换为JSON字符串（兼容中文）
	 * @param array $array  要转换的数组
	 * @return string  转换得到的json字符串
	 * @access public
	 *
	 *************************************************************/
	function arr2json(&$array) {
		arrayRecursive($array, 'urlencode', true); // 将urlencode函数用到array中的所有层字符串数据（urlencode转码用于正常显示中文字符串）
		return urldecode(json_encode($array)); // 解码
	}
?>