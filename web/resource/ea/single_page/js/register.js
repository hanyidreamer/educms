// JavaScript Document
$(function() {
    var validate = {
        validAccount: false,
        validCaptcha: false,
        validSmscode: false,
        validPassword: false,
        accountIsvalid: false, // 手机是否有效
        getCaptchaValidCode: false, // 图形验证码是否有效
        getMessageValidCode: false, //是否已经获取短信
        isSubmiting: false,
        captchaShow: false, //是否显示图形验证码
        account: function() {
            var ele = $("#AccountMobile");
            var val = ele.val();
            if (!val == "") {
                if (!(/^(((1[0-9]{2}))+\d{8})$/.test(val))) {
                    this.resMsg.errorMsg(ele, "请输入正确的手机号码");
                    this.validAccount = false;
                } else {
                    this.remoteMsg.registerStatus();
                }
            } else {
                this.resMsg.errorMsg(ele, "请输入正确的手机号码");
                this.validAccount = false;
            }
        },
        captchaValidCode: function() {
            var ele = $("#captchaValidCode");
            var val = ele.val();
            if (!val == "") {
                this.remoteMsg.captchaVerify();
            } else {
                this.resMsg.errorMsg(ele, "请输入正确的图形验证码");
                this.validCaptcha = false;
            }
        },
        MessageValidCode: function() {
            var ele = $("#MessageValidCode");
            var val = ele.val();
            if (!this.validAccount) {
                validate.account();
                return false;
            }
            if (!val == "") {
                if (!(/^\d{6}$/.test(val))) {
                    this.resMsg.errorMsg(ele, "请输入6位验证码");
                    this.validSmscode = false;
                } else {
                    this.remoteMsg.smscodeVerify();
                }
            } else {
                this.resMsg.errorMsg(ele, "请输入6位验证码");
                this.validSmscode = false;
            }
        },
        txtMPassword: function() {
            var ele = $("#txtMPassword");
            var val = ele.val();
            if (!this.validAccount) {
                validate.account();
                return false;
            }
            if (!val == "") {
                if (val.length < 6) {
                    this.resMsg.errorMsg(ele, "请输入6-16位密码");
                    this.validPassword = false;
                } else {
                    validate.resMsg.successMsg(ele);
                    this.validPassword = true;
                }
            } else {
                this.resMsg.errorMsg(ele, "请输入6-16位密码");
                this.validPassword = false;
            }
        },
        remoteMsg: {
            registerStatus: function() {
                var ele = $("#AccountMobile");
                var val = ele.val();
                $.ajax({
                    type: "GET",
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true,
                    url: "/api/v1/auth/register-status",
                    data: {
                        'account': val
                    },
                    success: function(res) {
                        if (res.data.isExists) {
                            validate.resMsg.errorMsg(ele, "该手机已被注册！");
                            validate.accountIsvalid = false;
                            validate.validAccount = false;
                            return;
                        } else {
                            validate.resMsg.successMsg(ele);
                            validate.accountIsvalid = true;
                            validate.validAccount = true;
                        }
                    },
                    error: function(res) {
                        console.log(res);
                        return;
                    }
                })
            },
            captchaVerify: function() {
                var ele = $("#captchaValidCode");
                var val = ele.val();
                $.ajax({
                    type: "POST",
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true,
                    url: "/api/v1/auth/captcha/verify",
                    data: {
                        'captcha': $("#captchaValidCode").val()
                    },
                    success: function(res) {
                        if (res.data.verified) {
                            validate.resMsg.successMsg(ele);
                            validate.validCaptcha = true;
                        } else {
                            validate.resMsg.errorMsg(ele, "图形验证码错误");
                            validate.validCaptcha = false;
                        }
                    },
                    error: function(res) {
                        console.log(res);
                    }
                })
            },
            smscodeVerify: function() {
                var ele = $("#MessageValidCode");
                var val = ele.val();
                $.ajax({
                    type: "POST",
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true,
                    url: "/api/v1/auth/smscode/verify",
                    data: {
                        'mobile': $("#AccountMobile").val(),
                        'smscode': $("#MessageValidCode").val()
                    },
                    success: function(res) {
                        if (res.data.verified) {
                            validate.resMsg.successMsg(ele);
                            validate.validSmscode = true;
                        } else {
                            validate.resMsg.errorMsg(ele, "手机验证码错误");
                            validate.validSmscode = false;
                        }
                    },
                    error: function(res) {
                        console.log(res);
                    }
                })
            }
        },
        resMsg: {
            successMsg: function(ele) {
                ele.removeClass('error');
                ele.parent().next().hide();
            },
            errorMsg: function(ele, msg) {
                ele.addClass('error');
                ele.parent().next().text(msg).show();
            }
        }
    };


    //输入满位数即刻验证
    $('input').bind('input', function() {
        var typeName = $(this).attr('name');
        var vlength = $(this).val().length;
        if (typeName == 'account') {
            if(vlength === 11){
                validate.account();
            }
        } else if (typeName == 'captcha') {
            if(vlength === 6){
                validate.captchaValidCode();
            }
        } else if (typeName == 'smscode') {
            if(vlength === 6){
                validate.MessageValidCode();
            }
        } else if (typeName == 'password') {
            validate.txtMPassword();
        }
    })

    //失去焦点时验证
    $('input').bind('change', function() {
        var typeName = $(this).attr('name');
        if (typeName == 'account') {
            validate.account();
            webBlurAccount($(this).val());
        } else if (typeName == 'captcha') {
            validate.captchaValidCode();
        } else if (typeName == 'smscode') {
            validate.MessageValidCode();
        } else if (typeName == 'password') {
            validate.txtMPassword();
        }
    })

    //发送手机验证码
    $(".js_msg_btn").bind('click', function(evt) {
        var _this = $(this);
        var clickId = $(evt.target).attr('id');
        var typeUrl = '/api/v1/auth/smscode';
        var track_tel_type = 'text';
        if(clickId === 'btnVoicecode') {
            typeUrl = '/api/v1/auth/voicecode';
            track_tel_type = 'voice';
        }
        var ele = $("#MessageValidCode");
        if (!validate.getMessageValidCode) {
            validate.account();
            if (!validate.accountIsvalid) {
                return false;
            } else {
                // 神策统计
                window.FMAnalytics && window.FMAnalytics.track($("#AccountMobile").val(), track_tel_type);
                $.ajax({
                    type: "GET",
                    url: typeUrl,
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true,
                    data: {
                        'mobile': $("#AccountMobile").val(),
                        'captcha': $('#captchaValidCode').val()
                    },
                    success: function(res) {
                        validate.resMsg.successMsg(ele);
                        if (res.code == 0) {
                            validate.getMessageValidCode = true;
                            setBtnInvalid(_this, 'msg_resend_btn');
                        }
                    },
                    error: function(err) {
                        try {
                            var r = JSON.parse(err.responseText);
                        } catch (e) {
                            return validate.resMsg.errorMsg(ele, "获取验证码失败，请联系客服");
                        }
                        //超过次数限制
                        if (r.code === 2000024 || r.code === 2000007) {
                            validate.captchaShow = true;
                            $('.captcha_box').addClass('captcha_box_show');
                            $('#captcha_img').attr('src', 'data:image/jpg;base64,' + r.captcha);
                            validate.captchaValidCode();
                        } else if (r.code === 2000011) {
                            validate.resMsg.errorMsg(ele, "操作太频繁,1分钟后再试");
                        } else if (r.code === 2000025) {
                            validate.resMsg.errorMsg(ele, "今日短信验证码获取次数过多，请明日再试");
                        } else if (r.code === 2000026) {
                            validate.resMsg.errorMsg(ele, "同一IP下已超过最大发送量");
                        } else {
                            validate.resMsg.errorMsg(ele, "获取验证码失败，请联系客服");
                            console.log(r);
                        }
                    }
                });
            }
        }
    });

    //更换图形验证码
    $('#btnValidCaptcha').bind('click', function(event) {
        $.ajax({
            type: "GET",
            url: "/api/v1/auth/captcha",
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            data: {
                'captcha': $("#captchaValidCode").val(),
            },
            success: function(res) {
                if (res.code == 0) {
                    validate.getCaptchaValidCode = true;
                    $('#captcha_img').attr('src', 'data:image/jpg;base64,' + res.data.captcha);
                }
            },
            error: function(res) {
                console.log(res)
            }
        });
    })

    $('#btnMobileSubmit').click(function() {
        submitMobileForm();
    })

    //勾选协议样式绑定
    $("#lblXieYi").click(function() {
        if ($(this).hasClass('checked')) {
            $(this).removeClass('checked');
        } else {
            $(this).addClass('checked');
            validate.resMsg.successMsg($(this));
        }
    });

    /*设置按钮60秒后可用*/
    function setBtnInvalid(obj, enableClass) {
        if (enableClass == undefined || enableClass == '') {
            enableClass = "";
        }
        var originalText = obj.html();
        var timer = 60;
        obj.addClass(enableClass).siblings().addClass(enableClass);
        obj.attr("disabled", "disabled").siblings().attr("disabled", "disabled");
        timer = timer - 1;
        obj.html(timer + "s重新发送");
        var timerId = setInterval(function() {
            timer = timer - 1;
            obj.html(timer + "s重新发送");
            if (timer <= 0) {
                //obj.css('background', '#656571')
                obj.removeClass(enableClass).siblings().removeClass(enableClass);
                obj.removeAttr("disabled").siblings().removeAttr("disabled");
                obj.html(originalText);
                validate.getMessageValidCode = false;
                clearInterval(timerId);
            }
        }, 1000);
    }

    function submitFn(hasVcode){
        var payload = {
            "account": $('#AccountMobile').val(),
            "password": $('#txtMPassword').val(),
            "smscode": $('#MessageValidCode').val(),
            "platform": "pcweb"
        };

        if(hasVcode && hasVcode>0) {
            payload['invite'] = hasVcode;
        }

        if (!validate.isSubmiting) {
            if (validate.captchaShow) {
                payload['captcha'] = $('#captchaValidCode').val();
                validate.captchaValidCode();
                !validate.validAccount && validate.account();
                !validate.validCaptcha && validate.captchaValidCode();
                !validate.validSmscode && validate.MessageValidCode();
                !validate.validPassword && validate.txtMPassword();
                if (!validate.validAccount || !validate.validCaptcha || !validate.validSmscode || !validate.validPassword) {
                    return false;
                }
            } else {
                !validate.validAccount && validate.account();
                !validate.validSmscode && validate.MessageValidCode();
                !validate.validPassword && validate.txtMPassword();
                if (!validate.validAccount || !validate.validSmscode || !validate.validPassword) {
                    return false;
                }
            }


            if (!$('#lblXieYi').hasClass('checked')) {
                validate.resMsg.errorMsg($('#lblXieYi'), "请勾选我已阅读并接受《Followme协议》");
                return false;
            }

            validate.isSubmiting = true;

            $.ajax({
                type: "POST",
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: true,
                url: "/api/v1/auth/register",
                data: payload,
                success: function(res) {
                    validate.isSubmiting = false;
                    var USERID = res.data.id;
                    if(USERID) {
                        var param = {
                            userId: USERID,
                            channel: 'fm',
                            type: 'mobile',
                            mobile: payload.account,
                            platform: 'pc.web',
                            registerTime: (new Date()).getTime()
                        };
                        window.FMAnalytics && window.FMAnalytics.register(param);
                    }
                    setTimeout(function(){
                        //注册完成页面
                        window.location.href = location.protocol + '//' + location.host;
                    }, 1000);
                },
                error: function(res) {
                    validate.isSubmiting = false;
                    console.log(res);
                }
            })
        }
    }

    //提交表单
    function submitMobileForm() {
        if(window.FMTrack){
            FMTrack.getVcode(function(vcode) {
                submitFn(+vcode);
            })
        }else {
            submitFn(-1);
        }
    }

    // 失去焦点获取手机验证码
    function webBlurAccount(tel) {
        if((/^(((1[0-9]{2}))+\d{8})$/.test(tel))) {
            window.FMAnalytics && window.FMAnalytics.track(tel);
        }
    }
})
