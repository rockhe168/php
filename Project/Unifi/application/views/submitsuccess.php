<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Unifi</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="yes" name="apple-touch-fullscreen">
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="screen-orientation" content="portrait"><!-- uc强制竖屏 -->
<meta name="x5-orientation" content="portrait"><!-- QQ强制竖屏 -->
<meta name="apple-mobile-web-app-status-bar-style" content="white"/>
<meta content="telephone=no,email=no" name="format-detection">
<link rel="stylesheet" href="<?php echo base_url('static/css/base.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('static/css/succ_submit.css?v=20160606001') ?>">
</head>
<body>
<input type="hidden" id="token" value="<?= empty($userinfo) ? "" : $userinfo[UserInfo_Model::token]  ?>">
<div class="sub_success">
    <span>您已成功提交信息</span>
    <P>分享公益，点击右上角，选择【分享到朋友圈】，即可报名成功获取抽奖码</P>
    <img src="<?php echo base_url('static/images/wei_friend.jpg') ?>" />
</div>
<!-- 尾部-->
<?php $this->load->view('common/footer')?>
<script>
    //初始化页面JS
    require(["submitsuccess"],function(submitsuccess){
      new submitsuccess();
    });
</script>
</body>
</html>