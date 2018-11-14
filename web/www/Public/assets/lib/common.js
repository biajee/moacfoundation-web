// JavaScript Document
function getbyid(id) {
	return document.getElementById(id);
}

function display(id) {
	var obj = getbyid(id);
	if (obj.style.display=='none')
		obj.style.display = '';
	else
		obj.style.display = "none";
}

function selectall(obj,name){
	var items = document.getElementsByName(name);
		if(items!=null)
			for(i=0;i<items.length;i++)
				items[i].checked = obj.checked;
}

function myalert(title,message){
	Boxy.alert(message,null,{title:title,closeable:true,modal:false});
}


function getEvent() {
	if(document.all) return window.event;
	func = getEvent.caller;
	while(func != null) {
		var arg0 = func.arguments[0];
		if (arg0) {
			if((arg0.constructor  == Event || arg0.constructor == MouseEvent) || (typeof(arg0) == "object" && arg0.preventDefault && arg0.stopPropagation)) {
				return arg0;
			}
		}
		func=func.caller;
	}
	return null;
}


function stopEvent(event, preventDefault, stopPropagation) {
	var preventDefault = isUndefined(preventDefault) ? 1 : preventDefault;
	var stopPropagation = isUndefined(stopPropagation) ? 1 : stopPropagation;
	e = event ? event : window.event;
	if(!e) {
		e = getEvent();
	}
	if(!e) {
		return null;
	}
	if(preventDefault) {
		if(e.preventDefault) {
			e.preventDefault();
		} else {
			e.returnValue = false;
		}
	}
	if(stopPropagation) {
		if(e.stopPropagation) {
			e.stopPropagation();
		} else {
			e.cancelBubble = true;
		}
	}
	return e;
}

function addFavorite(url, title) {
	try {
		window.external.addFavorite(url, title);
	} catch (e){
		try {
			window.sidebar.addPanel(title, url, '');
        	} catch (e) {
			alert("请按 Ctrl+D 键添加到收藏夹");
		}
	}
}

function setHomepage(sURL) {
	if($.browser.msie){
		document.body.style.behavior = 'url(#default#homepage)';
		document.body.setHomePage(sURL);
	} else {
		if(window.netscape) {
			try {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			}
			catch (e) {
					alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入about:config并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
			}
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref('browser.startup.homepage',sURL);
	 }
	}
}
//
// JavaScript Document
//字符处理;
//去左右空格;
function trim(s){
return rtrim(ltrim(s));
}
//去左空格;
function ltrim(s){
return s.replace( /^\s*/, "");
}
//去右空格;
function rtrim(s){
return s.replace( /\s*$/, "");
}
//验证信息;
//空字符值;
function isEmpty(s){
s = trim(s);
return s.length == 0;
}
//Email;
function isEmail(s){
s = trim(s);
var p = /^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.){1,4}[a-z]{2,3}$/i;
return p.test(s);
}
//数字;
function isNumber(s){
return !isNaN(s);
}
//颜色值;
function isColor(s){
s = trim(s);
if (s.length !=7) return false;
return s.search(/\#[a-fA-F0-9]{6}/) != -1;
}
//国际手机号
function isMobile(s) {
	s = trim(s);
	var r = /\+?\d{4,}/;
	return r.test(s);
}
//手机号码;
function isMobileCN(s){
s = trim(s);
var p = /^1[345678][0-9]{9}$/;
return p.test(s);
}
//身份证;
function isCard(s){
s = trim(s);
var p = /^\d{15}(\d{2}[xX0-9])?$/;
return p.test(s);
}
//URL;
function isURL(s){
s = trim(s).toLowerCase();
var p = /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
return p.test(s);
}
//Phone;
function isPhone(s){
s = trim(s);
var p = /^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/;
return p.test(s);
}
//Zip;
function isZip(s){
s = trim(s);
var p = /^[1-9]\d{5}$/;
return p.test(s);
}
//Double;
function isDouble(s){
s = trim(s);
var p = /^[-\+]?\d+(\.\d+)?$/;
return p.test(s);
}
//Integer;
function isInteger(s){
s = trim(s);
var p = /^[-\+]?\d+$/;
return p.test(s);
}
//English;
function isEnglish(s){
s = trim(s);
var p = /^[A-Za-z]+$/;
return p.test(s);
}
//中文;
function isChinese(s){
s = trim(s);
var p = /^[\u0391-\uFFE5]+$/;
return p.test(s);
}
//双字节
function isDoubleChar(s){
var p = /^[^\x00-\xff]+$/;
return p.test(s);
}
//含有中文字符
function hasChineseChar(s){
var p = /[^\x00-\xff]/;
return p.test(s);
}
function hasAccountChar(s){
var p = /^[a-zA-Z0-9][a-zA-Z0-9_-]{0,15}$/;
return p.test(s);
}
function limitLen(s,Min,Max){
s=trim(s);
if(s=="") return false;
if((s.length<Min)||(s.length>Max))
return false;
else
return true;
}