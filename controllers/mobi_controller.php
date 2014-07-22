<?php
//移动应用接口
class MobiController extends AppController {

	var $name = 'Mobi';
	var $version = 'v1';

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

	//搜索提示
	function suggest_v1(){

		$keyword = $this->_fStr(trim(@$_REQUEST['k']));

		if(!$keyword){
			$this->_error('字符为空或无效字符');
		}

		if(mb_strlen($keyword, 'utf8') > 100){
			$this->_error('请勿超过100字符');
		}

		$suggest = D('promotion')->getSuggest($keyword);

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
	function notify_v1(){

		$data = array();
		$data['btn_1'] = 11;
		$data['btn_2'] = 22;
		$data['btn_3'] = 33;
		$data['btn_4'] = 44;
		$data['btn_5'] = 55;
		$data['btn_6'] = 66;
		$data['btn_7'] = 77;
		$data['btn_subscribe'] = 10;
		$this->_success(array('content'=>$data), true);
	}
}
?>