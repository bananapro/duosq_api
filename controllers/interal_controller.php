<?php
//内部接口，用于server to server服务
class InteralController extends AppController {

	var $name = 'Interal';

	function pay($user_id){

		if(!$user_id)$this->_error('param user_id miss');

		$errcode = '';
		$ret = D('pay')->jfb($user_id, $errcode);
		if($ret)
			$this->_success('param user_id miss');
		else
			$this->_error($errcode);
	}
}
?>