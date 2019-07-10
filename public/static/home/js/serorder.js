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
    var curId = $(this).parent().parent().parent().parent().attr('data-dityId');
    var curgId = $(this).parent().parent().parent().parent().attr('data-ditygId');
    $('.cover').attr('style','display:block');
    $('#showEject').attr('style','display:block');
    $('body').css('overflow','hidden');//浮层出现时窗口不能滚动设置_pc端
    var goods_price = $(this).parent().parent().find('.moery').html();
    var goods_title = $(this).parent().parent().find('strong').html();

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
             console.log(typesArr);
             console.log(type_conArr);
             console.log(type_conArr[0])
             console.log(type_conArr[1])

            //  console.log(type_idArr);

             for (let i = 0 ; i < type_conArr.length ; i++) {
                 var div = document.createElement('div');
                 var clearDiv = document.createElement('div');
                 var span = document.createElement('span');
                 span.setAttribute('data-types',i);
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
        var id = $(this).parent().parent().parent().parent().find('.money_id').html();
        $.post('/home/goods/getNormsContent', {'id':id, 'ids':ids}, function(data){
            $('.money_prise').html('￥ '+data.now_price);
        });
    }
});

// 取消弹窗
$('.iconquxiao').bind('click',function() {
    $('#showEject').attr('style','display:none');
    $('.cover').attr('style','display:none');
    // 重置数量
    $('.store_nums').eq(0).text(num_default);
    // 清空规格样式内容
    $('.content_choice').html('');
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
    }
});

// 确认
$('#box_submit').bind('click',function(event) {
    // var g_id = $(this).parent().find('.money_id').html();
    // var gg_id = $(this).parent().find('.money_gid').html();
    // var g_price = $(this).parent().find('.money_prise').html();
    var g_id = $('.money_id').html();
    var gg_id = $('.money_gid').html();
    var g_price = $('.money_prise').html();
    g_price = g_price.replace('￥ ','');
    var choiceArr = []; //存放客户选择的样式
    var choicejson = {};//存放客户选择的样式
    var style_cur = $('.style_cur');
    var content_c_style = $('.content_c_style');
    var style_cur_txt = $('.style_cur').text();
    var addbtn_s_nums = $('.store_nums').eq(0).text();
    console.log('--------start')
    console.log(style_cur.length)
    console.log(style_cur_txt);
    console.log(content_c_style.length);
    console.log('---------end')

    style_cur.each(function(){
        choicejson = {};
        var json_t = $(this).parent().parent().find('.content_c_style').html();
        choicejson[json_t] = $(this).html();
        choiceArr.push(choicejson);
    });

    /**
     *如果有规格则就必须要选,如果没有就直接提交 
     **/
    console.log(type_conArr);
    console.log(style_cur_txt);

    if (content_c_style.length !== 0) {
        if (style_cur_txt == '' || style_cur.length !== content_c_style.length) {
            alert('请选择完整');
            $('#showEject').attr('style','display:block');
            $('.cover').attr('style','display:block');
            return false;
        }
    }

    if (content_c_style.length == 0 || content_c_style.length) {
        console.log('------------start1')
        console.log(g_id)
        console.log(gg_id)
        console.log(g_price)
        console.log(choiceArr)
        console.log(addbtn_s_nums)
        console.log('----------end')

        $.ajax({
            url : '/home/cart/add',
            type : 'POST',
            data : {
                'goods_id' : g_id,
                'gid' : gg_id,
                'price' : g_price,
                'norms' : choiceArr, //规格样式
                'number' : addbtn_s_nums //数量
            },
            success : function(data) {
                // console.log(data)
                alert(data.text);
            },
            error : function(data) {alert('加入失败')}
        });

        // 重置数量

        // 清空规格样式内容
        $('.content_choice').html('');
        $('#showEject').attr('style','display:none');
        $('.cover').attr('style','display:none');
        return false;
    }
    

    // 提交数据
    event.stopPropagation();
    return false;
});

// 数组去重
// function uniq(array){
//     var temp = []; //一个新的临时数组
//     for(var i = 0; i < array.length; i++){
//         if(temp.indexOf(array[i]) == -1){
//             temp.push(array[i]);
//         }
//     }
//     return temp;
// }

})();