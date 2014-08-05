<?php

/**
 * API统一签名函数
 *
 * @param array $params 不包括sn
 * @return string
 */
function apiSign($params) {

	$secret = MY_API_SECRET;
	ksort($params);

	$tmp = array();
	foreach ($params as $key => $val) {
		$tmp[] = $key . $val;
	}
	$tmp = implode('', $tmp);
	return md5($secret.$tmp);
}
?>