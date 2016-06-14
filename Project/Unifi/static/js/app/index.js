define(function(require,exports,module){
    var Model = require("model/model");
    var signInModel = new Model.signIn();
    var self;
    module.exports= Backbone.View.extend({
        el:"body",
        initialize: function () {
            self=this;
        },
        events: {
            "click .js_switchTab dd":"switchTab",
            "click #phone span":"switchAreaCode",
            "click #submit":"signIn"
        },
        switchAreaCode:function(e){
            self.$el.find('.phone span').removeClass("on");
            $(e.currentTarget).addClass("on");
        },
        switchTab:function(e){
            self.$el.find('.js_switchTab dd').removeClass("on");
            $(e.currentTarget).addClass("on");
            var classname=$(e.currentTarget).data("img");
            self.$el.find('#layout').attr({"class":""})
            self.$el.find('#layout').addClass(classname)
        },
        checkMobile: function () {
            var mobile = this.$el.find('#mobile').val();
            var re = /^0?1[123456789]\d{9}$/;
            if (mobile == "") {
                App.showToast("请输入手机号");
                return false;
            }
            if (re.test(mobile) != true) {
                App.showToast("手机号格式不正确");
                return false;
            }
            return true;
        },
        
        //根据QueryString参数名称获取值
        getQueryStringByName:function(name){
            var result = location.search.match(new RegExp("[\?\&]" + name+ "=([^\&]+)","i"));
            if(result == null || result.length < 1){
                return "";
            }
            return result[1];
        },

        signIn:function(){
            if(this.checkMobile()) {
                var btn = this.$el.find('#submit');
                var mobile = this.$el.find('#mobile').val();
                var destination = this.$el.find('.js_switchTab .on').text();
                var token=this.$el.find("#token").val()||self.getQueryStringByName("token");
                var gzid=self.getQueryStringByName("gzid")||9999;
                var areacode=this.$el.find('.phone .on').text();
                App.showLoading();
                signInModel.set({"token":token,"gzid":gzid,"mobile": areacode+mobile, "destination": destination})//组织参数
                signInModel.exec().then(function (data) {
                    App.hideLoading();
                    if (data && data.code == 0) {
                        //成功跳转
                        App.jump("/unifi/submitsuccess?token="+token);
                    } else {
                        App.showToast(data.msg);
                    }
                }).catch(function () {
                    App.hideLoading();
                })
            }
        }
    })
})
