/*
* JS自定义插件！ - v1.0
* 
* 作者：佳好 QQ：25009291
*/
(function($) {
	$.fn.mytest = function(suc) {
		var test = true;
		$(this).find(".formcheck").each(function(i, d) {
			if(test){
				var reg = eval($(this).attr("data-reg"));
				if(!reg.test($(this).val())){
					$.alert($(this).attr("data-reg-txt"));
					test = false;
				}
			}
		});
		if(test){
			suc();
		}
	};
	$.fn.serializeObject = function() {
		var o = {};
		var a = this.serializeArray();
		var that = $(this);
		$.each(a, function(i) {
			if(o[this.name]) {
				if(!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				if(this.value != ""){
					o[this.name].push(this.value);
				}
			}else{
				if(this.value != ""){
					o[this.name] = this.value;
				}
			}
		});
		return o;
	};
	$.fn.limit=function(num){//截取文字
		$(this).each(function(i,d){
			var objString = $(this).text();
			var objLength = $(this).text().length;
			if(objLength > num){
				if(!$(this).attr("title")){
					$(this).attr("title",objString);
				}
				objString = $(this).text(objString.substring(0,JH.StringUtilities.limitLength(objString,num))+"…");
			}
		});
	};
	$.fn.animatepng = function(width,number,time,one){
		var that = $(this);
		var num = 1;
		if(time == undefined){
			time = 40;
		}
		$.doTimeout(that.attr("data-do"),time,function(){
			if(one == undefined){
				one = false;
			}
			that.css({"background-position-x":-width*num});
			num++;
			if(num == number&&!one){
				num = 0;
			}
			if(num<number){
				return true;
			}
		})
	};
	$.fn.animatestop = function(){
		var that = $(this);
		$.doTimeout(that.attr("data-do"));
	}
	$.loadingajax = function(id){
		if(id == undefined){
			id = "body";
		}
		$(id).prepend('<div class="loading-ajax"><span class="ico-loading-ajax" data-do="doLoadingAjax"></span></div>');
		$(".loading-ajax").css({opacity:0,display:"block"}).animate({opacity:1},500);
		$(".loading-ajax .ico-loading-ajax").animatepng(20,13);
	};
	$.loadingajax.close = function(suc) {
		$(".loading-ajax").stop().animate({opacity:1},300,"easeOutQuad",function(){
			$(".loading-ajax").animatestop();
			$(".loading-ajax").remove();
			if(suc!=undefined){
				suc();
			}
		});
	};
	$.getquery = function(url){
		if(url == undefined){
			url = window.location.href;
		}
		var u = url.split("?");
		if(typeof(u[1]) == "string"){
			u = u[1].split("&");
			var get = {};
			$.each(u, function(i) {
				var j = u[i].split("=");
				get[j[0]] = j[1];
			});
			return get;
		} else {
			return {};
		}
	};
	$.alert = function(title,type,time) {
		if(type == undefined||type == false){
			if(time == undefined){
				var time = 2000;
			}
			$(".alert").remove();
			$.doTimeout("doAlert");
			$.doTimeout("doAlert",1000,function(){
				time-=1000;
				if(time == 1000){
					$(".alert").remove();
					return false;
				}
				return true;
			});
			$("body").prepend('<div class="alert">'+title+'</div>');
		}else{
			$(".alert-confirm").remove();
			$("body").prepend('<div class="alert-confirm"><div class="alert"><div class="title">'+title+'</div><div class="ctrl"><div class="btn-yes">确定</div></div></div></div>');
			$(".alert .btn-yes").on("tap",function(){
				$(".alert-confirm").remove();
			});
			$(".alert-confirm").on('touchmove', function (event) {event.preventDefault();}, false);
		}
		$(".alert").css({marginLeft:-$(".alert").outerWidth()/2,marginTop:-$(".alert").outerHeight()/2});
	};
	$.confirm = function(title,suc,btn) {
		if(btn == undefined){
			btn = {yes:"是",no:"否"};
		}
		$(".confirm-wrapper").remove();
		$("body").prepend('\
			<div class="confirm-wrapper">\
				<div class="confirm">\
					<div class="title">'+title+'</div>\
					<div class="ctrl"><div class="btn-no">'+btn.no+'</div><div class="btn-yes">'+btn.yes+'</div></div>\
				</div>\
		</div>');
		$(".confirm").css({marginLeft:-$(".confirm").outerWidth()/2,marginTop:-$(".confirm").outerHeight()/2});
		$(".confirm .btn-no,.confirm .btn-yes").on("tap",function(){
			$(".confirm-wrapper").remove();
		});
		$(".confirm .btn-yes").on("tap",function(){
			if(suc!=undefined){
				suc();
			}
		});
		$(".confirm-wrapper").on('touchmove', function (event) {event.preventDefault();}, false);
	};
	$.success = function(title,suc,ctrl,btn) {
		if(btn == undefined){
			btn = {yes:"是",no:"否"};
		}
		var ctrlHtml = '';
		if(ctrl == undefined){
			ctrl = true;
		}
		if(ctrl){
			var borderRadius = "";
			var bg = "";
			var bs = "";
		}else{
			var borderRadius = "br10";
			$.doTimeout("doSuccessSuc");
			$.doTimeout("doSuccessSuc",2000,function(){
				$(".success-wrapper").remove();
				if(suc!=undefined){
					suc();
				}
			})
			var bg = "bg-none";
			var bs = "b bs";
		}
		if(ctrl){
			ctrlHtml = '<div class="ctrl"><div class="btn-no">'+btn.no+'</div><div class="btn-yes">'+btn.yes+'</div></div>';
		}
		$(".success-wrapper").remove();
		$("body").prepend('\
			<div class="success-wrapper '+bg+'">\
				<div class="success '+bs+'">\
					<div class="content '+borderRadius+'">\
						<div class="ac"><span class="ico ico-success" data-do="doSuccess"></span></div>\
						<div class="title">'+title+'</div>\
					</div>\
					'+ctrlHtml+'\
				</div>\
		</div>');
		$(".success").css({marginLeft:-$(".success").outerWidth()/2,marginTop:-$(".success").outerHeight()/2});
		$(".success .ico-success").animatepng(40,11,40,true);
		$(".success .btn-no,.success .btn-yes").on("tap",function(){
			$(".success-wrapper").remove();
		});
		$(".success .btn-yes").on("tap",function(){
			if(suc!=undefined){
				suc();
			}
		});
		$(".success-wrapper").on('touchmove', function (event) {event.preventDefault();}, false);
	};
	$.error = function(title,suc,ctrl,btn) {
		if(btn == undefined){
			btn = {yes:"是",no:"否"};
		}
		var ctrlHtml = '';
		if(ctrl == undefined){
			ctrl = true;
		}
		if(ctrl){
			var borderRadius = "";
			var bg = "";
			var bs = "";
		}else{
			var borderRadius = "br10";
			$.doTimeout("doErrorSuc");
			$.doTimeout("doErrorSuc",2000,function(){
				$(".error-wrapper").remove();
				if(suc!=undefined){
					suc();
				}
			})
			var bg = "bg-none";
			var bs = "b bs";
		}
		if(ctrl){
			ctrlHtml = '<div class="ctrl"><div class="btn-no">'+btn.no+'</div><div class="btn-yes">'+btn.yes+'</div></div>';
		}
		$(".error-wrapper").remove();
		$("body").prepend('\
			<div class="error-wrapper '+bg+'">\
				<div class="error '+bs+'">\
					<div class="content '+borderRadius+'">\
						<div class="ac"><span class="ico ico-error" data-do="doError"></span></div>\
						<div class="title">'+title+'</div>\
					</div>\
					'+ctrlHtml+'\
				</div>\
		</div>');
		$(".error").css({marginLeft:-$(".error").outerWidth()/2,marginTop:-$(".error").outerHeight()/2});
		$(".error .ico-error").animatepng(40,12,40,true);
		$(".error .btn-no,.error .btn-yes").on("tap",function(){
			$(".error-wrapper").remove();
		});
		$(".error .btn-yes").on("tap",function(){
			if(suc!=undefined){
				suc();
			}
		});
		$(".error-wrapper").on('touchmove', function (event) {event.preventDefault();}, false);
	};
	$.debug = function(title) {
		if(JH.debug){
			
			if($("body").find(".debug").length<=0){
				$("body").prepend('<div class="debug"><p>'+title+'</p></div>');
				$(".debug").on("tap",function(){
					$(".debug").remove();
					JH.debugNum = 0;
				});
			}else{
				$(".debug").append("<p>"+title+"</p>");
			}
			JH.debugNum++;
			if(JH.debugNum == 11){
				$(".debug p:first-child").remove();
				JH.debugNum--;
			}
		}
	};
	$.fn.choosebox = function(type) {
		if(type==undefined){
			type = 1;
		}
		$(this).each(function(i,d){
			var that = $(this).find(".choosebox");
			if(type==2){
				that.addClass("radiobox");
			}
			that.find("input").each(function(i,d){
				if($(d).prop("checked")){
					$(d).closest(".choosebox").addClass("active");
				}else{
					$(d).closest(".choosebox").removeClass("active");
				}
			})
			$(this).find(".choosebox").closest(".item").off("tap").on("tap",function(){
				if(type==1){
					if($(this).find(".choosebox").hasClass("active")){
						$(this).find(".choosebox").removeClass("active");
						$(this).find(".choosebox input").attr('checked',false);
					}else{
						$(this).find(".choosebox").addClass("active");
						$(this).find(".choosebox input").attr('checked',true);
					}
				}else{
					that.removeClass("active");
					$(this).find(".choosebox").addClass("active");
					that.find("input").attr('checked',false);
					$(this).find(".choosebox input").attr('checked',true);
				}
			});
		});
	};
	$.fn.openbox = function(text) {
		$(this).each(function(i,d){
			var that = $(this);
			that.find("input").each(function(i,d){
				if($(d).prop("checked")){
					$(d).closest(".openbox").addClass("active");
					if(text!=undefined){
						$(d).closest(".openbox").find("span").text(text[0]);
					}
				}else{
					$(d).closest(".openbox").removeClass("active");
					if(text!=undefined){
						$(d).closest(".openbox").find("span").text(text[1]);
					}
				}
			})
			$(this).on("tap",function(){
				if($(this).hasClass("active")){
					$(this).removeClass("active");
					$(this).find("input").attr('checked',false);
					if(text!=undefined){
						$(this).find("span").text(text[1]);
					}
				}else{
					$(this).addClass("active");
					$(this).find("input").attr('checked',true);
					if(text!=undefined){
						$(this).find("span").text(text[0]);
					}
				}
			});
		});
	};
	//tabs菜单
	$.fn.tabs = function(options) {
		var defaults = {
			active:1
		};
		var sets = $.extend(defaults, options || {});
		$(this).each(function(i,d){
			$(this).find(".tabs-con>.item").eq(sets.active-1).show();
			$(this).find(".tabs-menu>.item").eq(sets.active-1).addClass("active");
			var that = $(this);
			$(this).find(".tabs-menu>.item").each(function(i,id){
				$(this).on("tap",function(){
					that.find(".tabs-menu>.item").removeClass("active");
					$(this).addClass("active");
					that.find(".tabs-con>.item").eq($(this).index()).show().siblings().hide();
				});
			});
		});
	};
	//accordion菜单
	$.fn.accordion = function(options) {
		var defaults = {
			active:1,
			model:"multi"
		};//model:one,multi
		var sets = $.extend(defaults, options || {});
		$(this).each(function(i,d){
			if(sets.active!=-1){
				$(this).find(".item").eq(sets.active-1).addClass("active");
				$(this).find(".item").eq(sets.active-1).find(".con").show();
			}
			$(this).find(".item .switch").each(function(i,id){
				$(this).closest(".title").on("tap",function(){
					if(sets.model=="multi"){
						if($(this).closest(".item").hasClass("active")){
							$(this).closest(".item").removeClass("active");
							$(this).closest(".item").find(".con").slideUp(500);
						}else{
							$(this).closest(".item").addClass("active");
							$(this).closest(".item").find(".con").slideDown(500);
						}
					}else{
						if(!$(this).closest(".item").hasClass("active")){
							$(this).closest(".accordion").find(".item").removeClass("active");
							$(this).closest(".accordion").find(".con").slideUp(500);
							$(this).closest(".item").addClass("active");
							$(this).closest(".item").find(".con").slideDown(500);
						}else{
							$(this).closest(".item").removeClass("active");
							$(this).closest(".item").find(".con").slideUp(500);
						}
					}
				});
			});
		});
	};
})(jQuery);
var JH = {
	debugNum:0,debug:true,
	html:"",title:[],eff:[],
	post:false,back:false,closenum:1,
	url:window.location.href.split( "/" )[ window.location.href.split( "/" ).length-1 ],
	pageurl:[window.location.href.split( "/" )[ window.location.href.split( "/" ).length-1 ]],
	newurl:[],
	closesuc:$.noop,
	point:{lat:"",lng:""},
	reg:{
		phone: /^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/,
		telphone: /(^(\(\d{3,4}\)|\d{3,4}-)?\d{7,8}$)|(^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$)/,
		user: /^[a-zA-Z0-9_]{1,16}$/,
		pass: /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,16}$/,
		email: /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
		number: /^\d{1,}$/,
		code: /^[a-zA-Z0-9]{4}$/,
		chinese: /^[\u4e00-\u9fa5]{1,}$/,
		price: /^[0-9]+(.[0-9]{1,2})?$/,
		identity: /^[1-9]([0-9]{16}|[0-9]{13})[xX0-9]$/
	},
	data:{
		
	},
	apiURL:"http://api.jiahao400.com"
};
JH.NumberFormat = {
	format:function(num,pattern){//格式化数字显示方式  JH.NumberFormat.format(12345.999,'#,##0.00')|(12345.999,'#,##0.##')|(123,'000000')
		var strarr = num?num.toString().split('.'):['0'];
		var fmtarr = pattern?pattern.split('.'):[''];
		var retstr='';	
		// 整数部分
		var str = strarr[0];
		var fmt = fmtarr[0];
		var i = str.length-1;  
		var comma = false;
		//console.log(fmt.substr(0,1));
		for(var f=fmt.length-1;f>=0;f--){
			switch(fmt.substr(f,1)){
				case '#':
					if(i>=0 ) retstr = str.substr(i--,1) + retstr;
					break;
				case '0':
					if(i>=0) retstr = str.substr(i--,1) + retstr;
					else retstr = '0' + retstr;
					break;
				case ',':
					comma = true;
					retstr=','+retstr;
					break;
			}
		}
		if(i>=0){
			if(comma){
				var l = str.length;
				for(;i>=0;i--){
					retstr = str.substr(i,1) + retstr;
					if(i>0 && ((l-i)%3)==0) retstr = ',' + retstr;
				}
			}
			else retstr = str.substr(0,i+1) + retstr;
		}	
		retstr = retstr+'.';
		//小数部分
		str=strarr.length>1?strarr[1]:'';
		fmt=fmtarr.length>1?fmtarr[1]:'';
		i=0;
		for(var f=0;f<fmt.length;f++){
			switch(fmt.substr(f,1)){
				case '#':
					if(i<str.length) retstr+=str.substr(i++,1);
					break;
				case '0':
					if(i<str.length) retstr+= str.substr(i++,1);
					else retstr+='0';
					break;
			}
		}
		return retstr.replace(/^,+/,'').replace(/\.$/,'');
	}
}
JH.NumberUtilities = {
	_aUniqueIDs:[],
	getUnique:function(){//返回当前纪元日毫秒数,产生独一无二数字
		if(this._aUniqueIDs == null) {
			this._aUniqueIDs = new Array();
		}
		var dCurrent = new Date();
		var nID = dCurrent.getTime();
		while(!this.isUnique(nID)){
			nID += this.random(dCurrent.getTime(), 2 * dCurrent.getTime());
		}	
		this._aUniqueIDs.push(nID);
		return nID;
    },
	isUnique:function(nNumber){
		for(var i = 0; i < this._aUniqueIDs.length; i++) {
			if(this._aUniqueIDs[i] == nNumber) {
				return false;
			}
		}
		return true;
	},
	random:function(nMinimum, nMaximum, nRoundToInterval) {//生成随机数
		nMaximum?nMaximum:nMaximum = 0;
		nRoundToInterval?nRoundToInterval:nRoundToInterval = 1;
		if(nMinimum > nMaximum) {
			var nTemp = nMinimum;
			nMinimum = nMaximum;
			nMaximum = nTemp;
		}
		var nDeltaRange = (nMaximum - nMinimum) + (1 * nRoundToInterval);
		var nRandomNumber = Math.random() * nDeltaRange;
		nRandomNumber += nMinimum;
		return Math.floor(nRandomNumber, nRoundToInterval);
	}
}
JH.StringUtilities = {
	isWhitespace:function( ch ) {
		return ch == '\r' || 
				ch == '\n' ||
				ch == '\f' || 
				ch == '\t' ||
				ch == ' '; 
	},
	trim:function( original ) {//剪去开始结尾处空白
		var characters = original.split( "" );
		for ( var i = 0; i < characters.length; i++ ) {
			if ( this.isWhitespace( characters[i] ) ) {
				characters.splice( i, 1 );
				i--;
			} else {
				break;
			}
		}
		for ( i = characters.length - 1; i >= 0; i-- ) {
			if ( this.isWhitespace( characters[i] ) ) {
				characters.splice( i, 1 );
			} else {
				break;
			}
		}
		return characters.join("");
	},
	limitLength:function(str,num){
		var l = 0;
		var a = str.split("");
		for (var i=0;i<num;i++) {
			if (a[i].charCodeAt(0)<299) {
				l+=1.5;
			} else {
				l++;
			}
		}
		return l;
	}
};
JH.ArrayUtilities = {
	randomize:function(aArray){//数组元素随机化
		var aCopy = aArray.concat();
		var aRandomized = new Array();
		var oElement;
		var nRandom;
		for(var i = 0; i < aCopy.length; i++) {
			nRandom = JH.NumberUtilities.random(0, aCopy.length - 1);
			aRandomized.push(aCopy[nRandom]);
			aCopy.splice(nRandom, 1);
			i--;
		}
		return aRandomized;
	},
	rightShift:function(arr, N, k) {
		var temp = new Array();
		temp = arr.concat();
		k %= N;
		this.reverse(temp, 0, N-k-1);
		this.reverse(temp, N-k, N-1);
		this.reverse(temp, 0, N - 1);
		return temp;
	},
	reverse:function(arr, b, e){
		var temp;
		for (b; b < e; b++, e--) {
			temp = arr[e];
			arr[e] = arr[b];
			arr[b] = temp;
		}
	},
	toString:function(oArray, nLevel) {//快速输出数组内容
		nLevel?nLevel:nLevel = 0;
		var sIndent = "";
		for(var i = 0; i < nLevel; i++) {
			sIndent += "\t";
		}
		var sOutput = "";
		for(var sItem in oArray) {
			if(typeof oArray[sItem] == "object") {
				sOutput = sIndent + "** " + sItem + " **\n" + toString(oArray[sItem], nLevel + 1) + sOutput;
			}
			else {
				sOutput += sIndent + sItem + ":" + oArray[sItem] + "\n";
			}
		}
		return sOutput;
	}
}
JH.float = {
	div:function(arg1,arg2){ 
		var t1=0,t2=0,r1,r2,m,n;  
	    try{t1=arg1.toString().split(".")[1].length}catch(e){}  
	    try{t2=arg2.toString().split(".")[1].length}catch(e){}  
	    with(Math){  
	        r1=Number(arg1.toString().replace(".",""))  
	        r2=Number(arg2.toString().replace(".",""))
	        m = ((r1/r2)*pow(10,t2-t1));
	        //动态控制精度长度
	        try{n=m.toString().split(".")[1].length>2?2:m.toString().split(".")[1].length}catch(e){n=0}
			return m.toFixed(n);
	    }
	},
	mul:function(arg1,arg2){ 
		var m=0,s1=arg1.toString(),s2=arg2.toString(); 
		try{m+=s1.split(".")[1].length}catch(e){} 
		try{m+=s2.split(".")[1].length}catch(e){} 
		return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m);
	},
	add:function(arg1,arg2){ 
		var r1,r2,m; 
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0} 
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0} 
		m=Math.pow(10,Math.max(r1,r2));
		return (arg1*m+arg2*m)/m;
	},
	minus:function(arg1,arg2){ 
		var r1,r2,m,n;
		try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
		try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
		m=Math.pow(10,Math.max(r1,r2));
		//动态控制精度长度
		n=(r1>=r2)?r1:r2;
		return ((arg1*m-arg2*m)/m).toFixed(n);
	}
}
JH.load = {
	target:function(obj){
		if(obj.data == undefined){
			obj.data = {};	
		}
		if(!JH.post){
			JH.post = true;
			$.ajax({
				type:"post",
				url: obj.url,
				data:obj.data,
				dataType:"html",
				cache:false,
				success: function(d){
					$.loadingajax.close(function(){
						$(obj.id).html(d.match(/<section id="list">([\s\S]*?)<\/section>/)[1]);
						if(obj.suc != undefined){
							obj.suc(d);
						}
						JH.post = false;
					});
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(XMLHttpRequest.status==400){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
					}else if(XMLHttpRequest.status==404){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
					}else if(XMLHttpRequest.status==500){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
					}else{
						$.error('<div class="f38">'+ XMLHttpRequest.status + '</div>',function(){},false);
					}
					JH.post = false;
				}
			})
		}
	},
	page:function(url){
		if(!JH.post){
			JH.post = true;
			$.ajax({
				type:"get",
				url: url,
				dataType:"html",
//				cache:false,
				success: function(d){
					if(!JH.back){
						history.pushState("","",url);
						$("title").text(d.match(/<title>([\s\S]*?)<\/title>/)[1]);
						JH.title.unshift($("title").text());
						JH.pageurl.unshift(url);
					}else{
						JH.title.shift();
						JH.pageurl.shift();
						$("title").text(d.match(/<title>([\s\S]*?)<\/title>/)[1]);
					}
					JH.url = url;
					JH.back = false;
					JH.post = false;
					$(".page").last().html(d.match(/<!--page=begin-->([\s\S]*?)<!--page=end-->/)[0]);
					if(parseInt($("#header-menu-list").css("left"))!=0){
						$(".page").last().find(".main").css("left",0);
						$(".page").last().find(".operation").css("left",0);
					}
					$(".money").each(function(i,d){
						$(d).text(JH.NumberFormat.format($(d).text(),"#,###.00"));
					})
					$(".table").each(function(i,d){
						$(d).children("tbody").children("tr:odd").addClass("odd");
						$(d).children("tbody").children("tr:even").addClass("even");
					})
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(XMLHttpRequest.status==400){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
					}else if(XMLHttpRequest.status==404){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
					}else if(XMLHttpRequest.status==500){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
					}else{
						$.error('<div class="f38">'+ XMLHttpRequest.status + '</div>',function(){},false);
					}
					JH.post = false;
				}
			})
		}
	},
	new:function(url,eff){
		if(eff == undefined){
			eff = "slideInRight";
		}
		if(!JH.post){
			JH.post = true;
			$.ajax({
				type:"get",
				url: url,
				dataType:"html",
//				cache:false,
				success: function(d){
					JH.post = false;
					JH.pageurl.unshift(JH.pageurl[0]);
					JH.newurl.unshift(url);
					history.pushState("","",JH.pageurl[0]);
					JH.eff.unshift(eff);
					$("#header-menu-list").addClass("header-menu-list-new");
					if(eff=="slideInRight"){
						$(".page").last().removeClass("slideOutLeftSmall slideInLeftSmall").addClass("animated slideOutLeftSmall");
					}
					$("body").append('<div class="page pagenew z10 vv animated '+eff+'"></div>');
					$(".page").last().html(d.match(/<!--page=begin-->([\s\S]*?)<!--page=end-->/)[0]);
					$(".page").last().one("webkitAnimationEnd mozAnimationEnd animationend", function(){
						if($(".page").length>1){
							$(".page").last().removeClass("pf");
						}
					});
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					JH.post = false;
					if(XMLHttpRequest.status==400){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
					}else if(XMLHttpRequest.status==404){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
					}else if(XMLHttpRequest.status==500){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
					}else{
						$.error('<div class="f38">'+ XMLHttpRequest.status + '</div>',function(){},false);
					}
				}
			})
		}
	},
	show:function(url){
		if(!JH.post){
			JH.post = true;
			$.ajax({
				type:"get",
				url: url,
				dataType:"html",
//				cache:false,
				success: function(d){
					JH.post = false;
					JH.pageurl.unshift(JH.pageurl[0]);
					JH.newurl.unshift(url);
					history.pushState("","",JH.pageurl[0]);
					$("body").append('<div class="page z10 vv showClose animated fadeIn"></div>');
					$(".page").last().html(d.match(/<!--page=begin-->([\s\S]*?)<!--page=end-->/)[0]);
					if(parseInt($("#header-menu-list").css("left"))!=0){
						$(".page").last().find(".scroll").css("left",0);
						$(".page").last().find(".operation").css("left",0);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(XMLHttpRequest.status==400){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
					}else if(XMLHttpRequest.status==404){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
					}else if(XMLHttpRequest.status==500){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
					}else{
						$.error('<div class="f38">'+ XMLHttpRequest.status + '</div>',function(){},false);
					}
					JH.post = false;
				}
			})
		}
	},
	refresh:function(url,id,state,suc){
		if(!JH.post){
			JH.post = true;
			if(url == undefined){
				url = window.location.href;
			}
			if(state==undefined){
				state = true;
			}
			if(id==undefined){
				id = true;
			}
			if(id){
				id=$(".page").last();
				state = false;
			}else{
				id=$(".page").first();
			}
			$.ajax({
				type:"get",
				url: url,
				dataType:"html",
//				cache:false,
				success: function(d){
					JH.post = false;
					if(state){
						history.replaceState("","",url);
					}
					$(id).off("hold tap doubletap pinchend rotate swipeleft swiperight swipeup swipedown drag dragend touchstart touchmove touchend change");
					$(id).html(d.match(/<!--page=begin-->([\s\S]*?)<!--page=end-->/)[0]);
					if(suc != undefined){
						suc();
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(XMLHttpRequest.status==400){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
					}else if(XMLHttpRequest.status==404){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
					}else if(XMLHttpRequest.status==500){
						$.error('<div class="f38 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
					}else{
						$.error('<div class="f38">'+ XMLHttpRequest.status + '</div>',function(){},false);
					}
					JH.post = false;
				}
			})
		}
	},
	close:function(num){
		if(num == undefined){
			window.history.go(-($(".page").length-1));
			JH.closenum = $(".page").length-1;
		}else{
			window.history.go(-num);
			JH.closenum = num;
		}
	},
	newclose:function(d){
		JH.post = true;
		if(d == undefined){
			d = {};
		}
		if(JH.eff[0] == "slideInRight"){
			d.eff = "slideOutRight";
		}else if(JH.eff[0] == "zoomIn"){
			d.eff = "zoomOut";
		}else if(JH.eff[0] == "fadeIn"){
			d.eff = "fadeOut";
		}else if(JH.eff[0] == "slideInUp"){
			d.eff = "slideOutDown";
		}
		if(JH.closenum>1){
			var num = $(".page").length-1;
			for(var i=1;i<=JH.closenum;i++){
				$(".page").eq(num).removeClass("slideInLeft slideInRight slideInUp slideInDown fadeIn zoomIn slideInLeftSmall slideOutLeftSmall");
				$(".page").eq(num).addClass(d.eff);
				num--;
			}
			if(d.eff=="slideOutRight"){
				$(".page").eq($(".page").length-2).removeClass("slideOutLeftSmall slideInLeftSmall").addClass("slideInLeftSmall");
			}
			$(".page").last().one("webkitAnimationEnd mozAnimationEnd animationend", function(){
				var num = $(".page").length-1;
				for(var i=1;i<=JH.closenum;i++){
					JH.pageurl.shift();
					JH.newurl.shift();
					if($(".page").length>1){
						JH.eff.shift();
					}
					$(".page").eq(num).remove();
					num--;
				}
				if($(".page").length==1){
					$("#header-menu-list").removeClass("header-menu-list-new");
					$("#header").removeClass("dn slideOutUp");
				}
				JH.post = false;
				JH.back = false;
				if(d.suc != undefined){
					d.suc();
				}
				JH.closenum = 1;
			});
		}else{
			$(".page").last().removeClass("slideInLeft slideInRight slideInUp slideInDown fadeIn zoomIn slideOutLeftSmall slideInLeftSmall");
			$(".page").last().addClass(d.eff);
			if(d.eff=="slideOutRight"){
				$(".page").eq($(".page").length-2).removeClass("slideOutLeftSmall slideInLeftSmall").addClass("slideInLeftSmall");
			}
			$(".page").last().one("webkitAnimationEnd mozAnimationEnd animationend", function(){
				JH.pageurl.shift();
				JH.newurl.shift();
				JH.eff.shift();
				$("title").text(JH.title[0]);
				$(".page").last().remove();
				JH.post = false;
				JH.back = false;
				if(d.suc != undefined){
					d.suc();
				}
			});
		}
	},
	showclose:function(d){
		JH.post = true;
		if(d == undefined){
			d = {};
		}
		if(JH.closenum>1){
			var num = $(".page").length-1;
			for(var i=1;i<=JH.closenum;i++){
				$(".page").eq(num).fadeOut();
				num--;
			}
			$(".page").last().removeClass("fadeIn").fadeOut(function(){
				var num = $(".page").length-1;
				for(var i=1;i<=JH.closenum;i++){
					JH.pageurl.shift();
					JH.newurl.shift();
					$(".page").eq(num).remove();
					num--;
				}
				JH.post = false;
				JH.back = false;
				if(d.suc != undefined){
					d.suc();
				}
				JH.closenum = 1;
			});
		}else{
			$("title").text(JH.title[0]);
			$(".page").last().removeClass("fadeIn").fadeOut(function(){
				JH.pageurl.shift();
				JH.newurl.shift();
				$(".page").last().remove();
				JH.post = false;
				JH.back = false;
				if(d.suc != undefined){
					d.suc();
				}
			});
		}
	},
	jsonGET:function(url,suc,data,loading){
		if(!JH.post){
			if(data == undefined){
				data = {};
			}
			if(loading == undefined){
				loading = true;
			}
			if(loading){
				$.loadingajax();
			}
			JH.post = true;
			$.ajax({
				type:"GET",
				url: url,
				data:data,
				crossDomain:true,
				xhrFields: {
		        		withCredentials: true
		        },
				dataType:"json",
				cache:false,
				success: function(d){
					if(loading){
						$.loadingajax.close(function(){
							JH.post = false;
							suc(d);
						});
					}else{
						JH.post = false;
						suc(d);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(loading){
						$.loadingajax.close(function(){
							JH.post = false;
							if(XMLHttpRequest.status==400){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
							}else if(XMLHttpRequest.status==404){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
							}else if(XMLHttpRequest.status==500){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
							}else{
								$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
							}
						});
					}else{
						JH.post = false;
						if(XMLHttpRequest.status==400){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
						}else if(XMLHttpRequest.status==404){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
						}else if(XMLHttpRequest.status==500){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
						}else{
							$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
						}
					}
					
				}
			});
		}
	},
	jsonGET2:function(url,suc,data){
		if(data == undefined){
			data = {};
		}
		$.ajax({
			type:"GET",
			url: url,
			data:data,
			crossDomain:true,
			xhrFields: {
	        		withCredentials: true
	        },
			dataType:"json",
			cache:false,
			success: function(d){
				suc(d);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if(XMLHttpRequest.status==400){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
				}else if(XMLHttpRequest.status==404){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
				}else if(XMLHttpRequest.status==500){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
				}else{
					$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
				}
			}
		});
	},
	jsonPOST:function(url,suc,data,loading){
		if(!JH.post){
			if(data == undefined){
				data = {};
			}
			if(loading == undefined){
				loading = true;
			}
			if(loading){
				$.loadingajax();
			}
			JH.post = true;
			$.ajax({
				type:"POST",
				url: url,
				data:data,
				crossDomain:true,
				xhrFields: {
		        		withCredentials: true
		        },
				dataType:"json",
				success: function(d){
					if(loading){
						$.loadingajax.close(function(){
							JH.post = false;
							suc(d);
						});
					}else{
						JH.post = false;
						suc(d);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(loading){
						$.loadingajax.close(function(){
							JH.post = false;
							if(XMLHttpRequest.status==400){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
							}else if(XMLHttpRequest.status==404){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
							}else if(XMLHttpRequest.status==500){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
							}else{
								$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
							}
						});
					}else{
						JH.post = false;
						if(XMLHttpRequest.status==400){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
						}else if(XMLHttpRequest.status==404){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
						}else if(XMLHttpRequest.status==500){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
						}else{
							$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
						}
					}
				}
			});
		}
	},
	jsonPOST2:function(url,suc,data){
		if(data == undefined){
			data = {};
		}
		$.ajax({
			type:"POST",
			url: url,
			data:data,
			crossDomain:true,
			xhrFields: {
	        		withCredentials: true
	        },
			dataType:"json",
			success: function(d){
				suc(d);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if(XMLHttpRequest.status==400){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
				}else if(XMLHttpRequest.status==404){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
				}else if(XMLHttpRequest.status==500){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
				}else{
					$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
				}
			}
		});
	},
	jsonp:function(url,suc,data,loading){
		if(!JH.post){
			if(data == undefined){
				data = {};
			}
			if(loading == undefined){
				loading = true;
			}
			if(loading){
				$.loadingajax();
			}
			JH.post = true;
			$.ajax({
				type: "GET",
				url: url,
				data:data,
				dataType:"jsonp",
				jsonp: "callback",
				jsonpCallback:"suc",
				success: function(d){
					if(loading){
						$.loadingajax.close(function(){
							JH.post = false;
							suc(d);
						});
					}else{
						JH.post = false;
						suc(d);
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(loading){
						$.loadingajax.close(function(){
							JH.post = false;
							if(XMLHttpRequest.status==400){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
							}else if(XMLHttpRequest.status==404){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
							}else if(XMLHttpRequest.status==500){
								$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
							}else{
								$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
							}
						});
					}else{
						JH.post = false;
						if(XMLHttpRequest.status==400){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
						}else if(XMLHttpRequest.status==404){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
						}else if(XMLHttpRequest.status==500){
							$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
						}else{
							$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){},false);
						}
					}
				}
			})
		}
	},
	jsonp2:function(url,suc,data){
		if(data == undefined){
			data = {};
		}
		$.ajax({
			type: "GET",
			url: url,
			data:data,
			dataType:"jsonp",
			jsonp: "callback",
			jsonpCallback:"suc",
			success: function(d){
				suc(d);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if(XMLHttpRequest.status==400){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>请求语法错误',function(){},false);
				}else if(XMLHttpRequest.status==404){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>你所访问的页面不存在',function(){},false);
				}else if(XMLHttpRequest.status==500){
					$.error('<div class="f18 mb10">'+ XMLHttpRequest.status + '</div>服务器处理出错',function(){},false);
				}else{
					$.error('<div class="f18">'+ XMLHttpRequest.status + '</div>',function(){});
				}
			}
		})
	}
}
JH.limit=function(str,num){//截取文字
	var objString = str;
	var objLength = str.length;
	if(objLength > num){
		objString = objString.substring(0,JH.StringUtilities.limitLength(objString,num))+"…";
	}
	return objString;
}
JH.transDate = function(options){
	options = options.replace(/-/g, '/');
	var date=new Date(options); 
	date.setFullYear(options.substring(0,4)); 
	date.setMonth(options.substring(5,7)-1); 
	date.setDate(options.substring(8,10)); 
	date.setHours(options.substring(11,13)); 
	date.setMinutes(options.substring(14,16)); 
	date.setSeconds(options.substring(17,19)); 
	return Date.parse(date);
}
JH.toDate = function(options,format){
	var date = new Date(options);
	Y = date.getFullYear() + '-';
	M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
	D = (date.getDate() < 10 ? '0'+(date.getDate()) : date.getDate()) + ' ';
	h = (date.getHours() < 10 ? '0'+(date.getHours()) : date.getHours()) + ':';
	m = (date.getMinutes() < 10 ? '0'+(date.getMinutes()) : date.getMinutes()) + ':';
	s = (date.getSeconds() < 10 ? '0'+(date.getSeconds()) : date.getSeconds());
	if(format == undefined){
		return Y+M+D+h+m+s;
	}else{
		return Y+M+$.trim(D);;
	}
}
JH.browserandroid = function(){
	if(/android/i.test(navigator.userAgent)){
		return true;
	}else{
		return false;
	}
}
JH.browserios = function(){
	if(/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)){
		return true;
	}else{
		return false;
	}
}
JH.browserwx = function(){
	if(/MicroMessenger/i.test(navigator.userAgent)){
		return true;
	}else{
		return false;
	}
}
JH.setupWebViewJavascriptBridge = function(callback) {
	if (window.WebViewJavascriptBridge) { return callback(WebViewJavascriptBridge); }
	if (window.WVJBCallbacks) { return window.WVJBCallbacks.push(callback); }
	window.WVJBCallbacks = [callback];
	var WVJBIframe = document.createElement('iframe');
	WVJBIframe.style.display = "none";
	WVJBIframe.src = "wvjbscheme://__BRIDGE_LOADED__";
	document.documentElement.appendChild(WVJBIframe);
	setTimeout(function() { document.documentElement.removeChild(WVJBIframe) }, 0);
}
JH.imageLoad = function(s){
	function $id(id){ return document.getElementById(id);}
	function $c(tagName){ return document.createElement(tagName);}
	function imageLoad(s){
		var urlset = [], undefined, toString = Object.prototype.toString;
		switch( toString.apply(s.url) ){
			case '[object String]': urlset[urlset.length] = s.url; break;
			case '[object Array]': if(!s.url.length){ return false; } urlset = s.url; break;
			case '[object Function]': s.url = s.url(); return imageLoad( s );
			default: return false;
		}
		var imgset =[], r ={ total:urlset.length, load:0, error:0, abort:0, complete:0, currentIndex:0 }, timer,
			_defaults = {
				url:'',
				onload: 'function',
				onerror: 'function',
				oncomplete: 'function',
				ready: 'function',
				complete: 'function',
				timeout: 15
			};
		for( var v in _defaults){
			s[v] = s[v]===undefined? _defaults[v]: s[v];
		}
		s.timeout = parseInt( s.timeout ) || _defaults.timeout;
		timer = setTimeout( _callback, s.timeout*1000);
		for( var i=0,l=urlset.length,img; i<l; i++){
			img 		= new Image();
			img.loaded	= false;
			imgset[imgset.length] = img;
		}	for( i=0,l=imgset.length; i<l; i++){
			imgset[i].onload  	= function(){ _imageHandle.call(this, 'load', i ); };
			imgset[i].onerror 	= function(){ _imageHandle.call(this, 'error', i ); };
			imgset[i].onabort 	= function(){ _imageHandle.call(this, 'abort', i ); };
			imgset[i].src 		= ''+urlset[i];
		}
		if( _isFn(s.ready) ){ s.ready.call({}, imgset, r); }	
		function _imageHandle( handle, index ){
			r.currentIndex = index;
			switch( handle ){
				case 'load':
					this.onload  = null; this.loaded = true; r.load++;
					if( _isFn(s.onload) ){ s.onload.call(this, r); }	
					break;case 'error': r.error++;
					if( _isFn(s.onerror) ){ s.onerror.call(this, r); }
					break;
				case 'abort': r.abort++; break;
			}
			r.complete++;
			// oncomplete 事件回调
			if( _isFn(s.oncomplete) ){ s.oncomplete.call(this, r); }
			// 判断全局加载
			if( r.complete===imgset.length ){  _callback(); }
		}
		function _callback(){
			clearTimeout( timer );
			if( _isFn(s.complete) ){ s.complete.call({}, imgset, r); }
		}
		function _isFn(fn){ return toString.apply(fn)==='[object Function]'; }
		return true;
	}
	imageLoad(s);
}
//if(JH.browserwx()||JH.browserandroid()||JH.browserios()){
//	
//}else{
//	window.location.href = "/";
//}
JH.htmlEncode = function (html){
	var temp = document.createElement ("div");
	(temp.textContent != undefined ) ? (temp.textContent = html) : (temp.innerText = html);
	var output = temp.innerHTML;
	temp = null;
	return output;
 }
JH.htmlDecode = function (text){
	var temp = document.createElement("div");
	temp.innerHTML = text;
	var output = temp.innerText || temp.textContent;
	temp = null;
	return output;
} 
 /*
 loadScript.add({
    url:'ad.js',
    container: 'msat-adwrap',
    callback:function(){ console.log('msat-adwrap'); }
  }).add({
    url:'ad2.js',
    container: 'msat-adwrap2',
    callback:function(){ console.log('msat-adwrap2'); }
  }).add({//google adsense
    url:'http://pagead2.googlesyndication.com/pagead/show_ads.js',
    container: 'msat-adwrap',
    init: function(){
      google_ad_client = "ca-pub-2152294856721899";
      google_ad_slot = "3929903770";
      google_ad_width = 250;
      google_ad_height = 250;
    },
    callback:function(){ console.log('msat-adwrap3'); }
  }).execute();
*/
var loadScript = ( function() {
    var adQueue = [], dw = document.write;
    //缓存js自身的document.write

    function LoadADScript(url, container, init, callback) {
        this.url = url;
        this.containerObj = ( typeof container == 'string' ? document.getElementById(container) : container);
        this.init = init ||
        function() {
        };


        this.callback = callback ||
        function() {
        };

    }


    LoadADScript.prototype = {
        startLoad : function() {
            var script = document.createElement('script'), _this = this;

            _this.init.apply();

            if(script.readyState) {//IE
                script.onreadystatechange = function() {
                    if(script.readyState == "loaded" || script.readyState == "complete") {
                        script.onreadystatechange = null;
                        _this.startNext();
                    }
                };
            } else {//Other
                script.onload = function() {
                    _this.startNext();
                };
            }
            //重写document.write
            document.write = function(ad) {
                var html = _this.containerObj.innerHTML;
                _this.containerObj.innerHTML = html + ad;
            }

            script.src = _this.url;
            script.type = 'text/javascript';
            document.getElementsByTagName('head')[0].appendChild(script);
        },
        finished : function() {
            //还原document.write
            document.write = this.dw;
        },
        startNext : function() {
            adQueue.shift();
            this.callback.apply();
            if(adQueue.length > 0) {
                adQueue[0].startLoad();
            } else {
                this.finished();
            }
        }
    };

    return {
        add : function(adObj) {
            if(!adObj)
                return;

            adQueue.push(new LoadADScript(adObj.url, adObj.container, adObj.init, adObj.callback));
            return this;
        },
        execute : function() {
            if(adQueue.length > 0) {
                adQueue[0].startLoad();
            }
        }
    };
}());
JH.userinfo = function(suc){
	if(JH.data.userinfo!=undefined){
		if(suc!=undefined){
			suc();
		}
	}else{
		JH.load.jsonGET2(JH.apiURL+"/rest/userinfo/",function(d){
			if(d.code == 1){
				JH.data.userinfo = d.data;
				if(suc!=undefined){
					suc();
				}
			}else{
				if(suc!=undefined){
					suc();
				}
			}
		});
	}
}