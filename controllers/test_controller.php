<?php
//测试后台功能
class TestController extends AppController {

	var $name = 'Test';

	function beforeRender() {
		die();
	}

	function taobao($o_id){

		//$o_id, $sub, $new_field
		//D('order')->updateSub('taobao', $o_id, array('status'=>10)); //通过审核
		D('order')->updateSub('taobao', $o_id, array('status'=>20)); //设置无效
	}

	function sendMail(){
		I('email/send');
		$data = array('demo'=>'hello world', 'emails'=>array('banana19191919@163.com'));
		$obj = new send($data, 'demo');
		$ret = $obj->send();
		pr($ret);
	}

	function notify($o_id){

		if(!$o_id)die('o_id error');
		//$ret = D('notify')->addPaymentCompleteJob($o_id);
		//var_dump($ret);
		//die();
		$ret = D('notify')->getPaymentCompleteJobs('email');
		pr($ret);
	}
}
?>