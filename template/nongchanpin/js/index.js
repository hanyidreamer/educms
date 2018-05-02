$(function() {	
	
	$(document).on('click', '#internetTab', function(e) {
		if($('#internet').is(':hidden')){
			$('#internet').show();
		}else{
			$('#internet').hide();
		}
	});
	
	$(document).on('click', '#higreenTabSwitch dd a', function(e) {
		console.log($(this).attr('id'));
		var md5 = hex_md5('higreenTab='+$(this).attr('id'));//返回编码后的字符  
		var base4 = $.base64.encode('higreenTab='+$(this).attr('id'));
		window.location.href='higreen/'+base4;
	});
	
	$(document).on('click', '#investorTabSwitch dd a', function(e) {
		console.log($(this).attr('id'));
		var md5 = hex_md5('investorTab='+$(this).attr('id'));//返回编码后的字符  
		var base4 = $.base64.encode('investorTab='+$(this).attr('id'));
		window.location.href='investor/'+base4;
	});
	
	$(document).on('click', '#messageTabSwitch a', function(e) {
		console.log($(this).attr('id'));
		var md5 = hex_md5('newsSubTab='+$(this).attr('id'));//返回编码后的字符  
		var base4 = $.base64.encode('newsTab=news&newsSubTab='+$(this).attr('id'));
		window.location.href='news/'+base4;
	});
	
	$(document).on('click', '#stockTabSwitch a', function(e) {
		console.log($(this).attr('id'));
		var md5 = $.base64.encode('investorTab='+$(this).attr('id'));//返回编码后的字符  
		window.location.href='investor/'+md5;
	});
	
	//加载公司新闻
	loadCompanyNewInfo();
	
	//媒体报道
	loadPrintMediaInfo();
	
	//热点关注
	loadHotFocusInfo();
	
	//加载指数数据
	loadIndexInfo();
	
	//加载公司年报
	loadCompanyAnnalsInfo();
	
	//加载信息披露
	loadMsgAnnounceInfo();
	
	//企业文化子菜单切换
	$(document).on('click', '#messageShow li', function(e) {
		e.preventDefault();
		$('#messageShow li').removeClass('active');
		$(this).addClass('active');
		var id = $(this).attr('id')+'Sub';
		
		$('.messageDetail').each(function(){
			if($(this).is(':visible')){
				$(this).hide();
			}
		});
		if($('#'+id).is(':hidden')){
			$('#'+id).show();
		}
	});
});

function loadIndexInfo(){
	$.ajax({
        type: "get",
        async: true,
        url: 'http://old.chinaap.com/qhindex/index/ajax/compositeJsonp',
        dataType: "jsonp",
        jsonp: "callback",
        jsonpCallback:"handle"
    })
    .done(function(data){// 调用成功处理逻辑
   	 if (data.result == 1) {
			fnInitIndexChart(data);
   	 }
    })
    .fail(function(){
    });
}

function fnInitIndexChart(data) {
	$('#comTable').highcharts(
		{
		chart : {
			type : 'spline'
		},
		xAxis : {
			gridLineWidth : 1,
			gridLineDashStyle : 'ShortDot',
			lineColor : '#000',
			type : 'datetime',
			labels : {
				formatter : function() {
					return Highcharts.dateFormat('%Y/%m', this.value);
				}
			}
		},
		yAxis : {
			title : {
				text : null
			},
			gridLineWidth : 1,
			gridLineDashStyle : 'ShortDot',
			lineColor : '#000'
		},
		title : {
			text : '前海•中国生鲜农产品批发价格指数',
			style : {
				cursor : 'pointer',
				font : '15px 宋体',
				fontWeight : 'bold'
			}
		},
		tooltip : {
			formatter : function() {
				return '<b>' + this.series.name + ':' + Number(this.y).toFixed(2)
						+ '</b><br/>' + Highcharts.dateFormat('%Y/%m/%d', this.x);
			}
		},
		plotOptions : {
			series : {
				showCheckbox : true
			},
			spline : {
				marker : {
					radius : 1
				},
				events : {
					checkboxClick : function(event) {
						if (event.checked == true) {
							this.show();
						} else {
							this.hide();
						}
					},
					legendItemClick : function(event) {// return false
						// 即可禁用LegendIteml，防止通过点击item显示隐藏系列
						return false;
					}
				}
			}
		},
		credits : {
			enabled : false
		},
		series : [ {
			name : '日线',
			color : '#004BA1',
			data : data.priceIndexChartList,
			selected : true
		// 默认checkbox勾选
		} ]
	});
}

//加载公司年报
function loadCompanyAnnalsInfo(){
	$.ajax({
        type: "GET",
        url: "articles",
        data : {
        	type : 'companyAnnals',
        	start : 0,
        	size : 8
        },
       dataType: "json",
       async : false,
        success:function(data){
        	var html = '<ul>';
        	$.each(data.tableData.data,function(i, item){
        		html += '<li><span class="title"><a target= "_blank" href="/resource-szap-bsp/file/pdf/'+item.articleResourcesUrl+'">'+item.articleTitle+' <i class="mini pdf icon"></i></a></span><span class="date">'+item.releaseDate+'</span></li>'
        	});
    		html += '</ul>';
        	$('#companyAnnalsSub').html(html);
        }
	});
}

function loadMsgAnnounceInfo(){
	$.ajax({
        type: "GET",
        url: "articles",
        data : {
        	type : 'messageAnnounce',
        	start : 0,
        	size : 8
        },
        dataType: "json",
        async : false,
        success:function(data){
        	var html = '<ul>';
        	$.each(data.tableData.data,function(i, item){
        		html += '<li><span class="title"><a target= "_blank" href="/resource-szap-bsp/file/pdf/'+item.articleResourcesUrl+'">'+item.articleTitle+' <i class="mini pdf icon"></i></a></span><span class="date">'+item.releaseDate+'</span></li>'
        	});
    		html += '</ul>';
        	$('#msgAnnounceSub').html(html);
        }
	});
}

function loadHotFocusInfo(){
	$.ajax({
        type: "GET",
        url: "articles",
        data : {
        	type : 'hotFocus',
        	start : 0,
        	size : 6
        },
        dataType: "json",
        async : false,
        success:function(data){
        	var html = '';
        	$.each(data.tableData.data,function(i, item){
    			var md5 = $.base64.encode('newsTab=hotFocus&id='+item.articleId);
        		if(i == 0){
        			html += '<div class="description1"><a href="news/'+md5+'">'+item.shortSummary+'...</a></div>'
        		}else if(i == 1){
        			html += '<ul><li><span class="title"><a href="news/'+md5+'">'+item.articleTitle+'...</a></span></a><span class="date">'+item.releaseDate+'</span></li>';
        		}else{
        			html += '<li><span class="title"><a href="news/'+md5+'">'+item.articleTitle+'...</a></span></a><span class="date">'+item.releaseDate+'</span></li>';
        		}
        	});
    		html += '</ul>';
        	$('#hotFocus').append(html);
        }
	});
}

function loadPrintMediaInfo(){
	$.ajax({
        type: "GET",
        url: "articles",
        data : {
        	type : 'printMedia',
        	start : 0,
        	size : 6
        },
        dataType: "json",
        async : false,
        success:function(data){
        	var html = '';
        	$.each(data.tableData.data,function(i, item){
    			var md5 = $.base64.encode('newsTab=printMedia&id='+item.articleId);
        		if(i == 0){
        			var str = item.summary;
        			while (str.indexOf("&nbsp;") >= 0){
                        str = str.replace("&nbsp;", "");
                    }
        			html += '<div class="description1"><a href="news/'+md5+'">'+item.shortSummary+'...</a></div>'
        		}else if(i == 1){
        			html += '<ul><li><span class="title"><a href="news/'+md5+'">'+item.articleTitle+'...</a></span></a><span class="date">'+item.releaseDate+'</span></li>';
        		}else{
        			html += '<li><span class="title"><a href="news/'+md5+'">'+item.articleTitle+'...</a></span></a><span class="date">'+item.releaseDate+'</span></li>';
        		}
        	});
    		html += '</ul>';
        	$('#printMedia').append(html);
        }
	});
}

function loadCompanyNewInfo() {
	$.ajax({
        type: "GET",
        url: "articles",
        data : {
        	type : 'companyNew',
        	start : 0,
        	size : 6
        },
        dataType: "json",
        async : false,
        cache: false,
        success:function(data){
        	var html = '';
        	$.each(data.tableData.data,function(i, item){
    			var md5 = $.base64.encode('newsTab=comNews&id='+item.articleId);
        		if(i == 0){
        			var str = item.summary;
        			while (str.indexOf("&nbsp;") >= 0){
                        str = str.replace("&nbsp;", "");
                    }
        			html += '<div class="description1"><a href="news/'+md5+'">'+item.shortSummary+'...</a></div>'
        		}else if(i == 1){
        			html += '<ul><li><span class="title"><a href="news/'+md5+'">'+item.articleTitle+'...</a></span></a><span class="date">'+item.releaseDate+'</span></li>';
        		}else{
        			html += '<li><span class="title"><a href="news/'+md5+'">'+item.articleTitle+'...</a></span></a><span class="date">'+item.releaseDate+'</span></li>';
        		}
        	});
    		html += '</ul>';
        	$('#companyNew').append(html);
        }
	});
}