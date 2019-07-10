(function() {

    // 控制编辑
    var editFlag = false;
    // 用于全选(初始和_用于定义所有合计)
    var total_sum = 0; 

    // 用于存放所有商品的价格数组
    var sib_sum = 0;
    var sib_prise_arr = [];

    (function() {
        // console.log($('.iconflag'));
        var iconlen = $('.iconflag').length;
        for ( let i = 0 ; i < iconlen-1 ; i++){
            // console.log($('.iconflag').eq(i).attr('data-iflag'));
        }
        
        if ($('.iconflag').attr('data-iflag') == 'y'){
            $('.total_t_money').text(total_sum);
        }
        if ($('.iconflag').attr('data-iflag') == 'n'){
            $('.total_t_money').text(total_sum);
        }
        
        
    })();
    
    // 选中商品
    /**
     *点击当前增加按钮,获取这个值,然后再去找同级所有值,再相加 
     * 
     **/
    $('.iconflag').bind('click',function() {
        // 选中
        if ($(this).attr('data-iflag') == 'n'){
            $(this).removeClass('iconfont iconwxz');
            $(this).addClass('iconfont iconxz');
            $(this).addClass('flag_i_xz');

            if ($(this).attr('data-iflag') == 'n'){
                var aa = $(this).siblings().find('.total_mon').text();
                total_sum += Number(aa);
                tofix_totalSum = total_sum.toFixed(2);
                // console.log('小计:'+total_sum);
                console.log('小计:'+tofix_totalSum);
                $('.total_t_money').text(tofix_totalSum)
            }

            $(this).attr('data-iflag' , 'y');
            isTrue();
            return false;
        }

        // 未选中
        if ($(this).attr('data-iflag') == 'y') {
            $(this).removeClass('iconfont iconxz');
            $(this).addClass('iconfont iconwxz');
            $(this).removeClass('flag_i_xz');
            
            if ($(this).attr('data-iflag') == 'y'){
                var aa = $(this).siblings().find('.total_mon').text();
                total_sum -= Number(aa);
                tofix_totalSum = total_sum.toFixed(2);
                // console.log('小计:'+total_sum);
                console.log('小计:'+tofix_totalSum);
                $('.total_t_money').text(tofix_totalSum)
            }
            $(this).attr('data-iflag' , 'n');
            isTrue();
            return false;
        }
    });

    // 按钮减少
    $('.iconjian').bind('click',function() {
        
        var num_default = parseInt($(this).next().text());
        // 单价unitPrise_mon
        var unitPrise_mon = Number($(this).parent().siblings().find('.unitPrise_mon').text()).toFixed(2);
        if (num_default <= 1) {
            alert('不能再少了^_^');
            return false;
        }
        --num_default;
        // 小计
        var total_mon = Number(unitPrise_mon) * num_default ;
        var unitPrise_mon_tofix = total_mon.toFixed(2);
        $(this).next().text(num_default);
        $(this).parent().siblings().find('.total_mon').text(unitPrise_mon_tofix);
        //total_sum
        var this_add_prise = Number($('.total_t_money').text()) - Number(unitPrise_mon);
        $('.total_t_money').text(this_add_prise);

        var the_number = $(this).parent().find('.store_nums').text();
        var the_id = $(this).parent().parent().parent().parent().parent().find('.iconflag').data('id');
        var the_price = $(this).parent().parent().find('.total_mon').text();
        $.post('/home/cart/operation', {price:the_price, number:the_number, id:the_id}, function(data){});

    });    

    // 按钮增加
    $('.iconjia').bind('click',function() {

        var num_default = parseInt($(this).prev().text());
        // 单价unitPrise_mon
        var unitPrise_mon = Number($(this).parent().siblings().find('.unitPrise_mon').text()).toFixed(2);
        ++num_default;
        $(this).prev().text(num_default);
        var clik_flag = $(this).parent().parent().parent().parent().siblings().attr('data-iflag');

        // 小计  
        //var total_mon = Number($(this).parent().siblings().find('.total_mon').text()).toFixed(2) * num_default;
        var total_mon = Number($(this).parent().siblings().find('.unitPrise_mon').text()).toFixed(2) * num_default;
        var total_mon_tofix = total_mon.toFixed(2);
        $(this).parent().siblings().find('.total_mon').text(total_mon_tofix);
        if (clik_flag == 'y'){ //y为选中,n为没选中.合计
            var this_add_prise = Number($('.total_t_money').text()) + Number(unitPrise_mon);
            $('.total_t_money').text(this_add_prise);
        }

        var the_number = $(this).parent().find('.store_nums').text();
        var the_id = $(this).parent().parent().parent().parent().parent().find('.iconflag').data('id');
        var the_price = $(this).parent().parent().find('.total_mon').text();
        $.post('/home/cart/operation', {price:the_price, number:the_number, id:the_id}, function(data){});

    });

    // 全选 或 全不选
    $('#allelection_btn').bind('click',function() {
        // n为全不选
        if ($(this).attr('data-iflag') == 'n'){

            $('.iconflag').removeClass('iconfont iconxz');
            $('.iconflag').addClass('iconfont iconwxz');
            console.log($('.store_l_content').find('li').find('.iconflag').attr('data-iflag') )
            sib_prise_arr = []; //清空数组
            sib_sum = 0; //清空初始和
            $('.iconflag').attr('data-iflag' , 'n');
            

        }   
        if ($(this).attr('data-iflag') == 'y'){
            // console.log($(this).attr('data-iflag'))
            $('.iconflag').removeClass('iconfont iconwxz');
            $('.iconflag').addClass('iconfont iconxz');

            console.log($('.store_l_content').find('li').find('.iconflag').attr('data-iflag') )

            let li_icolen = $('.store_l_content').find('li').length;
            for (let i = 0 ; i < li_icolen ; i++) {
                let str_l_con = Number($('.store_l_content').find('li').eq(i).find('.total_mon').text()); 
                sib_prise_arr.push(str_l_con);
                sib_sum += sib_prise_arr[i];
            }
            console.log(sib_prise_arr)
            console.log(sib_sum+'总和')
            $('.total_t_money').text(sib_sum);
            $('.iconflag').attr('data-iflag' , 'y');

        }

    });

    // 用于全选- 每一个都选时,全选
    function isTrue() {

        var store_li = $('.store_l_content').find('li').length; //li
        var flag_i_xz = $('.flag_i_xz').length 
        if (flag_i_xz == store_li){
            $('#allelection_btn').removeClass('iconfont iconwxz');
            $('#allelection_btn').addClass('iconfont iconxz');
        }
        if (flag_i_xz !== store_li){
            $('#allelection_btn').removeClass('iconfont iconxz');
            $('#allelection_btn').addClass('iconfont iconwxz');
        }

    }


    // 编辑
    $('.edit').bind('click',function() {

        if (editFlag) {
            $('.edit').text('编辑');
            $('.payment').text('去支付');
            $('.payment').removeAttr('data-deleFlag');
            $('.total_txt').attr('style','display:block');
            editFlag = false;
            return false;
        }
        if (!editFlag) {
            $('.edit').text('取消');
            $('.payment').text('删除');
            $('.payment').attr('data-deleFlag','deleteele');
            $('.total_txt').attr('style','display:none');
            editFlag = true;
            return false;
        }
    });


    // 删除 和 去支付
    $('.payment').bind('click', function() {
        var select_list = [];
        $('.flag_i_xz ').each(function(){
            select_list.push($(this).data('id'));
        });
        if ($(this).attr('data-deleFlag') == 'deleteele'){ // 删除
            $.post('/home/cart/delete', {ids:select_list}, function(data){
                if (data.code === 1) {
                    console.log($('.flag_i_xz ').parent());
                    $('.flag_i_xz ').parent().remove();
                }
                alert(data.text);
            }, 'json');
        } else { // 支付
            var cart_id = select_list.join(',');
            location.href='/home/order/add?id='+cart_id+'&type=cart';
        }
    })


})();