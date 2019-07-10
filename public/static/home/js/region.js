(function() {
    var ul = document.createElement('ul');
    // 监听input值
    $('#search').bind('input propertychang',function(event) {
        var iptval = $(this).val();
        console.log(iptval)
        $('.con').eq(0).attr('style','display:none');
        $('.cover').eq(0).attr('style','display:block');


        if (iptval == '') {
            $('.con').eq(0).attr('style','display:block');
            $('.cover').eq(0).attr('style','display:none');
        }
        if (iptval !== '') {
            $('.con').eq(0).attr('style','display:none');
            $('.cover').eq(0).attr('style','display:block');
            $.ajax({
                url : '/home/index/getRegionList',
                type : 'POST',
                dataType : 'json',
                data : {
                    'iptval' : iptval
                },
                success : function(data) {
                    $('.cover ul').html(' ');
                    if (data.status === 1) {
                        for (let i in data.data) {
                            let li = document.createElement('li');
                            li.innerHTML = data.data[i].name;
                            ul.append(li);
                        }
                        $('.cover').eq(0).append(ul);
                    } else {

                    }

                },
                error : function(data) {
                    
                }
           });

        }
    

    });


    // 获取焦点事件
    $("input").focus(function() {
        $('#search').eq(0).addClass('anima');
        $('.isearch').eq(0).addClass('animb');
        $('.cancel').eq(0).attr('style','display:block;transition:all 0.6s')
        
    });

    // 取消事件
    $('.cancel').bind('click',function(event) {
        $('#search').eq(0).removeClass('anima');
        $('.isearch').eq(0).removeClass('animb');
        $('.cancel').eq(0).attr('style','display:none');
        $('.con').eq(0).attr('style','display:block');
        $('.cover').eq(0).attr('style','display:none');        

        event.stopPropagation(); //阻止事件冒泡
    });

    navigator.geolocation.getCurrentPosition(function (position) {
        $.get('/api/map/baiduLocation',{lat:position.coords.latitude, lng:position.coords.longitude},function(address){
            var the_area = address.result.addressComponent.city+' '+address.result.addressComponent.district;
            $('.local_area li').html(the_area);
            localStorage.setItem('the-area', the_area);
        },'json');
    });

    // 点击地区，赋值，跳转
    $(document).on('click', '.hot_area li,.local_area li,.area li,.cover li', function(){
        localStorage.setItem('the-area', $(this).html());
        location.href='/';
    });

})();

