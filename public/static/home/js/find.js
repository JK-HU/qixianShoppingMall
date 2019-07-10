(function() {
    
    $(document).ready(function() {
        // 点赞

        $('.give').bind('click',function() {
            var istrue = $(this).hasClass('fid');
            var t = $(this);
            var nums = $(this).text();
            var aid = $(this).data('id');
            $.post('/home/article/clickGood', {id:aid}, function(data) {
                // 获取点赞数
                if (!istrue){
                    t.addClass('fid');
                    t.find('.givenums').eq(0).text(Number(nums) + 1);
                }
                if (istrue){
                    t.removeClass('fid');
                    t.find('.givenums').eq(0).text(Number(nums) - 1);
                }
            });
        });
    });

})();