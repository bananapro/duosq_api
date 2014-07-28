<?php
//移动应用接口
//用例：api.duosq.com:8080/mobi/suggest?ver=v1&k=iphone
class MobiController extends AppController {

	var $name = 'Mobi';
	var $version = 'v1';

	//route做了重定向，全部会指向此处
	function entry(){

		$action = $this->params['pass'][0];

		if(!$action)$this->_error('未指定接口名称', true);
		if(!@$_GET['ver']){
			$version = $this->version;
		}else{
			$version = $_GET['ver'];
		}
		$action = $action . '_' . $version;

		if(!method_exists($this, $action)){
			$this->_error('接口不存在', true);
		}
		call_user_func_array(array($this, $action), array());
	}

	//发送统计数据
	function s_v1(){

		$code_act = intval($_GET['op']);
		$status = intval($_GET['st']);

		$all_code = C('code_act');
		if(!isset($all_code[$code_act])){
			$this->_error('code_act');
		}

		$data = array();
		$data['data1'] = @$_GET['platform'];
		$data['data2'] = @$_GET['os'];
		$data['data3'] = @$_GET['app_ver'];
		$data['data4'] = @$_GET['device'];
		$data['data5'] = @$_GET['device_id'];
		D('log')->action($code_act, $status, $data);
		$this->_success('ok');
	}

	//搜索提示
	function suggest_v1(){

		$keyword = trim(@$_REQUEST['k']);

		if(!$keyword){
			$this->_error('字符为空或无效字符');
		}

		if(mb_strlen($keyword, 'utf8') > 100){
			$this->_error('请勿超过100字符');
		}

		$suggest = D('promotion')->getSuggest($keyword, 6, false);

		if($suggest){
			$ret = array();
			foreach($suggest as $n_k){
				$ret[] = $n_k;
			}
			$this->_success(array('content'=>$ret), true);
		}else{
			$this->_error(array('content'=>'找不到合适提示'), true);
		}
	}

	//手机按钮消息数提示
	function notifyNum_v1(){

		$data = array();

		$counter = D('promotion')->redis('promotion')->getPromoCatCountDate();

		$data['btn_1'] = count($counter['服装鞋子']);
		$data['btn_2'] = count($counter['手机数码']);
		$data['btn_3'] = count($counter['箱包配饰']);
		$data['btn_4'] = count($counter['美妆个护']);
		$data['btn_5'] = count($counter['母婴用品']);
		$data['btn_6'] = count($counter['家居日用']);
		$data['btn_7'] = count($counter['美食生鲜']);

		$data['btn_subscribe'] = 0;

		$device_id = @$_GET['device_id'];
		$platform = @$_GET['platform'];
		if(!$device_id || !valid($device_id, 'device_id') || !in_array($platform, array('ios','android'))){
			$this->_success(array('content'=>$data), true);
		}else{
			//读取未读订阅数
			$data['btn_subscribe'] = D('subscribe')->getUnOpenedMessageCount($device_id, $platform);
		}

		$this->_success(array('content'=>$data), true);
	}
}
?>