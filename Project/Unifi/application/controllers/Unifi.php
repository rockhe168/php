<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unifi extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		//$this->load->model('userInfo_Model');
		//$this->load->model('userCode_Model');
		//$this->load->model('gzhConfig_Model');
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

	//报名页过来 or 个人中心 统一入口
	public function index()
	{
		$code = $this->input->get_post('code');//公众号code
		//$token = $this->input->get_post('token');//针对完成页跳转过来
		//if(!empty($token)) {
			//redirect(base_url("/unifi/mycenter?token=".$token));
		//}
		//来自公众号认证 or 报名页
		if(!empty($code))
		{
			//echo $code;
			//获取用户信息
			$webChatUserinfo = Tool::getWebChatUserInfo($code);
			$fromgzhNo = $this->input->get_post('gzhNo');//来源公众号
			if($webChatUserinfo !=null && !empty($webChatUserinfo["openid"]))
			{
				//从微信公众平台获取openid
			    $openid =$webChatUserinfo["openid"];
				//查询用户是否存在
				$userinfo = $this->userInfo_Model->getUserInfo($openid);
				//var_dump($userinfo);
				//var_dump($webChatUserinfo);
				if(!empty($userinfo))
				{
					//来着分享，先更新微信信息
					if(!empty($userinfo[UserInfo_Model::recommendToken]) && empty($userinfo[UserInfo_Model::mobile]))
					{
						$now = date('Y-m-d H:i:s');
						$where = array(UserInfo_Model::token => $userinfo[UserInfo_Model::token]);
						$setUserInfoData = array(UserInfo_Model::gzhNo=>$fromgzhNo,UserInfo_Model::userName=>Tool::filterWeChatNickName($webChatUserinfo["nickname"]),UserInfo_Model::icon=> $webChatUserinfo["headimgurl"],UserInfo_Model::lastUpdateTime => $now);
						//1、修改用户
						$this->userInfo_Model->update($setUserInfoData,$where);
					}
					//var_dump($userinfo);
					$data["userinfo"] = $userinfo;
					if(empty($userinfo[UserInfo_Model::mobile]))
					{
						$this->load->view('index',$data);
					}else {
						redirect(base_url("/unifi/mycenter?token=".$openid));
					}
				}else
				{
					try {
						//注册（报名）
						$mobile = $this->input->get_post('mobile');
						$destnation = $this->input->get_post('destination');

						$now = date('Y-m-d H:i:s');
						$userinfo = array(
							UserInfo_Model::mobile => $mobile,
							UserInfo_Model::token => $openid,
							UserInfo_Model::userName => Tool::filterWeChatNickName($webChatUserinfo["nickname"]),
							UserInfo_Model::icon => $webChatUserinfo["headimgurl"],
							UserInfo_Model::activityName => $destnation,
							UserInfo_Model::gzhNo => $fromgzhNo,
							UserInfo_Model::createTime => $now,
							UserInfo_Model::lastUpdateTime => $now
						);

						//1、新增用户
						$this->userInfo_Model->add($userinfo);


						$data["userinfo"] = $userinfo;
						$this->load->view('index',$data);

					}catch (Exception $e)
					{
						$this->load->view('index');
					}

				}

			}else{
				$this->load->view('index');
			}
		}
		else{
			$this->load->view('index');
		}
	}

	//进入个人页
	public function mycenter(){

	    	$token = $this->input->get_post('token');//针对完成页跳转过来
			//查询用户是否存在
		    $userInfo = null;
			if(empty($userInfo))
			{
				$userinfoModel = UserInfo_Model::getInstance();
				$userInfo = $userinfoModel->getUserInfo($token); //$this->userInfo_Model->getUserInfo($token);
				//$userinfoModel->ge
				$userinfoModel->isExistsMobile();

			}
			if(!empty($userInfo))
			{
				$data['userinfo'] = $userInfo;
				//查询个人优惠码
				$codeInfoList = $this->userCode_Model->getUserCodeList($token);

				$isexists=false;
				foreach ($codeInfoList as $codeinfo) {
					if ($codeinfo[UserCode_Model::source] == 1 && $codeinfo[UserCode_Model::status] == 0) {
						$isexists = true;
						break;
					}
				}
				if($isexists)
				{
					$data["userinfo"] = $userInfo;
					//$this->load->view('couponsuccess',$data);
					//return;
					redirect(base_url("/unifi/submitsuccess?token=".$token));
				}
                //查询公众号列表
				$gzhConfigList = $this->gzhConfig_Model->getAllGzhConfig();
				$data['gzhList'] = $gzhConfigList;
				$data['codeInfoList'] = $codeInfoList;
				$data['userinfo'] = $userInfo;
				//跳转个人中心
				$this->load->view('mycenter',$data);
			}
	}

	//进入报名成功
	public function submitsuccess(){
		    $token = $this->input->get_post('token');
		    $userInfo = $this->userInfo_Model->getUserInfo($token);
		    $data["userinfo"] = $userInfo;
			//echo $token;
			$this->load->view('submitsuccess',$data);
	}

	//个人中心点击翻牌
	public function fanpai()
	{
		$token = $this->input->get_post('token');//针对完成页跳转过来
		if (empty($token)) {
            redirect(base_url('unifi/index'));
		}

		//查询个人信息
		$userInfo = $this->userInfo_Model->getUserInfo($token);
		if (empty($userInfo)) {
			$this->load->view('index');
			return;
		}
		//查询个人的优惠码
		$codeInfoList = $this->userCode_Model->getUserCodeList($token);

		//查询公众号列表
		$gzhConfigList = $this->gzhConfig_Model->getAllGzhConfig();

		//查找还有几次可以领用
		$chance=0;
		foreach ($codeInfoList as $codeinfo)
		{
			if($codeinfo[UserCode_Model::status] != 1 && $codeinfo[UserCode_Model::source]==4)
			{
				$chance++;
			}
		}

		$data['userinfo'] = $userInfo;
		$data['codeInfoList'] = $codeInfoList;
		$data['gzhList'] = $gzhConfigList;
		$data["back"] =false;
		$data["chance"] = $chance;
		$data["wxname"] ="";
		$this->load->view('fanpai',$data);
	}

    function getcode(){
        $gzid = $this->input->get_post('gzid');
        $fanpaibackurl=urlencode(base_url("unifi/fanpaiback?gzid=". $gzid));
        $redirect_uri="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3dbe22669284765c&redirect_uri=". $fanpaibackurl ."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        redirect($redirect_uri,"refresh");
    }

	//翻牌reback  action
	public function fanpaiback()
	{
		$code = $this->input->get_post('code');//公众号code
		$gzid = $this->input->get_post('gzid');//公众号
		if (empty($code)) {
            redirect(base_url('unifi/index'));
		}

		if(!empty($code)) {
			//获取用户信息
			$webChatUserinfo = Tool::getWebChatTokenInfo($code); //Tool::getWebChatUserInfo($code);
			//从微信公众平台获取openid
			$token =$webChatUserinfo["openid"];
			//查询用户是否存在
			$userInfo = $this->userInfo_Model->getUserInfo($token);
			if(!empty($userInfo)) {
				//查询此优惠码
				$codeInfo = $this->userCode_Model->getUserCodeInfoByFanPai($token);
				if(empty($codeInfo))
				{
                    redirect(base_url('unifi/index'));
				}
				try {
					//update 优惠码状态
					$now = date('Y-m-d H:i:s');
					$where = array(UserCode_Model::token => $token, UserCode_Model::status => 2,UserCode_Model::gzhNo=>$gzid);
					$setDate = array(UserCode_Model::lastUpdateTime => $now, UserCode_Model::status => 1);
					$this->userCode_Model->update($setDate,$where);


					//跳转到翻牌成功页
					//查询个人的优惠码
					$codeInfoList = $this->userCode_Model->getUserCodeList($token);
					//查询公众号列表
					$gzhConfigList = $this->gzhConfig_Model->getAllGzhConfig();
					//查找还有几次可以领用
					$chance=0;
					$wxname="";
					foreach ($codeInfoList as $codeinfo)
					{
						if($codeinfo[UserCode_Model::status] != 1 && $codeinfo[UserCode_Model::source]==4)
						{
							$chance++;
						}
					}
					foreach ($gzhConfigList as $gzh)
					{
						if($gzh[GzhConfig_Model::gzhNo] == $gzid)
						{
							$wxname = $gzh[GzhConfig_Model::title];
						}
					}
					$data['userinfo'] = $userInfo;
					$data['codeInfoList'] = $codeInfoList;
					$data['gzhList'] = $gzhConfigList;
					$data["back"] =true;
					$data["chance"] = $chance;
					$data["wxname"] =$wxname;

					$this->load->view('fanpai',$data);
				}catch(Exception $e){
                    redirect(base_url('unifi/index'));
				}
			}
		}

	}

    function arcitle(){

		$token = $this->input->get_post('token');
		$code = $this->input->get_post('code');
		$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$access_tokenInfo  = Tool::getWebChatTokenInfo($code);
		$openid =$access_tokenInfo["openid"];

		try{

			$isUserInfo = $this->userInfo_Model->getUserInfo($openid);
			if(empty($isUserInfo)) {
				$now = date('Y-m-d H:i:s');
				$userinfo = array(
					UserInfo_Model::token => $access_tokenInfo["openid"],
					UserInfo_Model::recommendToken => $token,
					UserInfo_Model::createTime => $now,
					UserInfo_Model::lastUpdateTime => $now
				);
				//1、新增用户
				$userInfoStaue = $this->userInfo_Model->add($userinfo);
			}
		}catch (Exception $e)
		{

		}
		
        //文章jump页面
        redirect("http://mp.weixin.qq.com/s?__biz=MzAwMDg3ODkzMw==&mid=502431199&idx=1&sn=0e7b79ac6675f81c66eee6fbac5cabae#rd","refresh");
    }

    function coupon(){
        $this->load->view('coupon');
    }

    function couponsuccess(){
        $this->load->view('couponsuccess');
    }

}
