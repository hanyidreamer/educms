define(["jquery","http://res.wx.qq.com/open/js/jweixin-1.0.0.js"],function(a){
	"user strict";
	var wx=a('http://res.wx.qq.com/open/js/jweixin-1.0.0.js');
	var shareTit = $('#shareTit').val();
	var shareDesc = $('#shareDesc').val();
	var shareImg = $('#shareImg').val();
	var appId = $('#appId').val();
	if(shareDesc=='')
	{ shareDesc = '创客匠人致力于打造全国性的创业平台';};
	var shareUrl = $('#shareUrl').val();
	if(shareTit==''){
		shareTit = '创客匠人学堂';
	};
	if (shareImg=='') {
		shareImg = "http://www.ckjr001.com/assets/i/wap/books/bookshelf.jpg";
	};
	if(shareUrl==''){
		shareUrl = 'http://www.ckjr001.com/';
	}
	var shareTimestampt = $('#shareTimestampt').val();
	var shareNonceStr = $('#shareNonceStr').val();
	var shareSignature = $('#shareSignature').val();

	wx.config(
	{
	    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	    appId: appId, // 必填，公众号的唯一标识
	    timestamp: shareTimestampt , // 必填，生成签名的时间戳
	    nonceStr: shareNonceStr, // 必填，生成签名的随机串
	    signature: shareSignature,// 必填，签名，见附录1
	    jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	});

	wx.ready(function(){
		// 分享到朋友圈
		wx.onMenuShareTimeline({
		    title: shareTit, // 分享标题
		    link: shareUrl, // 分享链接
		    imgUrl: shareImg, // 分享图标
		    success: function () {
		        // 用户确认分享后执行的回调函数
		        var sUrl = window.location.href.trim();
		        if(sUrl.indexOf("fm/detail")!=-1)
                {
                    var istart =sUrl.lastIndexOf('/')+1;
                    var id = sUrl.substr(istart);
                    $.post('/wb/share',{id:id},function(data){
                        if(data.valid)
                        {
                            alert("现在您可以免费收听该音频了，将刷新页面");
                            var aa = sUrl.lastIndexOf('?');
                            if(aa==-1)
                            {
                                window.location.href = window.location.href+"?id="+10000*Math.random();
                            }
                            else
                            {
                                window.location.href = window.location.href+"&id="+10000*Math.random();
                            }
                        }
                        else
                        {
                            alert("分享出现问题，请重试");
                        }
                    },'json');
                }
		    },
		    cancel: function () {
		        // 用户取消分享后执行的回调函数
		    }
		});
		// 分享给朋友
		wx.onMenuShareAppMessage({
		    title: shareTit, // 分享标题
		    desc: shareDesc, // 分享描述
		    link: shareUrl, // 分享链接
		    imgUrl: shareImg, // 分享图标
		    type: '', // 分享类型,music、video或link，不填默认为link
		    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		    success: function () {
                // 用户确认分享后执行的回调函数
                var sUrl = window.location.href.trim();
                if(sUrl.indexOf("fm/detail")!=-1)
                {
                    var istart =sUrl.lastIndexOf('/')+1;
                    var id = sUrl.substr(istart);
                    $.post('/wb/share',{id:id},function(data){
                        if(data.valid)
                        {
                            alert("现在您可以免费收听该音频了，将刷新页面");
                            var aa = sUrl.lastIndexOf('?');
                            if(aa==-1)
                            {
                                window.location.href = window.location.href+"?id="+10000*Math.random();
                            }
                            else
                            {
                                window.location.href = window.location.href+"&id="+10000*Math.random();
                            }
                        }
                        else
                        {
                            alert("分享出现问题，请重试");
                        }
                    },'json');
                }
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
		// 分享到QQ
		wx.onMenuShareQQ({
		    title: shareTit, // 分享标题
		    desc: shareDesc, // 分享描述
		    link: shareUrl, // 分享链接
		    imgUrl: shareImg, // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		       // 用户取消分享后执行的回调函数
		    }
		});
		// 分享到腾讯微博
		wx.onMenuShareWeibo({
		    title: shareTit, // 分享标题
		    desc: shareDesc, // 分享描述
		    link: shareUrl, // 分享链接
		    imgUrl: shareImg, // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
		// 分享到QQ空间
		wx.onMenuShareQZone({
		    title: shareTit, // 分享标题
		    desc: shareDesc, // 分享描述
		    link: shareUrl, // 分享链接
		    imgUrl: shareImg, // 分享图标
		    success: function () { 
		       // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
	})
});

