/**
 * Created by gangfeng on 2016/5/21.
 */
$(function(){

    /* 猎头个人中心 弹框修改电话 */

    $('.amend').on('click',function(){
        $('.shade').fadeIn(200);
        $('#amend_phone').fadeIn().stop().animate({top:'50%',opacity:'1'},400);
        $ (window).scroll (function ()
        {
            $ (this).scrollTop (0)
        });
    });
    $('.shade').on('click',function(){
        $('.shade').fadeOut(200);
        $('#amend_phone').stop().animate({top:'0',opacity:'0'},400).fadeOut();
        $ (window).off ('scroll');
    });

    /* 职位管理-改 职位选项卡 */
   $('.post_btn>span').hover(function(){
        $(this).addClass('current').siblings('span').removeClass('current');
       $(this).parent().siblings('.post').removeClass('active');
       $(this).parent().siblings('.post').eq($(this).index()).addClass('active');
   });

    /* 职位管理-增 填写信息选项卡 */
   $('.lin_write>a').click(function(){
       $(this).addClass('current').siblings('a').removeClass('current');
       var index=$(this).index();
       $(this).siblings($('.bottom_line')).animate({left:600*index},300);
       $(this).parent().siblings('div').removeClass('active');
       $(this).parent().siblings('div').eq(index).addClass('active');
   });

    /* 职位管理 勾选*/
    var tick= $('.radio_sty ').find($('.tick'));
   tick.click(function(){
       tick.removeClass('current');
      $(this).addClass('current');
    });
    var edtick=$('.tit_link').find($('.tick'));
    var  off=true;
    edtick.click(function(){
        if(off){
            $(this).addClass('current');
        }else{
            $(this).removeClass('current');
        }
        off=!off
    });

    /*提交审核资料页-分页 选项卡*/
    $('.att_list li').click(function(){
        var index=$(this).index();
        $(this).addClass('current').siblings().removeClass('current');
        $('.line').animate({left:400*index},200);
        $('.ver_select').children('div').eq(index).show().siblings('div').hide();
    });

    /*职位猎头-收藏展示页 选项卡*/
    var coll_post=$('.favorites').find($('.coll_post'));
    var coll_head=$('.favorites').find($('.coll_head'));
    var post=$('.favorites').find($('.post_list'));
    var head=$('.favorites').find($('.head_list'));
    coll_post.click(function(){
        $(this).addClass('current').siblings().removeClass('current');
        $('.had_tit').html('<span>职位名称</span> <span class="dres">所在地</span> <span>发布时间</span>');
        post.show();
        head.hide();
    })
    coll_head.click(function(){
        $(this).addClass('current').siblings().removeClass('current');
        $('.had_tit').html('<span>猎头姓名</span> <span class="dres">所在地</span> <span>擅长行业</span>');
        head.show();
        post.hide();
    })

});




