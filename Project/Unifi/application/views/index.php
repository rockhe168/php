<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Unifi</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="screen-orientation" content="portrait"><!-- uc强制竖屏 -->
<meta name="x5-orientation" content="portrait"><!-- QQ强制竖屏 -->
<meta name="apple-mobile-web-app-status-bar-style" content="white"/>
<meta content="telephone=no,email=no" name="format-detection">
<link rel="stylesheet" href="<?php echo base_url('static/css/base.css') ?>">
<link rel="stylesheet" href="<?php echo base_url('static/css/vol_lation.css?v=20160606001') ?>">
</head>
<body>
<input type="hidden" id="token" value="<?= empty($userinfo) ? "" : $userinfo[UserInfo_Model::token]  ?>">
<section id="layout">
    <div class="img_head">
        <img src="<?php echo base_url('static/images/img1.png') ?>">
    </div>
    <div class="body">
        <dl class="js_switchTab">
            <dt>我想去</dt>
            <dd class="on" data-img="bg1">桑给巴尔</dd>
            <dd data-img="bg2">不丹</dd>
            <dd data-img="bg3">马尔代夫</dd>
        </dl>
        <div id="phone" class="phone">
            <span class="on">001</span>
            <span>086</span>
            <input type="number" id="mobile" placeholder="请输入手机号码" class="text">
        </div>
    </div>
    <div class="foot">
        <div class="tip">
            提醒：请认真填写，若您中奖，我们将会通过手机号码联系您
        </div>
        <input type="submit" class="submit" id="submit" value="下一步">
    </div>

</section>
<!-- 尾部-->
<?php $this->load->view('common/footer')?>
<script>
    //初始化页面JS
    require(["index"],function(index){
        new index();
    });
</script>
</body>
</html>