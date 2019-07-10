// 地区跳转
$(function() {

    navigator.geolocation.getCurrentPosition(function (position) {
        $.get('/api/map/baiduLocation',{lat:position.coords.latitude, lng:position.coords.longitude},function(address){
            console.log(address);
            var the_all_area = address.result.addressComponent.province + address.result.addressComponent.city+' '+address.result.addressComponent.district;
            var the_area = address.result.addressComponent.city+' '+address.result.addressComponent.district;
            $('.area > a').html(the_area);
            localStorage.setItem('the-area', the_area);
            localStorage.setItem('the-all-area', the_all_area);
        },'json');
    }, function() {
        $('.area > a').html(localStorage.getItem('the-area'));
    });
});