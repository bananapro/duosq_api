<?php
//远程获取任务接口
class GetJobController extends AppController {

	var $name = 'GetJob';

	function beforeRender() {
		die();
	}

	function notifyOrderBackSms(){

		D('notify');
		$sendtype = \DAL\Notify::SENDTYPE_SMS; //短信通知

		I('sms');
		$sms = new sms();

		$ret = D('notify')->getOrderBackJobs($sendtype);
		$result = array();
		if($ret){

			foreach($ret as $user_id => $o_ids){
				$alipay = D('user')->detail($user_id, 'alipay');
				if(!valid($alipay, 'mobile'))continue;
				$content = $sms->getContent(\DAL\Notify::NOTIFYTYPE_ORDERBACK);
				$result[] = array('mobile' => $alipay, 'content'=>$content);
			}
		}
		if($result){
			if(DEBUG){
				pr($result);
			}else{
				echo serialize($result);
			}
		}else{
			echo 'empty';
		}
	}

	function notifyPaymentCompleteSms(){

		D('notify');
		$sendtype = \DAL\Notify::SENDTYPE_SMS; //短信通知

		I('sms');
		$sms = new sms();

		$ret = D('notify')->getPaymentCompleteJobs($sendtype);

		$result = array();
		if($ret){

			foreach($ret as $user_id => $o_ids){
				$alipay = D('user')->detail($user_id, 'alipay');
				if(!valid($alipay, 'mobile'))continue;

				$params = array();
				$params['alipay'] = mask($alipay);

				$pay_amount = array();
				foreach($o_ids as $o_id){

					$o_detail = D('order')->detail($o_id);
					if($o_detail['cashtype'] == \DAL\Order::CASHTYPE_CASH){
					}
					if($o_detail['amount']){
						if(!isset($pay_amount[$o_detail['cashtype']])){
							$pay_amount[$o_detail['cashtype']] = $o_detail['amount'];
						}else{
							$pay_amount[$o_detail['cashtype']] += $o_detail['amount'];
						}
					}
				}
				if(!$pay_amount)continue;

				if(isset($pay_amount[\DAL\Order::CASHTYPE_JFB])){
					$params['fanli'][] = $pay_amount[\DAL\Order::CASHTYPE_JFB]. "个集分宝";
				}

				if(isset($pay_amount[\DAL\Order::CASHTYPE_CASH])){
					$params['fanli'][] = price($pay_amount[\DAL\Order::CASHTYPE_CASH]). "元现金";
				}

				$params['fanli'] = join(',', $params['fanli']);

				$content = $sms->getContent(\DAL\Notify::NOTIFYTYPE_PAYMENTCOMPLETE, $params);
				$result[] = array('mobile' => $alipay, 'content'=>$content);
			}
		}

		if($result){
			if(DEBUG){
				pr($result);
			}else{
				echo serialize($result);
			}
		}else{
			echo 'empty';
		}
	}
}
?>