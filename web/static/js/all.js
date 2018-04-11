var ua = navigator.userAgent.toLowerCase()
var isIE=ua.indexOf('msie') > -1
var isFF=ua.indexOf('firefox') > -1
function scooke(name, value){
var expdate = new Date()
var argv = SetCookie.arguments
var argc = SetCookie.arguments.length
var expires = (argc > 2) ? argv[2] : null
var path = (argc > 3) ? argv[3] : null
var domain = (argc > 4) ? argv[4] : null
var secure = (argc > 5) ? argv[5] : false
if(expires!=null) expdate.setTime(expdate.getTime() + ( expires * 1000 ))
document.cookie = name + "=" + escape (value) +((expires == null) ? "" : ("; expires="+ expdate.toGMTString()))
+((path == null) ? "" : ("; path=" + path)) +((domain == null) ? "" : ("; domain=" + domain))
+((secure == true) ? "; secure" : "")}
function top_domain(){
var domain,tmp=location.host.split('.'),i = tmp.length
var hz = tmp[(i-1)]
var hz2 = tmp[(i-2)]
if( hz2=='net' || hz2=='com' || hz2=='org'  ){return tmp[(i-3)]+"."+hz2+"."+hz}
return tmp[(i-2)]+"."+tmp[(i-1)]}
function gcookie(n){
var aCookie = document.cookie.split("; ")
for(var i=0; i < aCookie.length; i++){
var aCrumb = aCookie[i].split("=")
if(n == aCrumb[0])	return unescape(aCrumb[1])
}return null}
function dcookie(name){
var exp = new Date()
exp.setTime (exp.getTime() - 1)
var cval = gck(name)
document.cookie = name + "=" + cval + "; expires="+ exp.toGMTString()}
function gck(n){
var aCookie = document.cookie.split("; ")
for(var i=0; i < aCookie.length; i++){
var aCrumb = aCookie[i].split("=")
if(n == aCrumb[0])	return unescape(aCrumb[1])
}return null}
function ck(n,v){SetCookie(n,v,94608000,'/',top_domain())}
function frame(n){return (isFF)?document.getElementById(n).contentWindow:document.frames[n]}
function getPos(el){
var r = {'x':el.offsetLeft,'y':el.offsetTop}
if(el.offsetParent){
var tmp = getPos(el.offsetParent)
r.x += tmp.x;r.y += tmp.y
}return r}
function regE(n,e,f){if(window.attachEvent)	n.attachEvent(e,f);	else n.addEventListener(e.replace(/^on/,''),f,false)}
function o(obj){return document.getElementById(obj)}
//function o(obj){return document.getElementById(obj)}
function os(obj){return o(obj).style}
var t_mobile={'134':1,135:1,136:1,137:1,138:1,139:1,150:1,151:1,158:1,159:1,157:1,187:1,188:1}
var t_unicom={130:1,131:1,132:1,155:1,156:1,185:1,186:1}
var t_telecom={133:1,153:1,180:1,189:1}
function ismobile(mobile){
mobile=mobile.toString();
var m=mobile.substr(0,3);
var l=mobile.length;
if(mobile.substr(0,1)==0 && l>10)
	return 3
else if(typeof(t_mobile[m])!='undefined' && l==11)
	return 1
else if(typeof(t_unicom[m])!='undefined' && l==11)
	return 2
else if(typeof(t_telecom[m])!='undefined' && l==11)
	return 4
return 0;
}
function ismobs(mobile){
mobile=mobile.toString();
var m=mobile.substr(0,3);
var l=mobile.length;
if(typeof(t_mobile[m])!='undefined')
	return 1
else if(typeof(t_unicom[m])!='undefined')
	return 2
else if(typeof(t_telecom[m])!='undefined')
	return 4
return 0;
}
function go()
{
	p=o('page').value;
	var url = ''+location.href;
	if( url.indexOf('p=') !=-1 )
	url = url.replace(/p=[0-9]*/i,'p='+p);
	else
	url = url+'&p='+p;
	location.href=url;
}
function selBox(obj){
	var str = false;
	if( typeof(obj)=="object" )
	{
		if( obj.checked ) str = true;
	}
	else
		str = obj;
	var box = et('input');
	var leng = box.length;
	for(var i=0;i<leng;i++)
	{
		if( box[i].type=='checkbox' && box[i].className!='ibox' )		box[i].checked=str;
	}
}
function body(){return(document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body}
