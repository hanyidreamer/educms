define(['jquery'], function(require, exports, module) {
//	 require.async(['amazeui'],function(){
//     $('.am-slider').flexslider(
//     {
//      animation: "slide",
//      slideshow: true,
//      animationDuration: 600,
//      start: function(slider){
////    	  $("#flex-control").removeClass("am-hide");
////		   开始前才显示控制条
//    	  $(".am-control-nav").attr("style","display:block");
//      }}
//      );
//     });

	 
	 $("#appclosed").click(function(){
		 $("#fixedbottom").hide(); 
	 });
	 $("#app").click(function(){
		 $("#fixedbottom").show(); 
	 });
	 $("#gotop").click(function() {
		 $("#gotop").hide(); 
		    $("body,html").animate({scrollTop:0},800);
	 });
	 $(window).scroll(function(){
		var sh= $(window).scrollTop();
		 var wh =$(window).height();
		 if(sh>0){
			 $("#gotop").show(); 
		 }else{
			 $("#gotop").hide(); 
		 }
	 });
	

	 $('#headmaster_ctr').on('touchstart',function(){
	 	$('.body-shadow').show();
	 	$('.headmaster').slideDown();
	 });
	$('#hdms_close').on('touchstart',function(){
		$('.body-shadow').hide();
		$('.headmaster').slideUp();
		return false;
	});
	$(document).ready(function(){
	    setInterval(function(){
	      $('.renew-tip').slideUp();
	    },8000);
  	});
 
});