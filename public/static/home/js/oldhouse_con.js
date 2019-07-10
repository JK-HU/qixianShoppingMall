(function() {

    //控制排序显示隐藏
    var istrue = true;
    // 排序
    $('.sortlis').bind('click',function() {

        // 获取属性
        var attrs = $(this).attr('data-fun');
        // 获取栏目名
        var li_name = $(this).text();
        var indexs = $(this).index();

        $(this).attr('style','color:red');
        $(this).siblings().attr('style','color:#000');
        $(this).find('i').addClass('iconfont iconzhengque'); 
        $(this).siblings().find('i').removeClass('iconfont iconzhengque');
        $('.fun').eq(indexs).attr('style','display:block');
        $('.fun').eq(indexs).siblings().attr('style','display:none');
        $('.truesort').html(li_name);
        $('.sorta').attr('style','color:red;position:fixed;left:50%;transform:translateX(-50%);width:100%;height:auto;display:block;background:#fff');
        $('.sortli').eq(0).attr('style','display:none');
        $('.cover').eq(0).attr('style','display:none');
        $('.arrows').eq(0).addClass('iconarrow-up1');
        $('.arrows').eq(0).removeClass('iconarrow-up');

        // 请求数据
        // $.ajax({
        //     url : '',
        //     type : 'POST',
        //     dataType : 'json',
        //     data : {
        //         'attrs' : attrs, //每个li的data-type,用于判断标识
        //     },
        //     success : function(data) {

        //     },
        //     error : function(data) {

        //     }
        // });


        istrue = true;
        return false;

        

    });
    
    // 点击排序按钮
    $('.sorta').bind('click',function(e) {
        $('body').css("overflow", "hidden");//禁止页面滚动
        if (istrue){
            $('.sortli').eq(0).attr('style','display:block;margin-top:43px');
            $('.cover').eq(0).attr('style','display:block');
            $(this).eq(0).attr('style','color:red;position:fixed;left:50%;transform:translateX(-50%);width:100%;height:auto;display:block;background:#fff');
            $('.arrows').eq(0).removeClass('iconarrow-up1');
            $('.arrows').eq(0).addClass('iconarrow-up');
            istrue = false;
            return false;
        }
        if (!istrue){
            $('.sortli').eq(0).attr('style','display:none');
            $('.cover').eq(0).attr('style','display:none');
            $(this).eq(0).attr('style','color:#000;position:fixed;left:50%;transform:translateX(-50%);width:100%;height:auto;display:block;background:#fff');
            $('.arrows').eq(0).addClass('iconarrow-up1');
            $('.arrows').eq(0).removeClass('iconarrow-up');
            istrue = true;
            return false;
        }
        
        e.preventDefault();
    });





})();