define(function(require,exports,module){
    var Model = require("model/model");
    var wx=require("http://res.wx.qq.com/open/js/jweixin-1.0.0.js");
    var signatureModel = new Model.signature();
    var sendCouponModel = new Model.sendcoupon();
    var self;
    var token=0;
    module.exports= Backbone.View.extend({
        el:"body",
        initialize: function () {
            self=this;
            token=this.$el.find("#token").val();
            self.initWechat();
        },
        events: {
            "click .js_switchTab dd":"switchTab",
            "click .phone span":"switchAreaCode",
            "click #submit":"signIn"
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
                var url="http://unifi.hih5.cn/unifi/arcitle?token="+token;
                var redirect_uri=encodeURIComponent(url);
                var shareurl="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3dbe22669284765c&redirect_uri="+redirect_uri+"&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
                var imgurl="http://unifi.hih5.cn/static/images/unifi_wx.png";
                var titleTimeline="Uinifi 测试分享朋友圈";
                var titlepMessage="Uinifi 测试分享好友";
                var descMessage="Uinifi分享测试";
                //分享到朋友圈
                wx.onMenuShareTimeline({
                    title:titleTimeline, // 分享标题
                    link: shareurl, // 分享链接
                    imgUrl:imgurl, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        sendCouponModel.set({"token":token})//组织参数
                        sendCouponModel.exec().then(function (data) {
                            if (data && data.code == 0) {
                                //成功跳转
                                App.jump("/unifi/mycenter?token="+token);
                            } else {
                            }
                        }).catch(function () {
                            App.showToast("分享失败",2000)
                        })
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
                //分享给好友
                wx.onMenuShareAppMessage({
                    title:titlepMessage, // 分享标题
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


        }
    })
})
