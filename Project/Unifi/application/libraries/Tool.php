<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/27 0027
 * Time: 23:50
 */
class Tool
{
    /**
     * @param $code*
     * 根据code 获取微信用户信息
     */
      public static  function getWebChatUserInfo($code)
      {
          $data =array();

          //模拟数据
//          if($code=="888888") {
//              $data["openid"] = "888888";
//          }else {
//              $data["openid"] = $code;
//          }
//          $data["nickname"] = "昵称".rand(10,100);
//          $data["headimgurl"]="static/images/head_ico.png";
//
//          return $data;

          //--------------------------------------------------正式逻辑
          try{
              $wechat = new Wechat();
              //1、调用微信公众号平台，获取 access_token
              $access_tokenInfo = $wechat->get_access_token($code);
              //if(empty($access_tokenInfo) || isset($access_tokenInfo->errcode))
              if(empty($access_tokenInfo) || isset($access_tokenInfo["errcode"]))
              {
                  return null;
              }
              //2、调用微信公众号平台，获取userinfo-->openid
              $userinfo = $wechat->get_user_info($access_tokenInfo["access_token"],$access_tokenInfo["openid"]);

              if(empty($userinfo))
              {
                  return null;
              }
              return $userinfo;
          }catch (Exception $e)
          {
              return null;
          }

      }

      public static  function getWebChatTokenInfo($code)
    {
        $wechat = new Wechat();
        //1、调用微信公众号平台，获取 access_token
        $access_tokenInfo = $wechat->get_access_token($code);
        if(empty($access_tokenInfo))
        {
            return null;
        }
        return $access_tokenInfo;
    }

      public static function  getWeChatWxConfig($url,$config)
      {

          try{
                $wechat = new Wechat();
                return $wechat->get_wx_signature_config($url);

          }catch (Exception $e)
          {
              return null;
          }
      }

      public static function filterWeChatNickName($nickname)
      {
          $name = preg_replace('~\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]~', '', $nickname);
          return $name;
      }

      
}