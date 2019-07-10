<?php /*a:2:{s:74:"/andx/wwwroot/qixian_wuyiyizhan_com/application/home/view/index_index.html";i:1562754662;s:76:"/andx/wwwroot/qixian_wuyiyizhan_com/application/home/view/common_footer.html";i:1560991702;}*/ ?>
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- 点击无高光 -->
    <meta name="msapplication-tap-highlight" content="no">
    <title>务团网</title>
    <link rel="stylesheet" href="/static/home/css/index.css" />
    <link rel="stylesheet" href="/static/home/css/iconfont.css" />
    <link rel="stylesheet" href="/static/home/css/iconfontb.css" />
    <link rel="stylesheet" href="/static/home/css/iconfont_gwc.css" />
    <link rel="stylesheet" href="/static/home/css/swiper.css" />
</head>
<body>

<div id="wrap">
    <div class="top_bg">

        <!-- head -->
        <header class="head">
            <div class="head_wrap">
                <div class="area"><a href="<?php echo url('region'); ?>">地区选择 <i class="iconfont icondiqu"></i></a></div>
                <div class="search"><i class="iconfont iconsousuo" style="position:absolute;top:50%;transform:translateY(-50%);left:15px;color:#fff"></i><input type="text" class="search_ipt" placeholder="请输入商品名称"></div>
                <div style="clear:both"></div>
            </div>
        </header>

        <!-- 轮播图 -->

        <!-- <div class="swiper banner"> -->

        <section class="pc-banner">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php if(is_array($banner) || $banner instanceof \think\Collection || $banner instanceof \think\Paginator): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <div class="swiper-slide swiper-slide-center none-effect" >
                        <a href="<?php echo htmlentities($vo['url']); ?>">
                            <img src="<?php echo htmlentities($vo['pic']); ?>">
                        </a>
                        <div class="layer-mask"></div>
                    </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <!-- <div class="button">
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div> -->
            </div>
        </section>
        <!-- </div> -->
        <!-- 轮播图结束 -->

        <!-- nav导航 -->
        <div class="nav">
            <ul class="nava">
                <li class="navali">
                    <a href="<?php echo url('goods/list1', ['id'=>1]); ?>">
                        <span><img src="/static/home/images/ban1.png" alt=""></span>
                        <span class="lifont">家政保洁</span>
                    </a>
                </li>
                <li class="navali">
                    <a href="<?php echo url('goods/list1', ['id'=>18]); ?>">
                        <span><img src="/static/home/images/ban2.png" alt=""></span>
                        <span class="lifont"> 家电清洗</span>
                    </a>
                </li>
                <li class="navali">
                    <a href="<?php echo url('goods/channel1', ['id'=>5]); ?>">
                        <span><img src="/static/home/images/ban3.png" alt=""></span>
                        <span class="lifont">甲醛治理</span>
                    </a>
                </li>
                <li class="navali">
                    <a href="<?php echo url('goods/list2', ['id'=>2]); ?>">
                        <span><img src="/static/home/images/ban4.png" alt=""></span>
                        <span class="lifont">旧房翻新</span>
                    </a>
                </li>
            </ul>

            <ul class="nava">
                <li class="navali">
                    <a href="<?php echo url('goods/channel2', ['id'=>20]); ?>">
                        <span><img src="/static/home/images/ban5.png" alt=""></span>
                        <span class="lifont">智能家居</span>
                    </a>
                </li>
                <li class="navali">
                    <a href="<?php echo url('goods/list1', ['id'=>19]); ?>">
                        <span><img src="/static/home/images/ban6.png" alt=""></span>
                        <span class="lifont">汽车修养</span>
                    </a>
                </li>
                <li class="navali">
                    <a href="<?php echo url('goods/list4', ['id'=>11]); ?>">
                        <span><img src="/static/home/images/ban7.png" alt=""></span>
                        <span class="lifont">辅材集市</span>
                    </a>
                </li>
                <li class="navali">
                    <a href="javascript:void(0);">
                        <span><img src="/static/home/images/ban8.png" alt=""></span>
                        <span class="lifont">便民服务</span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- nav导航结束 -->

        <!-- 轮播图下的产品图 -->

        <?php if(is_array($banner8) || $banner8 instanceof \think\Collection || $banner8 instanceof \think\Paginator): $i = 0; $__LIST__ = $banner8;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <div class="productimg">
            <a href="<?php echo htmlentities($vo['url']); ?>"><img src="<?php echo htmlentities($vo['pic']); ?>" alt=""></a>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        <!-- 轮播图下的产品图结束 -->

        <!-- 产品图下4个模块 -->
        <div class="firstModul">
            <?php if(is_array($banner9) || $banner9 instanceof \think\Collection || $banner9 instanceof \think\Paginator): $i = 0; $__LIST__ = $banner9;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <a href="<?php echo htmlentities($vo['url']); ?>">
                <div class="modul modula">
                    <span><?php echo htmlentities($vo['name']); ?></span>
                    <span><?php echo htmlentities($vo['content']); ?></span>
                    <div class="img"><img src="<?php echo htmlentities($vo['pic']); ?>" alt=""></div>
                </div>
            </a>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>


        <!-- 产品图下4个模块结束 -->




    </div>

    <!-- 内容wrap -->

    <div class="arcticleWrap">

        <!-- 广告通栏 -->
        <?php if(is_array($banner10) || $banner10 instanceof \think\Collection || $banner10 instanceof \think\Paginator): $i = 0; $__LIST__ = $banner10;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <div class="fancon">
            <div class="fana" style="background: url(<?php echo htmlentities($vo['pic']); ?>)">
                <a href="<?php echo htmlentities($vo['url']); ?>"><span><?php echo htmlentities($vo['name']); ?><br /><?php echo htmlentities($vo['content']); ?></span></a>
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        <!-- 广告通栏结束 -->


        <!-- 第一个模块3个分类 -->

        <div class="firstifical">
            <p>欢迎入驻 | 优惠有礼</p>
            <div class="ificalcon">
                <?php if(is_array($banner11) || $banner11 instanceof \think\Collection || $banner11 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner11) ? array_slice($banner11,0,1, true) : $banner11->slice(0,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="ificalleft" onclick="location.href='<?php echo htmlentities($vo['url']); ?>'" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>) <?php echo htmlentities($vo['bg_color']); ?>">
                    <span><?php echo htmlentities($vo['name']); ?></span>
                    <span><?php echo htmlentities($vo['content']); ?></span>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; if(is_array($banner11) || $banner11 instanceof \think\Collection || $banner11 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner11) ? array_slice($banner11,2,2, true) : $banner11->slice(2,2, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="ificalright">
                    <div class="rightTop" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>) <?php echo htmlentities($vo['bg_color']); ?>">
                        <a href="<?php echo htmlentities($vo['url']); ?>">
                            <span><?php echo htmlentities($vo['name']); ?></span>
                            <span><?php echo htmlentities($vo['content']); ?></span>
                        </a>
                    </div>
                    <div class="rightBtm" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>) <?php echo htmlentities($vo['bg_color']); ?>">
                        <a href="<?php echo htmlentities($vo['url']); ?>">
                            <span><?php echo htmlentities($vo['name']); ?></span>
                            <span><?php echo htmlentities($vo['content']); ?></span>
                        </a>
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <!-- 第一个模块3个分类结束 -->

        <!-- 第二个广告通栏 -->
        <?php if(is_array($banner12) || $banner12 instanceof \think\Collection || $banner12 instanceof \think\Paginator): $i = 0; $__LIST__ = $banner12;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <div class="fancon">
            <div class="fanb" >
                <a href="<?php echo htmlentities($vo['url']); ?>"><span><?php echo htmlentities($vo['name']); ?><br /><?php echo htmlentities($vo['content']); ?></span></a>
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        <!-- 第二个广告通栏结束 -->

        <!-- 第二个模块 -->
        <div class="secifical">
            <p>欢迎入驻 | 优惠有礼</p>
            <div class="secificalcon">
                <?php if(is_array($banner13) || $banner13 instanceof \think\Collection || $banner13 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner13) ? array_slice($banner13,0,1, true) : $banner13->slice(0,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="seca" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>)">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?></span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; if(is_array($banner13) || $banner13 instanceof \think\Collection || $banner13 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner13) ? array_slice($banner13,1,1, true) : $banner13->slice(1,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="secb" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>)">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?></span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; if(is_array($banner13) || $banner13 instanceof \think\Collection || $banner13 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner13) ? array_slice($banner13,2,1, true) : $banner13->slice(2,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="secc" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>)">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?></span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <!-- 第二个模块结束 -->


        <!-- 第三个广告通栏 -->
        <?php if(is_array($banner14) || $banner14 instanceof \think\Collection || $banner14 instanceof \think\Paginator): $i = 0; $__LIST__ = $banner14;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <div class="fancon">
            <div class="fanc" style="background-image: url(<?php echo htmlentities($vo['pic']); ?>)">
                <a href="<?php echo htmlentities($vo['url']); ?>"><span><?php echo htmlentities($vo['name']); ?><br /><?php echo htmlentities($vo['content']); ?></span></a>
            </div>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        <!-- 第三个广告通栏结束 -->

        <!-- 第三个模块4个分类 -->

        <div class="typescon">
            <p>全新体验 | 更多服务</p>
            <div class="types">
                <?php if(is_array($banner15) || $banner15 instanceof \think\Collection || $banner15 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner15) ? array_slice($banner15,0,1, true) : $banner15->slice(0,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="type_l" style="background-image: <?php echo htmlentities($vo['bg_color']); ?>">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?> &nbsp;&nbsp;&nbsp;&nbsp;<i class="iconfont iconjishicaisex"></i></span>
                        <span><?php echo htmlentities($vo['content']); ?></span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; if(is_array($banner15) || $banner15 instanceof \think\Collection || $banner15 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner15) ? array_slice($banner15,1,1, true) : $banner15->slice(1,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="type_r" style="background-image: <?php echo htmlentities($vo['bg_color']); ?>">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?> &nbsp;&nbsp;&nbsp;&nbsp;<i class="iconfont iconmianfei"></i></span>
                        <span><?php echo htmlentities($vo['content']); ?></span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="typesb">
                <?php if(is_array($banner15) || $banner15 instanceof \think\Collection || $banner15 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner15) ? array_slice($banner15,2,1, true) : $banner15->slice(2,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="type_l" style="background-image: <?php echo htmlentities($vo['bg_color']); ?>">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?> &nbsp;&nbsp;&nbsp;&nbsp;<i class="iconfont iconzhineng"></i></span>
                        <span><?php echo htmlentities($vo['content']); ?></span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; if(is_array($banner15) || $banner15 instanceof \think\Collection || $banner15 instanceof \think\Paginator): $i = 0;$__LIST__ = is_array($banner15) ? array_slice($banner15,3,1, true) : $banner15->slice(3,1, true); if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <div class="type_r" style="background-image: <?php echo htmlentities($vo['bg_color']); ?>">
                    <a href="<?php echo htmlentities($vo['url']); ?>">
                        <span><?php echo htmlentities($vo['name']); ?> &nbsp;&nbsp;&nbsp;&nbsp;<i class="iconfont iconbianmin2"></i></span>
                        <span><?php echo htmlentities($vo['content']); ?> </span>
                    </a>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>

        <!-- 4个分类结束 -->


        <!-- 图片+文字 -->

        <!-- <div class="imgtxt">
            <div class="imgcon">
                <img src="/static/home/images/a3.jpg" alt="" />
                <span>卫生间套餐 - 简约风情</span>
            </div>
        </div> -->

        <!-- 图片+文字结束 -->


        <!-- 商品 -->

        <div class="commodity">
            <div class="comwrap">
                <?php if(is_array($recommended_list) || $recommended_list instanceof \think\Collection || $recommended_list instanceof \think\Paginator): $k = 0; $__LIST__ = $recommended_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?>
                <div class='<?php if($k != 0 && $mod == 0): ?>com_l<?php else: ?>com_r<?php endif; ?>'>
                    <a href="<?php echo url('goods/content2', ['id'=>$vo['id']]); ?>">
                        <div class='<?php if($k != 0 && $mod == 0): ?>com_l_img<?php else: ?>com_r_img<?php endif; ?>'><img src="<?php echo htmlentities($vo['pic']); ?>" /></div>
                        <div class='<?php if($k != 0 && $mod == 0): ?>com_l_description<?php else: ?>com_r_description<?php endif; ?>'><?php echo str_cut($vo['title'], 12); ?></div>
                        <div class='<?php if($k != 0 && $mod == 0): ?>com_l_buy<?php else: ?>com_r_buy<?php endif; ?>'>
                            <span>￥<?php echo htmlentities($vo['money']); ?>元</span>
                            <button>立即预约</button>
                        </div>
                        <div style="clear:both"></div>
                    </a>
                </div>
                <?php if($k == 2): ?></div><div class="comwrap"><?php endif; ?>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <!-- 商品结束 -->

    </div>

    <!-- 内容wrap结束 -->

    <!-- footer -->
    <footer class="footer">
    <ul>
        <li>
            <a href="<?php echo url('home/index/index'); ?>">
                <i class="iconfont iconindex"></i>
                <span>首页</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('goods/channel2', ['id'=>20]); ?>">
                <i class="iconfont iconshangcheng"></i>
                <span>商城</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('home/article/index', ['id'=>1]); ?>">
                <i class="iconfont iconfaxian"></i>
                <span>发现</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('home/cart/index'); ?>">
                <i class="iconfont icongwc1"></i>
                <span>购物车</span>
            </a>
        </li>
        <li>
            <a href="<?php echo url('user/index/index'); ?>">
                <i class="iconfont iconwode1"></i>
                <span>我的</span>
            </a>
        </li>
    </ul>

</footer>
    <!-- footer结束 -->



</div>

<script src="/static/home/js/jquery.min.js"></script>
<script src="/static/home/js/swiper.min.js"></script>
<script src="/static/home/js/index.js"></script>
<script type="text/javascript">

    window.onload = function() {
        var swiper = new Swiper('.swiper-container',{
            autoplay: true,
            delay: 100, //轮播切换时间
            speed: 500,
            autoplayDisableOnInteraction: true, //触碰图片后时候轮播
            loop: true,
            centeredSlides: true,
            slidesPerView: 2,
            spaceBetween : 220,//控制间距
            pagination: '.swiper-pagination',
            paginationClickable: true,

            // prevButton: '.swiper-button-prev',
            // nextButton: '.swiper-button-next',

            // onInit: function(swiper) {
            //     swiper.slides[2].className = "swiper-slide swiper-slide-active";
            // },


            // breakpoints: {
            //     668: {
            //         slidesPerView: 2,
            //     }
            // }
        });
    }


</script>
</body>
</html>