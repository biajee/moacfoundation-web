$(document).ready(function(){
	var sUserAgent = navigator.userAgent.toLowerCase();
    var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
    var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
    var bIsMidp = sUserAgent.match(/midp/i) == "midp";
    var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
    var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
    var bIsAndroid = sUserAgent.match(/android/i) == "android";
    var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
    var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
    var windowWidth = $(window).width();
    var windowH = $(window).height();
    if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
    	//移动端
    	document.location.href = 'http://www.higo.com/wap';
    	//判断是否为微信内置浏览器
    	var ua = window.navigator.userAgent.toLowerCase();
	    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
	    	
    	}
    } else {
    	//pc端
//  	document.location.href = 'http://www.higo.com';
    }
});
(function($) { 
var jSelect = $(".jsSelect"); 
$(jSelect).find("li:first").hover(function(){ 
$(".s").css("background","url(images/68_60.png) 54px 0px no-repeat"); 
h=$(this).parent("ul").find("li").length; 
$(this).parent("ul").css("height",28*h) 
$(this).siblings("li:not(.s)").mouseenter(function(){ 
$(".s").css("background","url(images/68_60.png) 54px 0px no-repeat"); 
$(this).css("background","#428bca").css("color","#FFFFFF") 
}); 
$(this).siblings("li:not(.s)").mouseleave(function(){ 
$(this).css("background","none").css("color","#428bca") 
$(".s").css("background","url(images/68_60.png) 54px -30px no-repeat"); 
}); 

$(this).parent(jSelect).mouseleave(function(){ 
$(this).css("height",28) 
}); 
}); 
$(jSelect).find("li:not(.s)").click(function(){ 
var cContent=$(jSelect).find("li:first").html(); 
var cdContent = $(this).html(); 
$(jSelect).find("li:first").html(cdContent); 
$(this).html(cContent); 
$(this).find('a').removeClass('s'); 
$(this).find('a').removeAttr('style'); 
$(jSelect).find("li:first").addClass('s'); 
$(this).parent("ul").css("height",28); 
}); 
$(".s").css("background","url(images/68_60.png) 54px -30px no-repeat"); 

})(jQuery); 
