<?php /*a:3:{s:74:"/andx/wwwroot/qixian_wuyiyizhan_com/application/admin/view/index/main.html";i:1560848583;s:75:"/andx/wwwroot/qixian_wuyiyizhan_com/application/admin/view/common/head.html";i:1556015467;s:75:"/andx/wwwroot/qixian_wuyiyizhan_com/application/admin/view/common/foot.html";i:1556015467;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('sys_name'); ?>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/static/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="/static/admin/css/global.css" media="all">
    <link rel="stylesheet" href="/static/common/css/font.css" media="all">
</head>
<body class="skin-<?php if(!empty($_COOKIE['skin'])){echo $_COOKIE['skin'];}else{echo '0';setcookie('skin','0');}?>">
<style>
    body {
        background-color: #f2f2f2;
    }
</style>
<div class="admin-main layui-anim layui-anim-upbit">
    <div class="layui-card">
        <div class="layui-row">
            <div class="layui-col-xs12 layui-col-sm3 layui-col-md3" style="background: #f2f2f2">
                <div class="layui-row" style="width: 90%;height:90%;margin:0 auto;background-color: #fff;padding:10px;">
                    <div class="layui-col-md3">
                        <i class="layui-icon layui-icon-form" style="font-size:63px;"></i>
                    </div>
                    <div class="layui-col-md9" style="padding:10px;">
                        今日订单总数<br><span style="font-size:18px;"><?php echo htmlentities($statistics['today_order']); ?></span>
                    </div>
                </div>
            </div>
            <div class="layui-col-xs12 layui-col-sm3 layui-col-md3" style="background: #f2f2f2">
                <div class="layui-row" style="width: 90%;height:90%;margin:0 auto;background-color: #fff;padding:10px;">
                    <div class="layui-col-md3">
                        <i class="layui-icon layui-icon-rmb" style="font-size:63px;"></i>
                    </div>
                    <div class="layui-col-md9" style="padding:10px;">
                        今日销售总额<br><span style="font-size:18px;"><?php echo htmlentities($statistics['today_money']); ?></span>
                    </div>
                </div>
            </div>
            <div class="layui-col-xs12 layui-col-sm3 layui-col-md3" style="background: #f2f2f2">
                <div class="layui-row" style="width: 90%;height:90%;margin:0 auto;background-color: #fff;padding:10px;">
                    <div class="layui-col-md3">
                        <i class="layui-icon layui-icon-template-1" style="font-size:63px;"></i>
                    </div>
                    <div class="layui-col-md9" style="padding:10px;">
                        昨日销售总额<br><span style="font-size:22px;"><?php echo htmlentities($statistics['yesterday_money']); ?></span>
                    </div>
                </div>
            </div>
            <div class="layui-col-xs12 layui-col-sm3 layui-col-md3" style="background: #f2f2f2">
                <div class="layui-row" style="width: 90%;height:90%;margin:0 auto;background-color: #fff;padding:10px;">
                    <div class="layui-col-md3">
                        <i class="layui-icon layui-icon-chart-screen" style="font-size:63px;"></i>
                    </div>
                    <div class="layui-col-md9" style="padding:10px;">
                        近7天销售总额<br><span style="font-size:18px;"><?php echo htmlentities($statistics['week_money']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">待处理事务</div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs12 layui-col-sm6 layui-col-md4">
                    <div class="layui-row">
                        <div class="layui-col-md8">待付款订单</div>
                        <div class="layui-col-md4"><?php echo htmlentities($statistics['order1']); ?></div>
                    </div>
                </div>
                <div class="layui-col-xs12 layui-col-sm6 layui-col-md4">
                    <div class="layui-row">
                        <div class="layui-col-md8">待处理订单</div>
                        <div class="layui-col-md4"><?php echo htmlentities($statistics['order2']); ?></div>
                    </div>
                </div>
                <div class="layui-col-xs12 layui-col-sm12 layui-col-md4">
                    <div class="layui-row">
                        <div class="layui-col-md8">已完成订单</div>
                        <div class="layui-col-md4"><?php echo htmlentities($statistics['order3']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header">用户总览</div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs6 layui-col-sm4 layui-col-md2">
                    <div class="layui-row" style="padding:10px 0;">
                        <div class="layui-col-md12" style="color:red;font-size:33px;text-align: center"><?php echo htmlentities($statistics['today_user']); ?></div>
                        <div class="layui-col-md12" style="text-align: center;padding-top:5px;">今日新增</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm4 layui-col-md2">
                    <div class="layui-row" style="padding:10px 0;">
                        <div class="layui-col-md12" style="color:red;font-size:33px;text-align: center"><?php echo htmlentities($statistics['yesterday_user']); ?></div>
                        <div class="layui-col-md12" style="text-align: center;padding-top:5px;">昨日新增</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm4 layui-col-md2">
                    <div class="layui-row" style="padding:10px 0;">
                        <div class="layui-col-md12" style="color:red;font-size:33px;text-align: center"><?php echo htmlentities($statistics['week_user']); ?></div>
                        <div class="layui-col-md12" style="text-align: center;padding-top:5px;">近7日新增</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm4 layui-col-md2">
                    <div class="layui-row" style="padding:10px 0;">
                        <div class="layui-col-md12" style="color:red;font-size:33px;text-align: center"><?php echo htmlentities($statistics['month_user']); ?></div>
                        <div class="layui-col-md12" style="text-align: center;padding-top:5px;">本月新增</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm4 layui-col-md2">
                    <div class="layui-row" style="padding:10px 0;">
                        <div class="layui-col-md12" style="color:red;font-size:33px;text-align: center"><?php echo htmlentities($statistics['total_user']); ?></div>
                        <div class="layui-col-md12" style="text-align: center;padding-top:5px;">用户总数</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm4 layui-col-md2">
                    <div class="layui-row" style="padding:10px 0;">
                        <div class="layui-col-md12" style="color:red;font-size:33px;text-align: center"><?php echo htmlentities($statistics['member_user']); ?></div>
                        <div class="layui-col-md12" style="text-align: center;padding-top:5px;">会员用户</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="admin-main layui-anim layui-anim-upbit">
    <div class="table-responsive">
        <table class="layui-table" lay-even lay-skin="line">
            <colgroup>
                <col width="40%">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th class="text-center" colspan="2"><?php echo lang('systemInfo'); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>网站域名</td>
                <td><?php echo htmlentities($config['url']); ?></td>
            </tr>
            <tr>
                <td>网站目录</td>
                <td><?php echo htmlentities($config['document_root']); ?></td>
            </tr>
            <tr>
                <td>服务器操作系统</td>
                <td><?php echo htmlentities($config['server_os']); ?></td>
            </tr>
            <tr>
                <td>服务器端口</td>
                <td><?php echo htmlentities($config['server_port']); ?></td>
            </tr>
            <tr>
                <td>服务器IP</td>
                <td><?php echo htmlentities($config['server_ip']); ?></td>
            </tr>
            <tr>
                <td>WEB运行环境</td>
                <td><?php echo htmlentities($config['server_soft']); ?></td>
            </tr>
            <tr>
                <td>MySQL数据库版本</td>
                <td><?php echo htmlentities($config['mysql_version']); ?></td>
            </tr>
            <tr>
                <td>运行PHP版本</td>
                <td><?php echo htmlentities($config['php_version']); ?></td>
            </tr>

            <tr>
                <td>最大上传限制</td>
                <td><?php echo htmlentities($config['max_upload_size']); ?></td>
            </tr>
            <tr>
                <td>MTTPHP版本</td>
                <td><?php echo config('version'); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>-->
<script type="text/javascript" src="/static/plugins/layui/layui.js"></script>


<script>
    layui.use('table', function() {
        var table = layui.table;
    })
</script>
</body>
</html>