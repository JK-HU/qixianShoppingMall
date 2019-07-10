$('.swiper-wrapper').on('click','.comment_order',function(event) {

    console.log($(this));

    event.stopPropagation();

});