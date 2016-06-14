requirejs.config({
    baseUrl: "/static/js",
    urlArgs: "v="+new Date().getTime(),
    paths: {
        'index': 'app/index',
        'mycenter':'app/mycenter',
        'fanpai':'app/fanpai',
        'submitsuccess':'app/submitsuccess',
        'coupon':'app/coupon',
        'couponsuccess':'app/couponsuccess'
    }
});
//绑定fastclick
App.FastClick.attach(document.body);

