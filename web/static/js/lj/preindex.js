define(['jquery','touchslide.1.1'], function(require, exports, module) {

		TouchSlide({ 
			slideCell:"#slideBox",
			titCell:".hd ul", 
			mainCell:".bd ul", 
			effect:"leftLoop", 
			autoPage:true,
			autoPlay:true
//			satrtFun:function(i,c){
//			}
		});

		$('.bd li').removeClass('am-hide');

// 	 require.async(['amazeui'],function(){
//      $('.am-slider').flexslider(
//      {
//       animation: "slide",
//       slideshow: true,
//       animationDuration: 600,
//       start: function(slider){
// //    	  $("#flex-control").removeClass("am-hide");
// //		   开始前才显示控制条
//     	  $(".am-control-nav").attr("style","display:block");
//       }}
//       );
//      });
});