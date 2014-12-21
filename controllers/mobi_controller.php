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
			$this->_error('字符为空或无效字符', true);
		}

		if(mb_strlen($keyword, 'utf8') > 100){
			$this->_error('请勿超过100字符', true);
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

		$stat = D('promotion')->getStat();
		$counter = $stat['num_promo_cat_today'];

		$data['100'] = intval(@$counter['服装鞋子']);
		$data['101'] = intval(@$counter['手机数码']);
		$data['104'] = intval(@$counter['箱包配饰']);
		$data['103'] = intval(@$counter['美妆个护']);
		$data['105'] = intval(@$counter['母婴用品']);
		$data['106'] = intval(@$counter['家居日用']);
		$data['107'] = intval(@$counter['美食生鲜']);

		//旧版废除，显示假数据
		$data['111'] = 87;
		$data['100'] = 82;
		$data['101'] = 30;
		$data['104'] = 42;
		$data['103'] = 53;
		$data['105'] = 64;
		$data['106'] = 42;
		$data['107'] = 26;
		$device_id = @$_GET['device_id'];
		$platform = @$_GET['platform'];
		if(!$device_id || !valid($device_id, 'device_id') || !in_array($platform, array('ios','android'))){
			$this->_success(array('content'=>$data), true);
		}else{
			//读取未读订阅数
			//$data['111'] = D('subscribe')->getUnOpenedMessageCount($device_id, $platform);
		}
		$this->_success(array('content'=>$data), true);
	}

	//android系统升级
	function versionCheck_v1(){

		$data = array();
		$data['latestVersion'] = 4;
		$data['url'] = 'http://www.duosq.com/appconfig/android_2.0.apk';
		$data['message'] = '我们有最新版本了，超给力';
		$data['forceUpdate'] = 1;
		$this->_success($data, true);
	}
}
?>