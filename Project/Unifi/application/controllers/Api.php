<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		/*$this->load->model('userInfo_Model');
		$this->load->model('userCode_Model');
		$this->load->model('gzhConfig_Model');
		$this->load->model('config_Model');
		$this->load->model('coupon_Model');*/
	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	//高级功能-》开发者模式-》获取
	private $app_id = 'wx3dbe22669284765c';
	private $app_secret = '27ccbc22f1ce56952a682af53e933733';

	//注册
	public function signin()
	{
		$openid = $this->input->get_post('token');//公众号code
		//来自公众号认证 or 报名页
		if(!empty($openid))
		{

				//查询用户是否存在
				$userInfo = $this->userInfo_Model->getUserInfo($openid);
				if(empty($userInfo) )
				{
					$data["token"] = $userInfo["token"];
					echo json_encode(array('code' => 0, 'msg' => '非法请求！', 'data' => $userInfo));
				}else{
					try{
						//注册（报名）
						$mobile = $this->input->get_post('mobile');
						$destnation = $this->input->get_post('destination');
						$recommendToken = $this->input->get_post('recommendToken');//推荐好友的openid
						$fromgzhNo = $this->input->get_post('gzid');//来源公众号

						$isExistsMobile = $this->userInfo_Model->isExistsMobile($mobile);
						if($isExistsMobile)
						{
							echo json_encode(array('code' => 1, 'msg' => '该手机号已报名!'));
							return;
						}

						$now = date('Y-m-d H:i:s');
						$where = array(UserInfo_Model::token => $openid);
						$setUserInfoData = array(UserInfo_Model::mobile=>$mobile,UserInfo_Model::activityName=>$destnation,UserInfo_Model::gzhNo=>$fromgzhNo,UserInfo_Model::lastUpdateTime => time());

						//1、修改用户
						$userInfoStaue = $this->userInfo_Model->update($setUserInfoData,$where);

						//2、发送优惠卷
						//2.1、发送注册优惠码
						$userCode_reginData=array(
							UserCode_Model::token=>$openid,
							UserCode_Model::source=>1,
							UserCode_Model::code=>$this->userCode_Model->generateCode(),
							UserCode_Model::status=>0,
							UserCode_Model::createTime=>$now,
							UserCode_Model::lastUpdateTime=>$now,
							UserCode_Model::gzhNo=>$fromgzhNo
						);
						$userCode_reginState = $this->userCode_Model->add($userCode_reginData);
						//2.2 发送翻牌优惠码
						$this->insertFanpai($fromgzhNo,$openid);

						//3.如果来自好友推荐，给好友发送优惠码
						if(!empty($userInfo[UserInfo_Model::recommendToken]))
						{
							$recommenduserInfo = $this->userInfo_Model->getUserInfo($userInfo[UserInfo_Model::recommendToken]);
							$userCode_recommendData=array(
								UserCode_Model::token=>$userInfo[UserInfo_Model::recommendToken],
								UserCode_Model::source=>3,
								UserCode_Model::code=>$this->userCode_Model->generateCode(),
								UserCode_Model::status=>1,
								UserCode_Model::gzhNo=>$recommenduserInfo[UserInfo_Model::gzhNo],
								UserCode_Model::createTime=>$now,
								UserCode_Model::lastUpdateTime=>$now
							);
							$userCode_recommendState = $this->userCode_Model->add($userCode_recommendData);
						}

						//跳转到报名成功页
						//$data["token"] = $openid;
						//$this->load->view('submitsuccess',$data);
						$userInfo = $this->userInfo_Model->getUserInfo($openid);
						echo json_encode(array('code' => 0, 'msg' => '', 'data' => $userInfo));
					}catch(Exception $e)
					{
						echo json_encode(array('code' => 1, 'msg' => '微信认证失败!'));
					}
				}
		}else {
			echo json_encode(array('code' => 1, 'msg' => '非法请求！'));
		}
	}

	//签名
	public  function  signature()
	{
		//$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$url = urldecode($this->input->get_post("url"));

		$config = $this->config_Model->getWeConfig();

		//$data = Tool::getWeChatWxConfig($url, $config);
		$data = $this->get_wx_signature_config($url, $config);
		echo json_encode(array('code' => 0, 'msg' => '','data' =>$data));
	}

	//新增翻牌优惠码
	private function insertFanpai($fromgzhNo,$openid)
	{
		//1、查询配置公众号来源-->翻牌推的公众号信息
		if (empty($fromgzhNo)) {
			$fromgzhNo = '9999';//默认来源公众号9999
		}
		$gzhConfig = $this->gzhConfig_Model->getGzhConfigByGzhNo($fromgzhNo);
		if (empty($gzhConfig)) {
			$fromgzhNo = '9999';//默认来源公众号9999
			$gzhConfig = $this->gzhConfig_Model->getGzhConfigByGzhNo($fromgzhNo);
		}

		$now = date('Y-m-d H:i:s');
		//发送翻牌1
		$userCode_fanpaiData1 = array(
			UserCode_Model::token => $openid,
			UserCode_Model::source => 4,
			UserCode_Model::code => $this->userCode_Model->generateCode(),
			UserCode_Model::status => 0,
			UserCode_Model::gzhNo => $gzhConfig[GzhConfig_Model::recommendGzhNo1],
			UserCode_Model::createTime=>$now,
			UserCode_Model::lastUpdateTime=>$now
		);
		$userCode_fanpaiState1 = $this->userCode_Model->add($userCode_fanpaiData1);

		//发送翻牌2
		$userCode_fanpaiData2 = array(
			UserCode_Model::token => $openid,
			UserCode_Model::source => 4,
			UserCode_Model::code => $this->userCode_Model->generateCode(),
			UserCode_Model::status => 0,
			UserCode_Model::gzhNo => $gzhConfig[GzhConfig_Model::recommendGzhNo2],
			UserCode_Model::createTime=>$now,
			UserCode_Model::lastUpdateTime=>$now
		);
		$userCode_fanpaiState2 = $this->userCode_Model->add($userCode_fanpaiData2);

		//发送翻牌3
		$userCode_fanpaiData3 = array(
			UserCode_Model::token => $openid,
			UserCode_Model::source => 4,
			UserCode_Model::code => $this->userCode_Model->generateCode(),
			UserCode_Model::status => 0,
			UserCode_Model::gzhNo => $gzhConfig[GzhConfig_Model::recommendGzhNo3],
			UserCode_Model::createTime=>$now,
			UserCode_Model::lastUpdateTime=>$now
		);
		$userCode_fanpaiState2 = $this->userCode_Model->add($userCode_fanpaiData3);
	}


	//分享朋友圈送优惠码接口（针对分享朋友圈）
	public function sendpromocode()
	{
		$token = $this->input->get_post('token');
		//验证token
		if (empty($token)) {
			echo json_encode(array('code' => 1, 'msg' => '非法请求！'));
			die();
		}

		try {

			$isExistsSign = $this->userCode_Model->isExistsSignCode($token);
			if($isExistsSign)
			{
				echo json_encode(array('code' => 0, 'msg' => ''));
				return;
			}

			$now = date('Y-m-d H:i:s');
			$where = array(UserCode_Model::token => $token, UserCode_Model::source => 1);
			$setDate = array(UserCode_Model::lastUpdateTime => $now, UserCode_Model::status => 1);
			$this->userCode_Model->update($setDate,$where);
			echo json_encode(array('code' => 0, 'msg' => ''));
		}catch(Exception $e){
			echo json_encode(array('code' => 99999, 'msg' => '领取失败！'));
		}

		//查询此人是否已经送过分享朋友圈优惠吗
		/*$isExistsFriendCode =  $this->userCode_Model->isExistsFriendCode($token);
		if($isExistsFriendCode===true)
		{
			echo json_encode(array('code' => 0, 'msg' => ''));
		}else
		{
			try	{
				$now = date('Y-m-d H:i:s');
				$userCode_reginData=array(
					UserCode_Model::token=>$token,
					UserCode_Model::source=>2,
					UserCode_Model::code=>$this->userCode_Model->generateCode(),
					UserCode_Model::status=>1,
					UserCode_Model::createTime=>$now,
					UserCode_Model::lastUpdateTime=>$now
				);
				$state = $this->userCode_Model->add($userCode_reginData);
				echo json_encode(array('code' => 0, 'msg' => ''));
			}catch(Exception $e){
				echo json_encode(array('code' => 99999, 'msg' => '领取失败！'));
			}
		}*/
	}

	//翻牌接口
	public function receivecode()
	{
		$token = $this->input->get_post('token');
		$gzhNo = $this->input->get_post('gzid');

		$now = date('Y-m-d H:i:s');
		//验证token
		if (empty($token) || empty($gzhNo)) {
			echo json_encode(array('code' => 1, 'msg' => '非法请求！'));
			die();
		}

		//查询此优惠码
		$codeInfo = $this->userCode_Model->getUserCodeInfoByGzhNo($token,$gzhNo);

		if(empty($codeInfo))
		{
			echo json_encode(array('code' => 1, 'msg' => '非法请求！'));
			die();
		}
		else {
			try {
				$where = array(UserCode_Model::token => $token, UserCode_Model::gzhNo => $gzhNo);
				$setDate = array(UserCode_Model::lastUpdateTime => $now, UserCode_Model::status => 2);
				$state = $this->userCode_Model->update($setDate,$where);
				echo json_encode(array('code' => 0, 'msg' => ''));
			}catch(Exception $e){
				echo json_encode(array('code' => 99999, 'msg' => '领取失败！'));
			}
		}
	}

	//翻牌后的公众号信息
	public function getGzhInfo(){
		$gzhNo = $this->input->get_post('gzhNo');
		//验证gzhNo
		if (empty($gzhNo)) {
			echo json_encode(array('code' => 1, 'msg' => '非法请求！'));
			die();
		}
		//查询数据
		$gzhConfig = $this->gzhConfig_Model->getGzhConfigByGzhNo($gzhNo);
		echo json_encode(array('code' => 0, 'msg' => '','data' =>$gzhConfig));
	}

	public function signincoupon()
	{
		try {
			$mobile = $this->input->get_post('mobile');
			$isExistsMobile = $this->coupon_Model->isExistsMobile($mobile);
			if($isExistsMobile)
			{
				echo json_encode(array('code' => 1, 'msg' => '该手机号已经领取1万免息额度，请勿重复领取哦!'));
				return;
			}

			$now = date('Y-m-d H:i:s');
			$signcoupn = array(
				Coupon_Model::mobile => $mobile,
				Coupon_Model::createTime => $now,
				Coupon_Model::lastUpdateTime => $now
			);
			$this->coupon_Model->add($signcoupn);
			echo json_encode(array('code' => 0, 'msg' => ''));
			}catch(Exception $e) {
			echo json_encode(array('code' => 1, 'msg' => '领取失败！'));
		}

	}

	//获取签名access_token
	private function get_signature_access_token($config)
	{
		$now = date('Y-m-d H:i:s');
		if(!empty($config[Config_Model::access_token_cache]) && !empty($config[Config_Model::access_token_expires_in]) && (strtotime($config[Config_Model::access_token_expires_in]) > strtotime($now)))
		{
			return $config[Config_Model::access_token_cache];
		}
		else {
			$access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->app_id}&secret={$this->app_secret}";
			$access_token_data = $this->http($access_token_url, "GET", null, null, false);
			if ($access_token_data[0] == 200) {
				$data = json_decode($access_token_data[1], true);
				try {
					$expires = date('Y-m-d H:i:s',time() + ( 60 * 60));
					$where = array(Config_Model::configkey=>'wxconfig');
					$setDate = array(Config_Model::access_token_cache => $data["access_token"], Config_Model::access_token_expires_in => $expires,Config_Model::lastUpdateTime=>$now);
					$this-> config_Model-> update($setDate,$where);
				}catch(Exception $e){
				}

				return $data["access_token"];
			}
		}
		return FALSE;
	}

	//获取签名jsapi_ticket
	private function get_jsapi_ticket($config)
	{
		$now = date('Y-m-d H:i:s');
		$endTime = strtotime($config[Config_Model::jsapi_ticket_expires_in]);
		$currentTime = strtotime($now);
		if(!empty($config[Config_Model::jsapi_ticket_cache]) && !empty($config[Config_Model::jsapi_ticket_expires_in]) && ( $endTime> $currentTime))
		{
			return $config[Config_Model::jsapi_ticket_cache];
		}else {
			$current_access_token = $this->get_signature_access_token($config);
			//$jsapi_ticket_url = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=s%&type=jsapi",$current_access_token);
			$jsapi_ticket_url = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi", $current_access_token);
			$jsapi_ticket_data = $this->http($jsapi_ticket_url, "GET", null, null, false);

			if ($jsapi_ticket_data[0] == 200) {
				$data = json_decode($jsapi_ticket_data[1], true);
				if($data["errcode"]==0) {

					$expires = date('Y-m-d H:i:s',time() + ( 60 * 60));
					$where = array(Config_Model::configkey=>'wxconfig');
					$setDate = array(Config_Model::jsapi_ticket_cache => $data["ticket"], Config_Model::jsapi_ticket_expires_in => $expires,Config_Model::lastUpdateTime=>$now);
					$this-> config_Model->update($setDate,$where);
					return $data["ticket"];
				}
				return false;
			}
		}
	}

	//生成微信签名的wx.config
	private function get_wx_signature_config($url,$config){
		$wx = array();

		$wx["appId"] = $this->app_id;
		//生成签名的时间戳
		$wx['timestamp'] = time();
		//生成签名的随机串
		$wx['nonceStr'] = 'Wm3WZYTPz0wzccnW';

		$wx["url"] = $url;

		$jsapi_ticket = $this->get_jsapi_ticket($config);

		$wx["ticket"] = $jsapi_ticket;
		$string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $jsapi_ticket, $wx['nonceStr'], $wx['timestamp'], $url);
		//生成签名
		$wx['signature'] = sha1($string);

		return $wx;
	}
	private function http($url, $method, $postfields = null, $headers = array(), $debug = false)
	{
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, true);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		//curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ci, CURLINFO_HEADER_OUT, true);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ci);
		$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		if ($debug) {
			echo "=====post data======\r\n";
			var_dump($postfields);
			echo '=====info=====' . "\r\n";
			print_r(curl_getinfo($ci));
			echo '=====$response=====' . "\r\n";
			print_r($response);
		}
		curl_close($ci);
		return array($http_code, $response);
	}


}
