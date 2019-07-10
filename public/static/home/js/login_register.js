//登录页面
/**
 * 获取表单信息
 * get_ipt_name 手机号
 * get_ipt_ps 密码
 * login_btn 登录按钮
 */


// 注册页面
/**
 * 获取input表单信息
 * 
 * get_name 手机号
 * 
 * get_verification 验证码
 * 
 * get_verifbtn 验证码按钮
 * 
 * get_ps 登录密码(6-20位)
 * 
 * get_confirmps 确认登录密码
 * 
 * sub_btn 确认注册按钮(提交所有信息)
 * 
 */

// 控制登录和注册页面的显示隐藏
var isLogin = true;
(function() {
    // 点击注册
    $('.register_txt').bind('click',function() {

        $('#register').eq(0).attr('style','display:block');
        $('#forgetpsword').eq(0).attr('style','display:none');
        $('.login').eq(0).attr('style','display:none');
        
        return false;
    });

    // 登录
    $('.showlogin').bind('click',function() {
        $('#register').eq(0).attr('style','display:none');
        $('#forgetpsword').eq(0).attr('style','display:none');
        $('.login').eq(0).attr('style','display:block');
        return false;
        
    });


    // 忘记密码
    $('.showforget').bind('click',function() {
        $('#register').eq(0).attr('style','display:none');
        $('#forgetpsword').eq(0).attr('style','display:block');
        $('.login').eq(0).attr('style','display:none');

        return false;
    });


})();


//  登录页面
(function(){
    // 手机号检测
    var phoneReg = /^[1][3,4,5,7,8][0-9]{9}$/;
    // 密码检测 - 数字字母下划线6-20个
    var psReg = /^[A-Za-z0-9_]{6,20}$/;

    $('.login_btn').bind('click',function() {

        var get_ipt_name = $('.get_ipt_name').eq(0).val();
        var get_ipt_ps = $('.get_ipt_ps').eq(0).val();    

        if (get_ipt_name == '' || get_ipt_name == 'undefined') {
            alert('请填写手机号');
            return false;
        };
        if (!phoneReg.test(get_ipt_name)) {
            alert('请填写正确手机号');
            return false;
        };
        if (get_ipt_ps == '' || get_ipt_ps == 'undefined') {
            alert('请输入密码');
            return false;
        };
        if (!psReg.test(get_ipt_ps)) {
            alert('密码不正确');
            return false;
        };

        $.post("/user/login/index", {username:get_ipt_name,password:get_ipt_ps}, function (re) {
            alert(re.msg);
            if (re.code == '1') {
                location.href='/user/index/index';
            }
        });

    });

})();

//  注册页面
(function () {

    // 手机号检测
    var phoneReg = /^[1][3,4,5,7,8][0-9]{9}$/;
    // 密码检测 - 数字字母下划线6-20个
    var psReg = /^[A-Za-z0-9_]{6,20}$/;
    // 验证码检测-6位
    var verifReg = /^[0-9]{6}$/;

    // 点击验证码按钮
    $('.get_verifbtn').bind('click', function (e) {
        var get_nameValue = $('.get_name').eq(0).val();
        if (get_nameValue == '' || get_nameValue == 'undefined') {
            alert('请填写手机号');
            return false;
        };
        if (!phoneReg.test(get_nameValue)) {
            alert('请填写正确手机号');
            return false;
        };

        CountDown = 60; //默认倒计时60s
        timer = setInterval(function () {
            CountDown--;
            console.log(CountDown);
            $('.get_verifbtn')[0].innerHTML = '倒计时(' + CountDown + 's)';
            if (CountDown == 0) {
                $('.get_verifbtn').eq(0).removeAttr('disabled');
                clearInterval(timer);
                CountDown = 60; //重新赋值
                $('.get_verifbtn')[0].innerHTML = '获取短信验证码';
            }
        }, 1000);

        // 请求验证码
        $.post("/api/sms/sendAlisms", {mobile:get_nameValue,SmsTemplateCode:"SMS_164810129"}, function (data, state) {
            alert('发送成功，请查收');
        });

        $('.get_verifbtn').eq(0).attr('disabled', 'disabled');
        e.preventDefault();
    });

    // 点击确认注册按钮
    $('.sub_btn').bind('click', function (e) {

        var get_nameValue = $('.get_name').eq(0).val();
        var get_verValue = $('.get_verification').eq(0).val();
        var get_psValue = $('.get_ps').eq(0).val();
        var get_confirmps = $('.get_confirmps').eq(0).val();
        if (get_nameValue == '' || get_nameValue == 'undefined') {
            alert('请填写手机号');
            return false;
        };
        if (!phoneReg.test(get_nameValue)) {
            alert('请填写正确手机号');
            return false;
        }
        if (get_verValue == '' || get_verValue == 'undefined') {
            alert('请填写验证码');
            return false;
        };
        if (!verifReg.test(get_verValue)){
            alert('验证码错误');
            return false;
        };
        if (get_psValue == '' || get_psValue == 'undefined') {
            alert('请填写密码');
            return false;
        };
        if (get_confirmps == '' || get_psValue !== get_confirmps || get_confirmps == 'undefined') {
            alert('请检查密码是否一致');
            return false;
        };
        if (!psReg.test(get_psValue)) {
            alert('密码格式不对');
            return false;
        }

        // 提交表单
        $.post('/user/login/reg',{mobile:get_nameValue,password:get_psValue,password2:get_confirmps,smscode:get_verValue},function(re){
            alert(re.msg);
            if (re.code == '1') {
                location.href="/user/index/index";
            }
        });

        e.preventDefault();
    });

})();

// 忘记密码
(function() {
    // 手机号检测
    var phoneReg = /^[1][3,4,5,7,8][0-9]{9}$/;
    // 密码检测 - 数字字母下划线6-20个
    var psReg = /^[A-Za-z0-9_]{6,20}$/;
    // 验证码检测-6位
    var verifReg = /^[0-9]{6}$/;

    $('.forgetpsword_verifbtn').bind('click',function(e) {
        var forgetpsword_newname = $('.forgetpsword_newname').eq(0).val();
        if (forgetpsword_newname == '' || forgetpsword_newname == 'undefined') {
            alert('请填写手机号');
            return false;
        };
        if (!phoneReg.test(forgetpsword_newname)) {
            alert('请填写正确手机号');
            return false;
        };

        CountDown = 60; //默认倒计时60s
        timer = setInterval(function () {
            CountDown--;
            $('.forgetpsword_verifbtn')[0].innerHTML = '倒计时(' + CountDown + 's)';
            if (CountDown == 0) {
                $('.forgetpsword_verifbtn').eq(0).removeAttr('disabled');
                clearInterval(timer);
                CountDown = 60; //重新赋值
                $('.forgetpsword_verifbtn')[0].innerHTML = '获取短信验证码';
            }
        }, 1000);
        // 请求验证码
        $.post("/api/sms/sendAlisms", {mobile:forgetpsword_newname,SmsTemplateCode:"SMS_164810129"}, function (data, state) {
            alert('发送成功，请查收');
        });
        $('.forgetpsword_verifbtn').eq(0).attr('disabled', 'disabled');
        e.preventDefault();

    });


    // 确认修改按钮
    $('.forgetpsword_btn').bind('click',function(e) {
        var forgetpsword_newname = $('.forgetpsword_newname').eq(0).val();
        var forgetpsword_verification_ipt = $('.forgetpsword_verification_ipt').eq(0).val();
        var forgetpsword_newps_ipt = $('.forgetpsword_newps_ipt').eq(0).val();
        var forgetpsword_confirmps_ipt = $('.forgetpsword_confirmps_ipt').eq(0).val();
        if (forgetpsword_newname == '' || forgetpsword_newname == 'undefined') {
            alert('请填写手机号');
            return false;
        };
        if (!phoneReg.test(forgetpsword_newname)) {
            alert('请填写正确手机号');
            return false;
        }
        if (forgetpsword_verification_ipt == '' || forgetpsword_verification_ipt == 'undefined') {
            alert('请填写验证码');
            return false;
        };
        if (!verifReg.test(forgetpsword_verification_ipt)){
            alert('验证码错误');
            return false;
        };
        if (forgetpsword_newps_ipt == '' || forgetpsword_newps_ipt == 'undefined') {
            alert('请填写密码');
            return false;
        };
        if (forgetpsword_confirmps_ipt == '' || forgetpsword_newps_ipt !== forgetpsword_confirmps_ipt || forgetpsword_confirmps_ipt == 'undefined') {
            alert('请检查密码是否一致');
            return false;
        };
        if (!psReg.test(forgetpsword_newps_ipt)) {
            alert('密码格式不对');
            return false;
        }
        // 提交表单
        $.post('/user/login/forget',{mobile:forgetpsword_newname,password:forgetpsword_newps_ipt,password2:forgetpsword_confirmps_ipt,smscode:forgetpsword_verification_ipt},function(re){
            alert(re.msg);
            if (re.code == '1') {
                location.href="/user/index/index";
            }
        });

        e.preventDefault();
    });
})();



