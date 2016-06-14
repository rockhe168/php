define(function (require, exports, module) {
    var initStore=function(opts){
        var store = App.Util.LocalStorage.extend(opts);
        return store;
    }
    var Store = {
        loginStore: initStore({
            key: 'S_UNIFI_LOGIN_STATUS',
            lifeTime: '1D'
        })
    };
    module.exports= Store;
});