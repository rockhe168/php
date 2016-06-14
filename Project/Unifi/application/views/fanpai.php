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
<link rel="stylesheet" href="<?php echo base_url('static/css/vol_person.css?v=20160607003') ?>">
</head>
<body>
<input type="hidden" id="token" value="<?=$userinfo[UserInfo_Model::token]?>">
<input type="hidden" id="fanpaiback" value="<?=$back?>">
<input type="hidden" id="chance" value="<?=$chance?>">
<input type="hidden" id="wxname" value="<?=$wxname?>">
<div class="vol_draw">
    <div class="head_mes">
        <div class="header_ico"><img src="<?php echo $userinfo[UserInfo_Model::icon] ?>"></div>
        <p clsss="name"><?=$userinfo[UserInfo_Model::userName]?></p>
        <p class="phone"><?=$userinfo[UserInfo_Model::mobile]?></p>
    </div>
    <div class="draw">
        <span class="current" id="sharetofriends">邀好友拿抽奖码</span>
    </div>
    <div class="card">
        <div class="card_left"><img src="<?php echo base_url('static/images/card-1_03.png') ?>" /></div>
        <ul>
            <?php foreach ($codeInfoList as $codeinfo):?>
               <?php if(!empty($codeinfo) && $codeinfo[UserCode_Model::status] != 1 && $codeinfo[UserCode_Model::source]==4):?>
                    <?php foreach ($gzhList as $gzh):?>
                        <?php if(!empty($gzh) && !empty($codeinfo[UserCode_Model::gzhNo]) && ($gzh[GzhConfig_Model::gzhNo] == $codeinfo[UserCode_Model::gzhNo])):?>
                          <li data-codeurl="<?=$gzh[GzhConfig_Model::twoDimensionCode]?>" data-id="<?=$gzh[GzhConfig_Model::gzhNo]?>" data-name="<?=$gzh[GzhConfig_Model::title]?>" data-slogan="<?=$gzh[GzhConfig_Model::descinfo]?>"><img src="<?php echo base_url('static/images/card01_03.png') ?>" /></li>
                         <?php  endif; ?>
                    <?php endforeach;?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <div class="card_right"><img src="<?php echo base_url('static/images/654_05.png') ?>"></div>
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
</div>
<!-- 尾部-->
<?php $this->load->view('common/footer')?>
<script>
    //初始化页面JS
    require(["fanpai"],function(fanpai){
        new fanpai();
    });
</script>

</body>
</html>