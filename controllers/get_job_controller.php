<?php
//远程获取任务接口
class GetJobController extends AppController {

	var $name = 'GetJob';

	function beforeRender() {
		die();
	}

	//获取短信知会任务
	function notifySms(){

		$a = $this->_notifyOrderBackSms();
		$b = $this->_notifyPaymentCompleteSms();
		$c = $this->_notifyCashgiftActivedSms();

		$ret = array_merge($a, $b, $c);
		if(DEBUG){
			pr($ret);
		}else{
			if($ret){
				echo serialize($ret);
			}else{
				echo 'empty';
			}
		}
	}

	//获取跟单成功短信知会任务
	private function _notifyOrderBackSms(){

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
			return $result;
		}else{
			return array();
		}
	}

	//获取打款成功短信知会任务
	private function _notifyPaymentCompleteSms(){

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
			return $result;
		}else{
			return array();
		}
	}

	//获取红包激活短信知会任务
	private function _notifyCashgiftActivedSms(){

		D('notify');
		$sendtype = \DAL\Notify::SENDTYPE_SMS; //短信通知

		I('sms');
		$sms = new sms();

		$ret = D('notify')->getCashgiftActivedJobs($sendtype);

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

				$content = $sms->getContent(\DAL\Notify::NOTIFYTYPE_CASHGIFT_ACTIVED, $params);
				$result[] = array('mobile' => $alipay, 'content'=>$content);
			}
		}

		if($result){
			return $result;
		}else{
			return array();
		}
	}
}
?>