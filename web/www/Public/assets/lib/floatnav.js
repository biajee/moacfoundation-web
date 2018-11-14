$(function(){
	$(document.body).append('<div id="floatad" style="width:120px;height:140px;position:absolute;top:40px;right:10px;"><a href="http://www.tjcxwh.com/geodown.asp"><img src="images/flash/floatnav.gif" /></a></div>');
	$(window).scroll(function(){
		var pt = ($(window).scrollTop() + 40) + 'px';
		$('#floatad').stop();
		$('#floatad').animate({top:pt},'fast');
	});
})