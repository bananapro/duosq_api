<?php
//广告接口，使用IP调用
//<script type="text/javascript" charset="UTF-8" src="http://121.199.10.168/adv/display"></script>
class AdvController extends AppController {

	var $name = 'Adv';
	var $signVerify = false; //是否验签
	var $layout = 'blank';

	function beforeRender(){}

	//根据条件显示广告
	function display(){

		//研发环境直接跳过条件，直接测试广告
		if(!isDevelop() && !isset($_COOKIE['test_adv'])){

			if(isset($_COOKIE['utmo_']))die('<!--utmo_-->');

			$area = getAreaByIp();
			$area_forbidden = array('上海', '南京', '北京');
			//$area_forbidden = array();

			//控制时间段
			if(date('H') >= 8 && date('H') <= 19){
				//die('<!--time-->');
			}

			//排除地址
			foreach($area_forbidden as $a){
				if(stripos($area, $a)!==false){
					die('<!--area-->');
				}
			}

			//每IP一天内显示1次
			$key = 'advertise:baozipu:ip:'.getIp();
			$cache = D('cache')->get($key);
			if($cache)die('<!--ip-->');
			D('cache')->set($key, 1, DAY, true);

			//种下已经访问过
			setcookie('utmo_', true, time() + WEEK, '/');

			//选取一定几率
			$rand = rand(1,10);
			//if($rand > 3)die('<!--rand-->');
		}
	}

	//点击广告
	function click(){

		if(isMobile()){
			$target = MY_WWW_URL.'/subscribe/app';
		}else{
			$target = '/subscribe';
		}

		$target = 'http://www.baozipu.com';

		$this->redirect(MY_WWW_URL . '/mark?sc=baozipu&t='.urlencode($target));
	}
}
?>