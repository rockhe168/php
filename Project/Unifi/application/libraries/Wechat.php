<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/29 0029
 * Time: 12:03
 */
class Wechat {

    //高级功能-》开发者模式-》获取
    public $app_id = 'wx3dbe22669284765c';
    public $app_secret = '27ccbc22f1ce56952a682af53e933733';

    /**
     * 获取微信授权链接
     *
     * @param string $redirect_uri 跳转地址
     * @param mixed $state 参数
     */
    public function get_authorize_url($redirect_uri = '', $state = '')
    {
        $redirect_uri = urlencode($redirect_uri);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
    }

    /**
     * 获取授权token
     *
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token($code = '')
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http($token_url,"GET",null,null,false);

        if($token_data[0] == 200)
        {
            return json_decode($token_data[1], true);
        }

        return FALSE;
    }

    /**
     * 获取授权token
     *
     * @param string $code 通过get_authorize_url获取到的code
     */
    public function get_access_token_info($code = '')
    {
        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http($token_url,"GET",null,null,false);

        if($token_data[0] == 200)
        {
            return json_decode($token_data[1], true);
        }

        return FALSE;
    }

    /**
     * 获取授权后的微信用户信息
     *
     * @param string $access_token
     * @param string $open_id
     */
    public function get_user_info($access_token = '', $open_id = '')
    {
        if($access_token && $open_id)
        {
            $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
            $info_data = $this->http($info_url,"GET",null,null,false);

            if($info_data[0] == 200)
            {
                return json_decode($info_data[1], TRUE);
            }
        }

        return FALSE;
    }


    //获取签名access_token
    public function get_signature_access_token($config)
    {

        if(!empty($config[Config_Model::access_token_cache]) && !empty($config[Config_Model::access_token_expires_in]) && (strtotime($config[Config_Model::access_token_expires_in]) > strtotime(time("y-m-d h:i:s"))))
        {
            return $config[Config_Model::access_token_cache];
        }
        else {
            $access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->app_id}&secret={$this->app_secret}";
            $access_token_data = $this->http($access_token_url, "GET", null, null, false);
            if ($access_token_data[0] == 200) {
                $data = json_decode($access_token_data[1], true);
                try {
                    $where = array(Config_Model::configkey=>'wxconfig');
                    $setDate = array(Config_Model::access_token_cache => $data["access_token"], Config_Model::access_token_expires_in => $data["expires_in"],Config_Model::lastUpdateTime=>now());
                    $config>update($setDate,$where);
                }catch(Exception $e){
                }

                return $data["access_token"];
              }
        }
        return FALSE;
    }

    //获取签名jsapi_ticket
    public function get_jsapi_ticket($config)
    {
        if(!empty($config[Config_Model::jsapi_ticket_cache]) && !empty($config[Config_Model::jsapi_ticket_expires_in]) && (strtotime($config[Config_Model::jsapi_ticket_expires_in]) > strtotime(time("y-m-d h:i:s"))))
        {
            return $config[Config_Model::jsapi_ticket_cache];
        }else {
            $current_access_token = $this->get_signature_access_token();
            //$jsapi_ticket_url = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=s%&type=jsapi",$current_access_token);
            $jsapi_ticket_url = sprintf("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi", $current_access_token);
            $jsapi_ticket_data = $this->http($jsapi_ticket_url, "GET", null, null, false);

            if ($jsapi_ticket_data[0] == 200) {
                $data = json_decode($jsapi_ticket_data[1], true);
                if($data["errcode"]==0) {
                    $where = array(Config_Model::configkey=>'wxconfig');
                    $setDate = array(Config_Model::jsapi_ticket_cache => $data["ticket"], Config_Model::jsapi_ticket_expires_in => $data["expires_in"],Config_Model::lastUpdateTime=>now());
                    $config>update($setDate,$where);
                    return $data["ticket"];
                }
                return false;
            }
        }
    }

    //生成微信签名的wx.config
    public function get_wx_signature_config($url,$config){
        $wx = array();
        //生成签名的时间戳
        $wx['timestamp'] = time();
        //生成签名的随机串
        $wx['noncestr'] = 'Wm3WZYTPz0wzccnW';

        $jsapi_ticket = $this->get_jsapi_ticket($config);
        $string = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $jsapi_ticket, $wx['noncestr'], $wx['timestamp'], $url);
        //生成签名
        $wx['signature'] = sha1($string);

        return $wx;
    }

    public function http($url, $method, $postfields = null, $headers = array(), $debug = false)
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
