define(function (require, exports, module) {
	var initModel=function(opts){
		return App.Model.extend(opts || {});
	};
    var domain = "";
    var host=domain+"/api";
	var Model={
		//提交手机号码
		signIn : initModel({
			url:host+"/signin"
		}),
        //注册接口
		sendcoupon : initModel({
            url:host+"/sendpromocode"
        }),
		signature: initModel({
			url:host+"/signature"
		}),
		signInCoupon: initModel({
			url:host+"/signincoupon"
		}),
		fanpai: initModel({
			url:host+"/receivecode"
		})
	}
	module.exports= Model;
});
