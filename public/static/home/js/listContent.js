(function() {


    /**
     * 监听点击增加减少按钮
     * btn_add增加
     * btn_less减少
     * 
     **/
    
    var num_default = 1; //购物车默认数量为1
    var typesArr = []; //存放产品样式,规格的数组
    var type_idArr = [];
    var type_conArr = [];
    
     //增加
     $('#btn_add').bind('click',function() {
        var num_default = parseInt($(this).next().text());
        ++num_default;
        $(this).next().text(num_default);
        console.log(num_default)
     });
    
    //减少
    $('#btn_less').bind('click',function() {
        var num_default = parseInt($(this).prev().text());
        if (num_default <= 1) {
            alert('不能再少了^_^');
            return false;
        }
        --num_default;
        $(this).prev().text(num_default);
        console.log(num_default)
    
    });
    
    // 添加购物车
    $(document).on('click', '.addbtn', function(event) {
        // var curId = $(this).parent().parent().parent().parent().attr('data-dityId');
        var curId = $('.desc_con').eq(0).attr('data-commodityID');
        var curgId = $('.desc_con').eq(0).attr('data-commoditygID');
        console.log(curId)
        console.log(curgId)

        $('.cover').attr('style','display:block');
        $('#showEject').attr('style','display:block');
        $('body').css('overflow','hidden');//浮层出现时窗口不能滚动设置_pc端
        var goods_price = $('.desc_prise').eq(0).text(); //商品价格
        var goods_title = $('.desc_con').eq(0).find('strong').html(); //商品名字

        console.log(goods_price);
        console.log(goods_title);

        $('.money_prise').html('￥ '+goods_price);
        $('.money_descript').html(goods_title);
        $('.money_id').html(curId);
        $('.money_gid').html(curgId);
    
        $.ajax({
             url : '/home/goods/getNorms', //请求产品规格样式url
             type : 'POST',
             data : {'id' : curId},
             dataType : 'json',
             success : function(data) {
                 typesArr = [], type_conArr = [], type_idArr = [];
                 for (var i in data) {
                     typesArr.push(data[i].title);
                     if (!type_idArr[i]) type_idArr[i] = [];
                     if (!type_conArr[i]) type_conArr[i] = [];
                     for (var j in data[i].son) {
                         type_conArr[i].push(data[i].son[j].title);
                         type_idArr[i].push(data[i].son[j].id);
                     }
                 }
                //  console.log(typesArr);
                //  console.log(type_conArr);
                //  console.log(type_idArr);
    
                 for (let i = 0 ; i < type_conArr.length ; i++) {
                     var div = document.createElement('div');
                     var clearDiv = document.createElement('div');
                     var span = document.createElement('span');
                     span.setAttribute('data-types', i);
                     var ul = document.createElement('ul');
                     span.classList.add('content_c_style');
                     $(span).text(typesArr[i]);
    
                     for (let j = 0 ; j < type_conArr[i].length ; j++) {
                         var li = document.createElement('li');
                         div.classList.add('content_choice');
                         li.classList.add('c_style_li');
                         li.setAttribute('data-types', type_idArr[i][j]);
                         // console.log(type_conArr[i][j]);
                         $(li).text(type_conArr[i][j])
                         $(clearDiv).attr('style','clear:both');
                         $(div).prepend(span);
                         $(ul).append(li);
                         $(div).append(ul);
                         $(div).append(clearDiv);
                         $('.box_content').prepend(div);
                     }
                 }
             },
             error : function() {}
         });
    
            event.stopPropagation();
            return false;
    });
    
    // 点击li添加样式
    $('.box_content').on('click','.c_style_li',function() {
        $(this).addClass('style_cur');
        $(this).siblings().removeClass('style_cur');
        if ($('.style_cur').length == typesArr.length) {
            var ids = [];
            $('.style_cur').each(function(){
                ids.push($(this).data('types'));
            });
            var id = $('.money_id').text();

            $.post('/home/goods/getNormsContent', {'id':id, 'ids':ids}, function(data){
                console.log(data);
                $('.money_prise').html('￥ '+data.member_price);
            });
        }
    });

    // 定义状态
    var buyflag = false;
    
    // 取消弹窗
    $('.iconquxiao').bind('click',function() {
        $('#showEject').attr('style','display:none');
        $('.cover').attr('style','display:none');
        // 重置数量
        $('.store_nums').eq(0).text(num_default);
        // 清空规格样式内容
        // $('.box_content').html('');
        $('.content_choice').empty();
        $('body').attr('style','overflow:auto');

        //改变状态
        buyflag = false;
        $('.nowbuy').attr('data-buyflag',buyflag);
    });

    // 点击任何地方取消弹框
    $('body').bind('click',function(e) {
        if (!$(e.target).closest("#showEject").length) {
            $('#showEject').attr('style','display:none');
            $('.cover').attr('style','display:none');
            // 重置数量
            $('.store_nums').eq(0).text(num_default);
            // 清空规格样式内容
            $('.content_choice').html('');
            //body设置overflow:auto
            $('body').attr('style','auto');
        }
    });

    // 点击立即购买,添加状态,当选好产品规格点击确认时,判断这个状态,是否跳转支付页
    $('.nowbuy').bind('click',function() {
        buyflag = true;
        $(this).attr('data-buyflag',buyflag);
        // return false;
    });

    // 确认
    $('#box_submit').bind('click',function(event) {
        var g_id = $('.money_id').html();
        var g_price = $('.money_prise').html();
        var gg_id = $('.money_gid').html();

        console.log(g_id);
        console.log(g_price)
        // 判断是否为true
        var buyflag = $('.nowbuy').attr('data-buyflag');
        console.log(buyflag);

        g_price = g_price.replace('￥ ','');
        var choiceArr = []; //存放客户选择的样式
        var choicejson = {};//存放客户选择的样式
        var style_cur = $('.style_cur');
        var content_c_style = $('.content_c_style');
        var style_cur_txt = $('.style_cur').text();
        var addbtn_s_nums = $('.store_nums').eq(0).text();
    
        style_cur.each(function(){
            choicejson = {};
            var json_t = $(this).parent().parent().find('.content_c_style').html();
            choicejson[json_t] = $(this).html();
            choiceArr.push(choicejson);
        });
        
        if(content_c_style.length !== 0) {
            if (style_cur_txt == '' || style_cur.length !== content_c_style.length) {
                alert('请选择完整');
                $('#showEject').attr('style','display:block');
                $('.cover').attr('style','display:block');
                $('body').attr('style','auto');
                return false;
            }
        }

        if (content_c_style.length == 0 || content_c_style.length) {
            
            $.ajax({
                url : '/home/cart/add',
                type : 'POST',
                dataType : 'json',
                data : {
                    'goods_id' : g_id,
                    'gid' : gg_id,
                    'price' : g_price,
                    'norms' : choiceArr, //规格样式
                    'number' : addbtn_s_nums //数量
                },
                success : function(data) {
                    if (buyflag == 'true'){
                        location.href="/home/order/add/id/"+data.cart_id+"/type/cart.html";
                    } else {
                        alert(data.text);
                    }
                },
                error : function(data) {
                    console.log(data)
                    alert('加入失败，请重试');
                }
            });
            //  {:url('order/add', ['id'=>$info['id']])}
    
            // 重置数量
    
            // 清空规格样式内容
            $('.box_content').html('');
            $('#showEject').attr('style','display:none');
            $('.cover').attr('style','display:none');
            //body设置overflow:auto
            $('body').attr('style','auto');
            // return false;
        }

    });
    

    })();