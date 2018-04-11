var layer = layui.layer,
    element = layui.element(),
    laydate = layui.laydate,
    form = layui.form();

/**
 * AJAX全局设置
 */
$.ajaxSetup({
    type: "post",
    dataType: "json"
});

/**
 * 通用日期时间选择
 */
$('.datetime').on('click', function () {
    laydate({
        elem: this,
        istime: true,
        format: 'YYYY-MM-DD'
    })
});

/**
 * 通用表单提交(AJAX方式)
 */
form.on('submit(*)', function (data) {
    $.ajax({
        url: data.form.action,
        type: data.form.method,
        data: $(data.form).serialize(),
        success: function (info) {
			layer.msg(info.msg);
            if (info.code === 2) { //导入会员
                get_data('user',1);
			} else if (info.code === 1) {
                setTimeout(function () {
                    location.href = info.url;
                }, 1000);
            }
        }
    });
    return false;
});

/**
 * 服务条款
 */
$('.tk input').on('click', function () {
	if($(this).is(':checked')){
		$('#reg').removeClass('layui-btn-disabled').prop('disabled', false);
	}else{
		$('#reg').addClass('layui-btn-disabled').prop('disabled', 'disabled');
	}
});

/**
 * body高度自适应
 */
if($('.user').length>0){
	get_h();
}
function get_h(){
	if($('.tab-right').length>0){
		var height = $('.tab-right').height()+70;
		if(height<450) height = 450;
	}else{
		var height = $(window).height()-70;
	}
	$('.user').height(height);
	$('.tab-left').height($('.tab-right').height());
}

/**
 * 获取手机验证码
 */
var codetime;
var time = time2 = 90;
$('.tel-btn').on('click', function () {
    var tel=$("#tel").val();
    var code=$("#codes").val();
    var _this = $(this);
    if(tel==''){
    	layer.msg('请填写手机号码~!');
    } else if(code==''){
    	layer.msg('请填写图形验证码~!');
    } else {
	    $.ajax({
	        url: $(this).attr('action'),
	        data: {'tel':tel,'code':code,'t':Math.random()},
	        success: function (info) {
	            if (info.code === 1) {
	            	layer.msg('验证码已发送到您的手机，请查收~!');
			    	_this.removeClass('layui-btn-normal').addClass('layui-btn-disabled');
			    	_this.prop('disabled', 'disabled');
			    	_this.text(time+'s重新获取');
			    	codetime = setInterval(function(){
			    		telcode_time('tel-btn');
			    	},1000);
					$("#yzmpic").attr('src','/index/sms/send?'+(new Date().getTime().toString(36)));
	            }else{
					layer.msg(info.msg);
	            }
	        }
	    });
    }
});
function telcode_time(_id){
	if(time>1){
		time--;
    	$('.'+_id).text(time+'s重新获取');
	}else{
    	time = time2;
    	$('.'+_id).removeClass('layui-btn-disabled').addClass('layui-btn-normal');
		$('.'+_id).prop('disabled', false);
    	$('.'+_id).text('获取验证码');
    	clearInterval(codetime);
	}
}

/**
 * 导入数据
 */
function get_data(ac,page){
	$.get('/user/login/cscms_'+ac+'/'+page, function(data){
		if(data.code==2){
			layer.msg('模型：'+ac+'，第'+page+'页导入完成，请稍后...~', {icon: 16,time:0});
			if(ac=='user'){
				get_data('pay',1);
			} else if(ac=='pay'){
				get_data('tixian',1);
			} else {
				layer.msg('恭喜你，全部导入完毕~');
				setTimeout(function () {
                    location.href = data.url;
                }, 1000);
			}
		} else if(data.code==1){
			layer.msg('模型：'+ac+'，第'+page+'页导入完成，请稍后...~', {icon: 16,time:0});
			get_data(ac,page+1);
		} else {
			layer.msg('导入出错：'+data.msg);
		}
	},"json");
}
/**
 * 通用翻页跳转
 */
$('.goto_page').on('click', function () {
    var page = $('#goto_page').val();
    var url = $(this).attr('link');
    if(url.indexOf("?") > 0 ){
        url+='&page='+page;
    }else{
        url+='?page='+page;
    }
    window.location.href=url;
});

/**
 * 通用IF弹出层
 */
$('.open_if').on('click', function () {
    var u = $(this).attr('u');
    var w = $(this).attr('w');
    var h = $(this).attr('h');
    layer.open({
        type: 2,
        shadeClose: true,
        area: [w, h],
        content: u
    });
});

/**
 * 通用单图上传
 */
layui.upload({
    url: "/user/upload",
    type: 'image',
    ext: 'jpg|png|gif|bmp|jpeg',
    success: function (data) {
        if (data.code === 1) {
            $('#'+data.pid).show();
            $('#'+data.pid).find('img').attr('src',data.url);
            get_h();
        } else {
            layer.msg(data.msg);
        }
    }
});

/**
 * 认证操作
 */
$('.radio').on('click', function () {
    var _res = $('input[name=sid]:checked').val();
    if(_res==1){
        $('#p1').html('您的姓名');
        $('#p2').hide();
    }else{
        $('#p1').html('公司名字');
        $('#p2').show();
    }
    get_h();
});
//判断提现提示
if($('.txmsg').length>0){
	layer.open({
		type: 1,
		skin: 'layui-layer-demo', //样式类名
		anim: 2,
		area: ['350px', '180px'], //宽高
		shadeClose: true, //开启遮罩关闭
		content: '<div style="padding:20px;">接到上级通知，提现姓名要必须跟认证姓名一至，<br>请修改，否则不能提现~!<br><br><br>意支付，2017.03.21</div>'
	});
}
//判断提现提示
if($('.fjmsg').length>0){
    layer.open({
        type: 1,
        skin: 'layui-layer-demo', //样式类名
        anim: 2,
        area: ['350px', '180px'], //宽高
        shadeClose: true, //开启遮罩关闭
        content: '<div style="padding:20px;">根据国家规定，清明节公司放假4天，<br>放假期间，不能提现~!<br><br><br>意支付，2017.03.31</div>'
    });
}