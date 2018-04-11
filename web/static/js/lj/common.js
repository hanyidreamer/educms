define(['jquery'], function(require, exports, module) {
	$(document).ready(function()
	{			
	$("#ckjr_index").click(function(){
			window.location.href="/";
	});
	
	$("#ckjr_category").click(function(){
		window.location.href="/category/index";
    });
	
	$("#ckjr_index_img").click(function(){
		window.location.href="/";
	});
	
	$(".am-appbtn").click(function(){
		window.location.href="/download/index";
	});

//	$("#ckjr_back").click(function(){
//		window.history.back();
//	});
	
	$("#ckjr_back_img").click(function(){
		window.history.back();
	});
	
	$("#ckjr_login").click(function(){
		window.location.href="/login";
	});
	
	$("#ckjr_register").click(function(){
		window.location.href="/login?type=reg";
	});
	
	
	$("#ckjr_search").click(function(){
		window.location.href="/search/";
    });
	
	$("#ckjr_onlinelist").click(function(){
		window.location.href="/list/online";
	});
	
	$("#ckjr_viplist").click(function(){
		window.location.href="/list/vip";
	});
	
	$("#gotoindex").click(function(){
		window.location.href="/index";
	});
	
	$("#gotoapp").click(function(){
		window.location.href="/download/index";
	});
	
	$("#gotoserver").click(function(){
		window.location.href="/service/ApplyServer";
	});
	
	$("#gotofeedback").click(function(){
		window.location.href="/feedback";
	});
	
	});
	

 });