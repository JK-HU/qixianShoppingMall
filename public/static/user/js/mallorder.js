$('.tab a').bind('click',function() {


    $(this).find('i').addClass('active');
    $(this).siblings().find('i').removeClass('active');

});