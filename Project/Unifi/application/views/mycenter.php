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
<link rel="stylesheet" href="<?php echo base_url('static/css/vol_person.css?v=20160607002') ?>">
</head>
<body>
<!--token-->
<input type="hidden" id="token" value="<?=$userinfo[UserInfo_Model::token]?>">
<div class="vol_person">
    <div class="head_mes">
        <div class="header_ico"><img src="<?php echo $userinfo[UserInfo_Model::icon] ?>"></div>
        <p clsss="name"><?=$userinfo[UserInfo_Model::userName] ?></p>
        <p class="phone"><?=$userinfo[UserInfo_Model::mobile]?></p>
    </div>
    <div class="draw">
        <span class="current" id="sharetofriends">邀好友拿抽奖码</span>
        <span> <a href="<?php $url='unifi/fanpai?token='.$userinfo[UserInfo_Model::token]; echo base_url($url) ?>" id="startfanpai">翻牌赢多个抽奖码</a> </span>
    </div>
    <div class="draw_tit">我的抽奖码</div>
            <?php foreach ($codeInfoList as $codeinfo):?>
                <?php if(!empty($codeinfo) && $codeinfo[UserCode_Model::status] ==1):?>
                    <div class="my_draw">
                        <span><?=$codeinfo[UserCode_Model::code]?></span>
                        <i>
                            <?php foreach ($gzhList as $gzh):?>
                                <?php if(!empty($gzh) && !empty($codeinfo[UserCode_Model::gzhNo]) && ($gzh[GzhConfig_Model::gzhNo] == $codeinfo[UserCode_Model::gzhNo])):?>
                                    <p>
                                        <?php if(empty($codeinfo[UserCode_Model::source]==3)):?>邀请奖励
                                        <?php else:?>
                                        <?=$gzh[GzhConfig_Model::title]?>
                                        <?php  endif; ?>
                                    </p>
                                <?php  endif; ?>
                            <?php endforeach;?>
                            <?php if(empty($codeinfo[UserCode_Model::gzhNo])):?>
                                <p><?php if(empty($codeinfo[UserCode_Model::source]==3)):?>邀请奖励<?php  endif; ?></p>
                            <?php  endif; ?>
                        <p><?=$codeinfo[UserCode_Model::lastUpdateTime]?></p>
                        </i>
                     </div>
                <?php endif; ?>
            <?php endforeach; ?>
    <div class="immediately">
        <a href="<?php echo base_url('unifi/coupon') ?>"><img src="<?php echo base_url('static/images/immed.png') ?>"  id="startcoupon"/></a>
    </div>
    <div class="tit_hint">
        温馨提示
    </div>
    <p>1.点击上方【翻牌赢抽奖码】，每翻开一张塔罗牌并成功报名，即可多获得1个抽奖码</p>
    <p>2.点击上方【邀好友拿抽奖码】，每成功邀请1位好友，即可多奖励1个抽奖</p>
    <p>3.本活动报名截止时间：2016年6月20日24时00分，2017年6月21日20时00分 公布中奖名单</p>
    <div class="tit_hint">
        获奖结果
    </div>
    <div class="explain">本活动获奖结果会在 <a href="javascript:void(0);">UniFi </a>公众号公布</div>
</div>


<!-- 尾部-->
<?php $this->load->view('common/footer')?>
<script>
    //初始化页面JS
    require(["mycenter"],function(mycenter){
        new mycenter();
    });
</script>
</body>
</html>