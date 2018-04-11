/* 
*作者：一些事情
*时间：2015-4-17
*需要结合jquery和Validform和artdialog一起使用
----------------------------------------------------------*/


//$(function () {
//    FastClick.attach(document.body);
//});
/*工具类方法
------------------------------------------------*/
//切换二维码码
//function toggleCode() {
//    $("#right-code").toggle();
//}

//function popupResize() {
//    var w = $(window).width();
//    console.log($('.popup-modal').outerHeight());
//    if (w >= 768) {
//        var popupH = $('.popup-modal').outerHeight();
//        $('.popup-modal').css({
//            'margin-top': -popupH / 2 + "px",
//        });
//    }
//}




//可以自动关闭的提示，基于artdialog插件
function jsprint(msgtitle, url, callback) {
    var d = dialog({ content: msgtitle }).show();
    setTimeout(function () {
        d.close().remove();
    }, 3000);
    if (url == "back") {
        history.back(-1);
    } else if (url != "") {
        location.href = url;
    }
    //执行回调函数
    if (arguments.length == 3) {
        callback();
    }
}
//弹出一个Dialog窗口
function jsdialog(msgtitle, msgcontent, url, callback) {
    var d = dialog({
        title: msgtitle,
        content: msgcontent,
        okValue: '确定',
        ok: function () { },
        onclose: function () {
            if (url == "back") {
                history.back(-1);
            } else if (url != "") {
                location.href = url;
            }
            //执行回调函数
            if (argnum == 5) {
                callback();
            }
        }
    }).showModal();
}
//弹出一个Dialog窗口
function jsdialogwx(msgtitle, msgcontent, url, returnUrl) {
    var d = dialog({
        title: msgtitle,
        content: msgcontent,
        cancelValue: '返回',
        cancel: function () { 
            history.back(-1);
        },
        okValue: '会员中心',
        ok: function () {location.href = url; },
        onclose: function () {
                //location.href = url;
                 history.back(-1);
        }
    }).showModal();
}

//弹出一个Dialog窗口
function jsdialog(msgtitle, msgcontent, url) {
    var d = dialog({
        title: msgtitle,
        content: msgcontent,
//        cancelValue: '返回',
//        cancel: function () { 
//            history.back(-1);
//        },
        okValue: '最新活动',
        ok: function () {location.href = url; },
        onclose: function () {
                //location.href = url;
                 history.back(-1);
        }
    }).showModal();
}

function showConfirm(msgtitle, msgcontent, url, returnUrl) {
        $.modal({
            title: "Hello",
            text: "我是自定义的modal",
            buttons: [
            { text: "支付宝", onClick: function () { $.alert("你选择了支付宝"); } },
//            { text: "微信支付", onClick: function () { $.alert("你选择了微信支付"); } },
            { text: "取消", className: "default" },
          ]
        });
    }


//打开一个最大化的Dialog
function ShowMaxDialog(tit, url) {
    dialog({
        title: tit,
        url: url
    }).showModal();
}
/*页面级通用方法
------------------------------------------------*/

/*表单AJAX提交封装(包含验证)
------------------------------------------------*/
function AjaxInitForm(formObj, btnObj, isDialog, urlObj, callback){
	var argNum = arguments.length; //参数个数
	$(formObj).Validform({
		tiptype:3,
		callback:function(form){
			//AJAX提交表单
            $(form).ajaxSubmit({
                beforeSubmit: formRequest,
                success: formResponse,
                error: formError,
                url: $(formObj).attr("url"),
                type: "post",
                dataType: "json",
                timeout: 60000
            });
            return false;
		}
	});
    
    //表单提交前
    function formRequest(formData, jqForm, options) {
        $(btnObj).prop("disabled", true);
        $(btnObj).val("提交中...");
    }

    //表单提交后
    function formResponse(data, textStatus) {
		if (data.status == 1) {
            $(btnObj).val("提交成功");
			//是否提示，默认不提示
			if(isDialog == 1){
				var d = dialog({content:data.msg}).show();
				setTimeout(function () {
					d.close().remove();
					if (argNum == 5) {
						callback();
					}else if(data.url){
						location.href = data.url;
					}else if($(urlObj).length > 0 && $(urlObj).val() != ""){
						location.href = $(urlObj).val();
					}else{
						location.reload();
					}
				}, 2000);
			}else{
				if (argNum == 5) {
					callback();
				}else if(data.url){
					location.href = data.url;
				}else if($(urlObj)){
					location.href = $(urlObj).val();
				}else{
					location.reload();
				}
			}
        } else {
			dialog({title:'提示', content:data.msg, okValue:'确定', ok:function (){}}).showModal();
            $(btnObj).prop("disabled", false);
            $(btnObj).val("再次提交");
        }
    }
    //表单提交出错
    function formError(XMLHttpRequest, textStatus, errorThrown) {
		dialog({title:'提示', content:'状态：'+textStatus+'；出错提示：'+errorThrown, okValue:'确定', ok:function (){}}).showModal();
        $(btnObj).prop("disabled", false);
        $(btnObj).val("再次提交");
    }
}


