function getQueryString(name) {
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null){
        return  unescape(r[2]);
    }else{
        return null;
    }
}
(function($){
	if($("meta[name='m_name']").size()>0)
	{
		var m_name=$("meta[name='m_name']").attr("content");

		$("#"+m_name).addClass("is_current");
		$("#"+m_name).parent().addClass("is_current_li");
		$("#"+m_name).show();	
	}
	
    $('.myMenu > ul > li').not($(".is_current_li")).hover(function() {
			$(".is_current").hide();
			$(this).find('ul').stop(true, false, true).slideToggle(300);
		
		});
	$('.myMenu').hover(function() {
		
			$(".is_current").stop(true, false, true).show();
	
		});



    $("#owl-demo").owlCarousel({
        navigation : true, // Show next and prev buttons
        autoPlay:true,
        stopOnHover:true,
        slideSpeed : 300,
        paginationSpeed : 400,
        singleItem:true,
        navigationText : ["prev", "next"]
    }).hover(function () {
            $(".owl-buttons").fadeToggle(400);
        });

    $('.toTop').click(function(){$("html,body").animate({scrollTop:0},300);return false});

    $('.weixinCode').hover(function (e) {
        $(".code2").fadeToggle();
    }, function () {
        $(".code2").fadeToggle();
    });

})(jQuery);