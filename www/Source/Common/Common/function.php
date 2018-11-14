<?php

function tree_build(&$list,$upid=0,$idfield='id',$upfield='upid',$leaffield='isleaf') {
    $result = array();
    foreach($list as $k => $v) {
        if ($v[$upfield]==$upid) {
            $result[] = $v;
            if ($v[$leaffield]==0) {
                $sub = tree_build($list, $v[$idfield], $idfield, $upfield);
                if ($sub) {
                    $result = array_merge($result, $sub);
                }
            }
        }
    }
    return $result;
}

function tree_get_leaves(&$list,$upid=0,$idfield='id',$upfield='upid', $leaffield='isleaf' ) {
    $leaves = '';
    if ($upid>0)
        $leaves .= ',' . $upid;
    foreach($list as $k => $v) {
        if ($v[$upfield]==$upid) {
            if ($v[$leaffield]==1)
                $leaves .= ',' . $v[$idfield];
            else {
                $subs = tree_get_leaves($list, $v[$idfield], $idfield, $upfield, $leaffield);
                if ($subs)  
                    $leaves .= ',' . $subs; 
            }
        }
    }
    return substr($leaves,1);
}

function pass_encode($username, $password) {
    $str = $username . '###' & $password;
    $str = md5($str);
    $result = substr($str,16,8) . substr($str,24,8) . substr($str,0,8) . substr($str,8,8);
    return $result;
}

function text_trim($text) {
    $text = str_replace(array(' ','　'), '', $text);
    return $text;
}

function uniqid2() {
	$hash = uniqid('',true);
	$hash = str_replace('.', '', $hash);
	return $hash;
}

function instr($needle, $str) {
    $ret = strpos($str, $needle.'');
    if ($ret !== false)
        return true;
    else
        return false;
}

function msubstr($str, $start=0, $length, $suffix=false, $charset="utf-8")  
{  
    if(function_exists("mb_substr")){  
        if($suffix)  
            return mb_substr($str, $start, $length, $charset)."...";  
        else
            return mb_substr($str, $start, $length, $charset);  
    }  
    elseif(function_exists('iconv_substr')) {  
        if($suffix)  
            return iconv_substr($str,$start,$length,$charset)."...";  
        else
            return iconv_substr($str,$start,$length,$charset);  
    }  
    $re['utf-8']   = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef]
    [x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";  
    $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";  
    $re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";  
    $re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";  
    preg_match_all($re[$charset], $str, $match);  
    $slice = join("",array_slice($match[0], $start, $length));  
    if($suffix) return $slice."…";
    return $slice;
}

function str_fixcn($str) {
    $find = array('-','，','　');
    $replace = array('-',',',' ');
    return str_replace($find, $replace, $str);
}

function str_fixnl($str) {
    return str_replace(array("\r\n","\r"),"\n", $str);
}

function str_padzero($str, $len) {
    return str_pad($str, $len, '0', STR_PAD_LEFT);
}

function ext_implode($glue, $pieces) {
    return empty($pieces) ? '' : $glue.implode($glue, $pieces).$glue;
}

function num2cn($str) {
    $arr = array('零','一','二','三','四','五','六','七','八','九');
    $len = strlen($str);
    $result = '';
    for($i = 0; $i < $len; $i ++) {
        $char = $str[$i];
        if (is_numeric($char))
            $result .= $arr[$char];
    }
    return $result;
}
/**
* 检测是否有敏感词
 * @param $str
 * @return string
**/
function sensor_test($str) {
    $badwords = C('SECURITY_DENYWORDS');
    $arr = explode(',', $badwords);
    foreach($arr as $v) {
        if (strpos($str, $v)!==FALSE)
            return false;
    }
    return true;
}

/**
 * 过滤敏感词，默认用星号替换
 * @param $str
 * @param string $with
 * @return mixed
 */
function sensor_replace($str, $with='*') {
    $badwords = C('SECURITY_DENYWORDS');
    $search = explode(',', $badwords);
    $replace = array();
    foreach($search as $v) {
        $replace[] = str_repeat($with, strlen($v));
    }
    return str_replace($search, $replace, $str);
}

/**
 * 判断是否手机浏览
 * @return bool
 */
function is_mobile() {
    $mobile = array();
    static $mobilebrowser_list ='Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
    //note 获取手机浏览器
    if(preg_match("/$mobilebrowser_list/i", $_SERVER['HTTP_USER_AGENT'], $mobile)) {
        return true;
    }else{
        if(preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }else{
            if($_GET['mobile'] === 'yes') {
                return true;
            }else{
                return false;
            }
        }
    }
}

/**
 * 获取当前日期字符串
 * @return int
 */
function today() {
    return strtotime(date('Y-m-d'), time());
}

function fix_http_get(){
    if(!function_exists('mb_detect_encoding')) 
        return;
    $utfAlias = array('UTF-8','CP936');
    foreach($_GET as $k=>$v) {
        $mb = mb_detect_encoding($v,array('GB2312','GBK','UTF-8'));
        if ($mb=='UCS-CN')
            $mb = 'GBK';
        if (!in_array($mb, $utfAlias)) {
            $_GET[$k] = mb_convert_encoding($v, 'UTF-8', $mb);
        }
    }
}

/**
 * html代码转换成普通字符串
 * @param $html
 * @return string
 */
function html2text($html) {
    $text = strip_tags($html);
    $text = preg_replace('/&[a-zA-Z0-9]+?;/i', '', $text);
    $text = preg_replace('/\s*/i', '', $text);
    return $text;
}

/**
 * 获取当前url
 * @return string
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * 处理多选项字符串
 * @param $str
 * @param string $chr
 * @return string
 */
function str_forsearch($str, $chr=' ') {
    if (empty($str))
        return '';
    $str = trim(str_fixcn($str), $chr);
    //多个相同字符
    $str = preg_replace("/$chr{2,}/i",$chr, $str);
    return $chr. $str . $chr;
}


/**
 * 获取缩略图路径
 * @param $img
 * @param $width
 * @param $height
 * @return mixed
 */
function img2thumb($img, $width, $height) {
    $ext = strrchr($img, '.');
    $thumb = str_replace(array('image', $ext), array('thumb', "-$width-$height$ext"), $img);
    return $thumb;
}

function img2water($img, $width, $height) {
    $ext = strrchr($img, '.');
    $dst = str_replace('image', 'water', $img);
    return $dst;
}
/**
 * 缩略图路径获取原始图片路径
 * @param $thumb
 * @return string
 */
function thumb2img($thumb) {
    $match = array();
    preg_match('#-\d+-\d+#', $thumb, $match);
    $size = $match[0];
    $img = strtr($thumb, array($size => ''));
    $img = strtr($img, array('thumb'=> 'image'));
    return $img;
}

function parse_range($range) {
    $condition = null;
    if (empty($range[1]))
        $condition = array('gt', $range[0]);
    else {
        if ($range[0] == $range[1]) {
            $condition = array('eq', $range[0]);
        } else {
            $condition = array(
                array('gt', $range[0]),
                array('elt', $range[1])
            );
        }
    }
    return $condition;
}

function mlt2map($mlt, $key='id', $val='title') {
    if (empty($mlt))
        return null;
    $new = array();
    foreach ($mlt as $k => $v) {
        $new[$v[$key]] = $v[$val];
    }
    return $new;
}
function mlt2lst($mlt, $val='id', $text='title') {
    if (empty($mlt))
        return null;
    $new = array();
    foreach ($mlt as $k => $v) {
        $new[] = array('value'=>$v[$val], 'text'=>$v[$text]);
    }
    return $new;
}
//给图片地址加上域名
function fix_imgurl($url) {
    $host = 'http://'.$_SERVER['HTTP_HOST'];
    if (!empty($url) && strpos($url,'http:')===FALSE)
        $url = $host . $url;
    return $url;
}

function fix_html($htm) {
    $host = 'http://'.$_SERVER['HTTP_HOST'];
    $search = array('/upload/');
    $replace = array($host.'/upload/');
    return str_replace($search, $replace, $htm);
}
//实例化服务
function service($name) {
    return D($name, 'Service');
}
//验证
function valid_mobile($mobile) {
    //$reg = '/^1[345678][0-9]{9}$/i';
    $reg = '/^\+?\d{4,}$/i';
    if (preg_match($reg, $mobile))
        return true;
    return false;
}
//email
function valid_email($email) {
    if (preg_match('/^([a-zA-Z0-9_-\.])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ i', $email))
        return true;
    return false;
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : C('APP_KEY'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

function timespan($time) {
    $span = time() - $time;
    if ($span>3600*24) {
        $num = floor($span/3600/24);
        $str = L('timespan_day', array('num'=>$num));
    } elseif ($span>3600) {
        $num = floor($span/3600);
        $str = L('timespan_hour', array('num'=>$num));
    } elseif ($span>60) {
        $num = floor($span/3600);
        $str = L('timespan_minute', array('num'=>$num));
    } else {
        $str = L('timespan_just');
    }
    return $str;
}

function num_short($number) {
    if ($number < 100)
        return $number;
    else
        return '99+';
}

function fix_link($link) {
    if (strtolower(substr($link, 0, 4)) !== 'http')
        $link = 'http://' . $link;
    return $link;
}

function autolink($str='') {
    if($str=='' or !preg_match('/(http|www\.|@)/i', $str)) { return $str; }
    $lines = explode("\n", $str); $new_text = '';
    while (list($k,$l) = each($lines)) {
        $l = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $l);
        $l = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $l);
        $l = preg_replace("/(http:\/\/[^ )!]+)/i", "<a href=\"\\1\">\\1</a>", $l);
        $l = preg_replace("/(https:\/\/[^ )!]+)/i", "<a href=\"\\1\">\\1</a>", $l);
        $l = preg_replace("/(ftp:\/\/[^ )!]+)/i", "<a href=\"\\1\">\\1</a>", $l);
        $l = preg_replace("/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i", "<a href=\"mailto:\\1\">\\1</a>", $l);
        $new_text .= $l."\n";
    }
    return $new_text;
}
//是否微信中
function is_weixin()
{
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    return false;
}

function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

function filter_html($str) {
    $str=preg_replace("/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i", " ", $str); //过滤img标签

    $str=preg_replace("/\s+/", " ", $str); //过滤多余回车

    $str=preg_replace("/<[ ]+/si","<",$str); //过滤<__("<"号后面带空格)

    $str=preg_replace("/<\!--.*?-->/si","",$str); //注释

    $str=preg_replace("/<(\!.*?)>/si","",$str); //过滤DOCTYPE

    $str=preg_replace("/<(\/?html.*?)>/si","",$str); //过滤html标签

    $str=preg_replace("/<(\/?head.*?)>/si","",$str); //过滤head标签

    $str=preg_replace("/<(\/?meta.*?)>/si","",$str); //过滤meta标签

    $str=preg_replace("/<(\/?body.*?)>/si","",$str); //过滤body标签

    $str=preg_replace("/<(\/?link.*?)>/si","",$str); //过滤link标签

    $str=preg_replace("/<(\/?form.*?)>/si","",$str); //过滤form标签

    $str=preg_replace("/cookie/si","COOKIE",$str); //过滤COOKIE标签

    $str=preg_replace("/<(applet.*?)>(.*?)<(\/applet.*?)>/si","",$str); //过滤applet标签

    $str=preg_replace("/<(\/?applet.*?)>/si","",$str); //过滤applet标签

    $str=preg_replace("/<(style.*?)>(.*?)<(\/style.*?)>/si","",$str); //过滤style标签

    $str=preg_replace("/<(\/?style.*?)>/si","",$str); //过滤style标签

    $str=preg_replace("/<(title.*?)>(.*?)<(\/title.*?)>/si","",$str); //过滤title标签

    $str=preg_replace("/<(\/?title.*?)>/si","",$str); //过滤title标签

    $str=preg_replace("/<(object.*?)>(.*?)<(\/object.*?)>/si","",$str); //过滤object标签

    $str=preg_replace("/<(\/?objec.*?)>/si","",$str); //过滤object标签

    $str=preg_replace("/<(noframes.*?)>(.*?)<(\/noframes.*?)>/si","",$str); //过滤noframes标签

    $str=preg_replace("/<(\/?noframes.*?)>/si","",$str); //过滤noframes标签

    $str=preg_replace("/<(i?frame.*?)>(.*?)<(\/i?frame.*?)>/si","",$str); //过滤frame标签

    $str=preg_replace("/<(\/?i?frame.*?)>/si","",$str); //过滤frame标签

    $str=preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si","",$str); //过滤script标签

    $str=preg_replace("/<(\/?script.*?)>/si","",$str); //过滤script标签

    $str=preg_replace("/javascript/si","Javascript",$str); //过滤script标签

    $str=preg_replace("/vbscript/si","Vbscript",$str); //过滤script标签

    $str=preg_replace("/on([a-z]+)\s*=/si","On\\1=",$str); //过滤script标签

    $str=preg_replace("/&#/si","&＃",$str); //过滤script标签

    return $str;
}