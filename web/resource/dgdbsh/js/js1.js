// JavaScript Document
var speeda=40 
var demoa=document.getElementById("demoa"); 
var demoa2=document.getElementById("demoa2"); 
var demoa1=document.getElementById("demoa1"); 
demoa2.innerHTML=demoa1.innerHTML 
function Marquee(){ 
if(demoa2.offsetTop-demoa.scrollTop<=0) 
  demoa.scrollTop-=demoa1.offsetHeight 
else{ 
  demoa.scrollTop++ 
} 
} 
var MyMar=setInterval(Marquee,speeda) 
demoa.onmouseover=function() {clearInterval(MyMar)} 
demoa.onmouseout=function() {MyMar=setInterval(Marquee,speeda)} 