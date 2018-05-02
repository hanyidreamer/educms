function fnChangeNav(navEle) {
	$('#topnav ul li').removeClass('current');
	if(navEle) {
		navEle.addClass('current');
	}
}

function fnGetRootPath() {
	var pathName = window.location.pathname.substring(1);
	var webName = pathName == '' ? '' : pathName.substring(0, pathName
			.indexOf('/'));
	return window.location.protocol + '//' + window.location.host + '/' + webName
			+ '/';
}

function fnGetAppPath(url) {
	return url.split('/')[0];
}