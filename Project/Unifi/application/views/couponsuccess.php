<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>免息-额度</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="<?php echo base_url('static/css/base.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('static/css/free_limit.css?v=20160606001') ?>">
</head>
<body>
    <input type="hidden" id="token" value="<?= empty($userinfo) ? "" : $userinfo[UserInfo_Model::token]  ?>">
    <div class="free_limit_two">
         <img src="<?php echo base_url('static/images/free.png') ?>" class="free_img">
        <div class="phone_call">
            <div class="cong">恭喜您，1万元免息额度</div>
            <span class="account">已存入您的UniFi账户中</span>
            <div class="once_get">下载UniFi，立即使用免息额度</div>
            <a href="javascript:void(0);" id="coupon_intro">免息游学额度说明>></a>
            <ul class="state_list">
                <li><span></span>领取成功后，下载UniFi APP申请借款。申请借款，即可享受一万元借款免息额度</li>
                <li><span></span>UniFi为此次公益活动提供3亿免息额度。先领先得，领完即止</li>
                <li><span></span>免息券仅限在美留学生使用~</li>
            </ul>
        </div>
        <div class="uni_app">
             <img src="<?php echo base_url('static/images/unifi_ico.png') ?>" class="un_ico">
            <div class="un_cont">
                <span class="tit">首款北美留学生专属的借款APP</span>
                <ul class="un_list">
                   <li><img src="<?php echo base_url('static/images/ya_ico.png') ?>"><span>借款</br>无抵押</span></li>
                    <li><img src="<?php echo base_url('static/images/time_ico.png') ?>"><span>APP</br>5分钟操作</span></li>
                    <li><img src="<?php echo base_url('static/images/coin_ico.png') ?>"><span>最高可借</br>10万</span></li>
                    <li ><img src="<?php echo base_url('static/images/safety_ico.png') ?>"><span >安全保密</span></li>
                </ul>
                <div class="uic_join">
                    <span class="jon_bg">UniFi&FICO达成战略合作</span>
                    <p>创造优良信用分数<br>
                        享受未来超低利率的北美信用生活</p>
                </div>
                <span class="foot_text">本次活动解释权归UniFi所有</span>
            </div>
        </div>
    </div>
    <!-- 尾部-->
    <?php $this->load->view('common/footer')?>
    <script>
        //初始化页面JS
        require(["couponsuccess"],function(couponsuccess){
            new couponsuccess();
        });
    </script>
</body>
</html>
