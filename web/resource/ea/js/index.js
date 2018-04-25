$(document).ready(function(){
	$(".home_part_one_title").addClass("animated fadeInUp");
	$(".home_part_one_sub_title").addClass("animated fadeInUp");
	$(".home_part_one_box").addClass("animated fadeInUp");
	window.onscroll = function(){ //绑定scroll事件
  	var time1 = 0;
  	var time2 = 0;
  	var time3 = 0;
    var time4 = 0;
    var time5 = 0;
    var t = document.documentElement.scrollTop || document.body.scrollTop;  //获取滚动距离
    //alert(t);
    if( t >= 520 && time1 ==0) { 
        $(".home_part_two_left").addClass("animated lightSpeedIn");
        $(".home_part_two_right").addClass("animated lightSpeedIn");
        time1++;
    }
    if(t >= 1100 && time2 == 0){ 
       	$(".home_part_three_title").addClass("animated fadeInUp");
       	$(".home_part_three_sub_title").addClass("animated fadeInUp");
       	$(".product_box").addClass("animated fadeInUp");
      	time2++;
    } 
    if(t >= 1400 && time3==0){
    	$(".home_part_four_title").addClass("animated fadeInLeft");
    	time3++;
    }
    if(t>=1800 && time4==0){
      $(".home_part_five_title").addClass("animated fadeInRight");
      time4++;
    }
    if(t>=2000 && time5==0){
      $("#home_part_six_title").addClass("animated fadeInUp");
      time5++;
    }
}	
});