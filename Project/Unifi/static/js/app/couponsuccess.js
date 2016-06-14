define(function(require,exports,module){
    var Model = require("model/model");
    var wx=require("http://res.wx.qq.com/open/js/jweixin-1.0.0.js");
    var signInCouponModel = new Model.signInCoupon();
    var signatureModel = new Model.signature();
    var self;
    module.exports= Backbone.View.extend({
        el:"body",
        initialize: function () {
            self=this;
            self.initWechat();
        },
        events: {
            "click #submitcoupon":"signincoupon",
            "click #coupon_intro":"couponIntro"
        },
        initWechat:function(){
            // 用户确认分享后执行的回调函数
            signatureModel.set({"url":encodeURIComponent(window.location.href)});
            signatureModel.exec().then(function (data) {
                if (data && data.code == 0) {
                    //获取签名成功
                    self.startWechat(data.data);
                } else {
                    //获取签名失败
                    App.showToast("获取签名失败");
                }
            }).catch(function () {
                //失败
            })
        },
        startWechat:function(config){
            //配置签名
            wx.config({
                debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: config.appId, // 必填，公众号的唯一标识
                timestamp:config.timestamp , // 必填，生成签名的时间戳
                nonceStr: config.nonceStr, // 必填，生成签名的随机串
                signature: config.signature,// 必填，签名，见附录1
                jsApiList: ["onMenuShareTimeline","onMenuShareAppMessage"] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
            });

            wx.ready(function(){
                var shareurl=window.location.href;
                var imgurl="http://unifi.hih5.cn/static/images/free.jpg";
                var titleTimeline="UniFi送你一万元免息借款额度,快来领呀!";
                var titlepMessage="立即给你一万元现金,不要利息哦,让UniFi为你的梦想买单吧!";
                var descMessage="UniFi送你一万元免息借款额度,快来领呀!";
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title:titleTimeline, // 分享标题
                    link: shareurl, // 分享链接
                    imgUrl: imgurl, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                //分享给好友
                wx.onMenuShareAppMessage({
                    title: titlepMessage, // 分享标题
                    desc: descMessage, // 分享描述
                    link: shareurl, // 分享链接
                    imgUrl: imgurl, // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
        },
        couponIntro:function(){
            self.$el.find('.state_list').toggle();
        },
        switchAreaCode:function(e){
            self.$el.find('.district span').removeClass("current");
            $(e.currentTarget).addClass("current");
        },
        checkMobile: function () {
            var mobile = this.$el.find('#mobile').val();
            var re = /^0?1[123456789]\d{9}$/;
            if (mobile == "") {
                App.showToast("请输入手机号");
                return false;
            }
            if (re.test(mobile) != true) {
                App.showToast("您填写的是无效手机号，请重新填写");
                return false;
            }
            return true;
        }
    })
})
