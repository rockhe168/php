define(function(require,exports,module){
    var Model = require("model/model");
    var wx=require("http://res.wx.qq.com/open/js/jweixin-1.0.0.js");
    var signatureModel = new Model.signature();
    var sendCouponModel = new Model.sendcoupon();
    var fanpaiModel= new Model.fanpai();
    var self;
    var token=0;
    module.exports= Backbone.View.extend({
        el:"body",
        initialize: function () {
            self=this;
            token=this.$el.find("#token").val();
            self.initWechat();
            self.initFanpai();
        },
        events: {
            "click .card ul li":"showFanpai",
            "click #sharetofriends":"shareTofriends"
        },
        initFanpai:function(){
            var status=this.$el.find("#fanpaiback").val();
            if(status==true||status=="true"){
                self.showFanpaiResult();
            }else{
                //self.showFanpaiResult();
                self.showFanpaiWelcome();
            }
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
                    imgUrl: imgurl, // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        sendCouponModel.set({"token":token})//组织参数
                        sendCouponModel.exec().then(function (data) {
                            if (data && data.code == 0) {
                                //成功跳转
                                App.jump("/unifi/mycenter");
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
        showFanpai:function(e){
            var el=$(e.currentTarget);
            var codeurl=el.data("codeurl");
            var slogan=el.data("slogan");
            var name=el.data("name");
            var gzid=el.data("id");
            //弹出窗口
            var tpl='<div class="two_code">\
                <i class="cross_pic"></i>\
                <span class="att_tit">长按二维码，关注【'+name+'】公众号，回复【国际义工】，点击提示消息中的【立即领抽奖码】即可</span>\
                <div class="code_ico">\
                <span><img src="'+codeurl+'" /></span>\
                <p>'+slogan+'</p>\
                </div>\
                </div>';
            var popwinfp=new App.UI.UIPopWin({
                template:tpl,
                events: {
                    'click .two_code .cross_pic': 'cancelAction'
                },
                cancelAction: function(){
                    popwinfp.hide();
                }
            })
            popwinfp.show();
            //发送翻牌请求
            fanpaiModel.set({"token":token,"gzid":gzid});
            fanpaiModel.exec().then(function (data) {
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
        shareTofriends:function(){
            var tpl='<div class="share_friend">\
                     <i class="cross_pic"></i>\
                     <span class="att_tit">点击右上角，选择【发送给朋友】，邀请好友，好友点击文章底部的【阅读原文】报名成功，您即可额外获得【抽奖码一个】，数量有限</span>\
                     <div class="code_ico"></div>\
                    </div>';
            var popwinshare=new App.UI.UIPopWin({
                template:tpl,
                events: {
                    'click .share_friend': 'cancelAction'
                },
                cancelAction: function(){
                    popwinshare.hide();
                }
            });
            popwinshare.show();
        },
        showFanpaiResult:function(){
            var wxname=this.$el.find("#wxname").val();
            var tpl='\
                <div class="draw_from">\
                    <h2>恭喜！您获得来自</h2>\
                    <span><i>'+wxname+'</i></span>\
                    <p>的抽奖码</p>\
                    <div class="draw_btn">确定</div>\
                </div>';
            var popwinfpresult=new App.UI.UIPopWin({
                template:tpl,
                events: {
                    'click .draw_from': 'cancelAction'
                },
                cancelAction: function(){
                    popwinfpresult.hide();
                }
            });
            popwinfpresult.show();
        },
        showFanpaiWelcome:function(){
            var chance=this.$el.find("#chance").val();
            if(parseInt(chance)>0) {
                var tpl = '\
                <div class="draw_yard">\
                <h2>恭喜！您有机会获得</h2>\
                <span><i>' + chance + '</i>个抽奖码</span>\
                <div class="draw_btn">翻牌赢取抽奖码</div>\
                </div>';
                var popwinfpwelcome = new App.UI.UIPopWin({
                    template: tpl,
                    events: {
                        'click .draw_yard': 'cancelAction'
                    },
                    cancelAction: function () {
                        popwinfpwelcome.hide();
                    }
                });
                popwinfpwelcome.show();
            }
        }
    })
})
