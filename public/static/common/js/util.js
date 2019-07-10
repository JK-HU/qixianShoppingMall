// 脚手架
let the_util = {};

// 经纬度转地址
the_util.lat_lng_to_address = function(lat, lng){
    $.get('/api/map/baiduLocation',{lat:lat, lng:lng},function(result){
        return result;
    },'json');
};