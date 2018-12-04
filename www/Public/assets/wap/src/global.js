(function(){
JH.userinfo();
new Clipboard('.clipboard');
JH.title.unshift($("title").text());
window.onpopstate = function(event){
	var pageurl = window.location.href.split( "/" )[ window.location.href.split( "/" ).length-1 ];
	JH.back = true;
	if(JH.url!=pageurl&&$(".page").length==1){
		JH.load.page(pageurl);
	}else if($(".page").length>1){
		if($(".page").last().hasClass("pagenew")){
			JH.load.newclose({suc:function(){
				JH.closesuc();
			}});
		}else{
			JH.load.showclose({suc:function(){
				JH.closesuc();
			}});	
		}
	}
};
$("body").on("tap",".myFavorite",function(e){
	if(JH.data.userinfo!=undefined){
		JH.load.new("articleMy.html");
	}else{
		$.error("未登录",function(){
			window.location.href = "login.html";
		},true,{yes:"去登录",no:"取消"});
	}
});
$("body").on("keyup",".formcheck",function(){
	var that = $(this);
	$.doTimeout("doFormcheck");
	$.doTimeout("doFormcheck",1500,function(){
		var reg = eval(that.attr("data-reg"));
		if(!reg.test(that.val())){
			$.alert(that.attr("data-reg-txt"));
		}
	})
	
});
$("body").on("tap",".backnew",function(){
	JH.load.newclose();
});
$("body").on("tap",".backshow",function(){
	JH.load.showclose();
});
$("body").on("swipeRight",".pagenew",function(){
	JH.load.newclose();
})
$("body").on("swipeRight",".showClose",function(){
	JH.load.showclose();
})
//弹出窗口关闭
$("body").on("tap",".showClose",function(){
	JH.load.showclose();
});
$("body").on("tap",".showClose .scroll,.showClose .operation",function(e){
	e.stopPropagation();
});
$("body").on("click","a",function(e){
	if($(this).attr("href") == "" || $(this).attr("href") == "#"){
		e.preventDefault();
	}
});
$("body").on("tap",".tel",function(){
	window.location.href="tel:"+$(this).attr("data-tel");
});
$("body").on("tap",".opennew",function(){
	JH.load.new($(this).attr("data-url"));
});
$("body").on("tap",".openshow",function(){
	JH.load.show($(this).attr("data-url"));
});
$("body").on("tap",".open",function(){
	JH.load.page($(this).attr("data-url"));
});
$("body").on("tap",".openlink",function(){
	window.open($(this).attr("data-url"))
});
$("body").on("tap",".openvideo",function(){
	$(".playvideo").remove();
	$("body").append('\
		<div class="playvideo pf z15 animated fadeIn">\
			<span class="ico ico-video-close pf z11" style="right:0px;top:10px;" id="video-close"></span>\
			<video width="100%" height="100%" x-webkit-airplay="true" webkit-playsinline="" playsinline="true" src="'+$(this).find("video").attr("src")+'"></video>\
	</div>');
	$(".playvideo").find("video").get(0).play();
	if(JH.browserios()){
		$(".playvideo").find("video").get(0).addEventListener("pause",function(){
	        $(".playvideo").remove();
	    },false);
	}else{
		$(".playvideo").addClass("pt60");
	}
});
$("body").on("tap",".playvideo #video-close",function(){
	$(".playvideo").remove();
})
})();