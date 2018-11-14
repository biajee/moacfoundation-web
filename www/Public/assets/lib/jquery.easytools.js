/**
 * jquery.easytools.js
 * author WangXianFeng
 * copyright 天津三三互联信息科技有限公司
 * support QQ 365102010
 */
 
//placeholder plugin
(function ($) {
    $.fn.placeholder = function () {
        return this.each(function () {
            var obj = $(this);
            var text = obj.data('placeholder');
            obj.val(text);
            obj.addClass('placeholder');
            obj.focus(function () {
                if (obj.val() == text) {
                    obj.val('');
                    obj.removeClass('placeholder');
                }
            });
            obj.blur(function () {
                if (obj.val() == '') {
                    obj.val(text);
                    obj.addClass('placeholder');
                }
            });
        });
    }
})(jQuery);
//tooltip plugin
(function ($) {
    $.fn.tooltip = function (configs) {
        configs = $.extend({}, $.fn.tooltip.defaults, configs || {});
        var helper = $('#tooltip');
        if (helper.parentsUntil().length == 0) {
            helper = $('<div id="tooltip" style="position:absolute;"></div>').appendTo(document.body).hide();
        }
        return this.each(function () {
            var obj = $(this);
            var t = obj.attr('title');
            var i = obj.find('img.preview');
            if (i.length > 0) {
                t = '<img src="' + i.attr('src') + '" /><p>' + t + '</p>';
            }
            if (t != null) {
                obj.attr('tip', t);
                obj.attr('title', '');
                obj.mouseenter(function (e) {
                    var xpos, ypos;
                    helper.html($(this).attr("tip"));

                    if (e) {
                        xpos = e.pageX;
                        ypos = e.pageY;
                    } else {
                        var pos = obj.position();
                        xpos = window.event.clientX + pos.left;
                        ypos = window.event.clientY + pos.top;
                    }
                    var xcenter = $(window).width() / 2 + $(document).scrollLeft();
                    var ycenter = $(window).height() / 2 + $(document).scrollTop();
                    if (ypos < ycenter) {
                        ypos = ypos + 5;
                    } else {
                        ypos = ypos - helper.outerHeight() - 5;
                    }
                    if (xpos < xcenter) {
                        xpos = xpos + 5;
                    } else {
                        xpos = xpos - helper.outerWidth() - 5;
                    }
                    helper.css("left", xpos);
                    helper.css("top", ypos);

                    helper.show();
                });
                obj.mouseleave(function () {
                    helper.hide();
                });
            }
        });
    }
})(jQuery);
//tab plugin
(function ($) {
    $.fn.tab = function (configs) {
        configs = $.extend({}, $.fn.tab.defaults, configs || {});
        return this.each(function () {
            var $obj = $(this);
            var $tabs = $obj.find(configs.tabs);
            var $sheets = $obj.find(configs.sheets);
			var tabWidth = $tabs.width();
            var sheet0 = $obj.find(configs.sheets).eq(0);
            if (!configs.autohide) {
                $obj.find(configs.tabs).eq(0).addClass(configs.current);
                $obj.find(configs.sheets).hide();
                $obj.find(configs.sheets).eq(0).show();
            }
            if (configs.animate) {
                var sh = sheet0.height();
                $obj.find(configs.sheets).show();
                sheet0.parent().css({ height: sh, overflow: 'hidden' });
            }
            var fnc = function () {
                $this = $(this);
                $this.siblings().removeClass(configs.current);
                $this.addClass(configs.current);

                var index = $obj.find(configs.tabs).index($this);
                if (configs.animate) {
                    var p = $obj.find(configs.sheets).eq(0);
                    var mt = 0;
                    if (index > 0)
                        mt = (p.height() * index) * -1;
                    p.animate({ marginTop: mt }, 'fast');
                } else {
                    $obj.find(configs.sheets).hide();
                    $obj.find(configs.sheets).eq(index).show();
                }
            }
            if (configs.auto == 0) {
                if (configs.event == "click") {
                    $obj.find(configs.tabs).click(fnc);
                } else {
                    $obj.find(configs.tabs).mouseover(fnc);
                }
                if (configs.autohide) {
                    $obj.mouseleave(function () {
                        $obj.find(configs.tabs).removeClass(configs.current);
                        $obj.find(configs.sheets).hide();
                    });
                }
            }
            var timer = null;
            var index = 0;
            var max = $tabs.size();
			function showSheet() {
				$sheets.hide();;
                $sheets.eq(index).show();
			}
            function onTimer() {
                index ++;
                if (index>=max) {
                    index = 0;
					$tabs.removeClass(configs.current);
					if (configs.tabanimate) {
						var $mask = $obj.find('.tabmask');
						
						$mask.animate({left: (max-index + 1)*tabWidth}, 500, function(){
							$mask.css({left:-1*tabWidth});
							$mask.animate({left:0}, 500, function(){
								$tabs.eq(index).addClass(configs.current);
								showSheet();
							});
						});
					} else {
						$tabs.eq(index).addClass(configs.current);
						showSheet();
					}
				} else {
					if (configs.tabanimate) {
						var left = $tabs.width()*index;
						$tabs.removeClass(configs.current);
						$obj.find('.tabmask').animate({left:left}, 1000, function(){
							$tabs.eq(index).addClass(configs.current);
							showSheet();
						});
					} else {
						$tabs.removeClass(configs.current);
						$tabs.eq(index).addClass(configs.current);
						showSheet();
					}
				}
                
            }
            function startTimer() {
                if (timer == null) {
                    timer = setInterval(onTimer, configs.auto);
                }
            }
            function stopTimer() {
                if (timer != null) {
                    clearInterval(timer);
                    timer = null;
                }
            }
            if (configs.auto>0) {
                $obj.hover(function(){
                    stopTimer();
                },function(){
                    startTimer();
                });
                startTimer();
            }
        });
    }
    $.fn.tab.defaults = { current: "current", event: "over", tabs: ".tabs li", sheets: ".sheet", autohide: false, animate: false, auto:0, tabanimate:false }
})(jQuery);

(function ($) {
    $.fn.rollover = function (suffix) {
        suffix = suffix || '_on';
        return this.not('[src*="' + suffix + '."]').each(function () {
            var img = jQuery(this);
            var src = img.attr('src');
            var _on = [
                src.substr(0, src.lastIndexOf('.')),
                src.substring(src.lastIndexOf('.'))
            ].join(suffix);
            jQuery('<img>').attr('src', _on);
            img.hover(
                function () { img.attr('src', _on); },
                function () { img.attr('src', src); }
                ).click(function () { img.attr('src', src); });
        });
    };
})(jQuery);

(function ($) {
    $.fn.imgState = function (state) {
        state = state || "on";
        return this.each(function () {
            var tagName = $(this).get(0).tagName;
            if (tagName == "IMG") {
                if (state == "on") {
                    var pass = $(this).attr("src");
                    pass = pass.replace(/(\.[a-z]{3})/g, "_o$1");
                    $(this).attr("src", pass);
                } else {
                    var pass = $(this).attr("src");
                    pass = pass.replace(/_o(\.[a-z]{3})/g, "$1");
                    $(this).attr("src", pass);
                }
            }
        });
    };
})(jQuery);

(function ($) {
    $.fn.blockHover = function () {
        return this.each(function () {
            $(this).find("img:eq(0)").unbindHover();
            $(this).hover(
                function () {
                    $(this).find("img:eq(0)").imgState("on");
                },
                function () {
                    $(this).find("img:eq(0)").imgState("off");
                }
                );
        });
    }
})(jQuery);

(function ($) {
    $.fn.floatTo = function (configs) {
        var configs = $.extend({}, $.fn.floatTo.defaults, configs || {});
        return this.each(function () {
            var $obj = $(this);
            var timer = null;
            var lastPos = 0;
            var isShow = false;
            var fadeTo = $obj.css('opacity');
            var setPosition = function () {
				
                var win = $(window);
                var $bar = $obj;
                var left = 0;
                var top = 0;
                var ww = win.width();
                var wh = win.height();
                var sl = win.scrollLeft();
                var st = win.scrollTop();
                var bw = $bar.width();
                var bh = $bar.height();
                if (configs.autohide != null && configs.refer != null) { //启用自动隐藏
					var $refer = $(configs.refer);
					var pos = $refer.offset();
                    var limit = {min: pos.top - wh, max:pos.top + wh}; //视野范围
					var canShow = false;
					switch (configs.autohide) {
						case 'in':
							if (st >= limit.min)
								canShow = true;
							else
								canShow = false;
							break;
						case 'out':
							if ( st >= pos.top + bh)
								canShow = true;
							else
								canShow = false;
							
					}
					
					if (canShow) { //可以显示
						if (!isShow) {
							$obj.show();
							//$obj.css({opacity:fadeTo});
							$obj.animate({opacity: fadeTo}, configs.showSpeed);
							isShow = true;
						}
					} else { //不能显示
						if (isShow) {
							$obj.css({opacity:fadeTo});
							$obj.animate({opacity:0}, configs.showSpeed);
							$obj.hide();
							isShow = false;
						} else {
							return;
						}
					} 
					
                } else {
					$obj.show();
				}
                //位置
                switch (configs.position) {
                    case 'left':
                        left = sl + configs.marginLeft;
                        top = st + (wh - bh) / 2 + configs.marginTop;
                        break;
                    case 'right':
                        left = sl + ww - bw - 2 - configs.marginRight;
                        top = st + (wh - bh) / 2 + configs.marginTop;
                        break;
                    case 'bottom':
                        left = 0;
                        top = st + wh - bh;
                        break;
                    case 'bottomright':
                        left = sl + ww - bw - 2 - configs.marginRight;
                        top = st + wh - bh - 2 - configs.marginBottom;
                        break;
                }

                if (configs.animate == 0) {
                    $obj.css({ left: left, top: top });
                } else {
                    if (Math.abs(st - lastPos)> configs.animate) {
                        //$obj.stop();
                        $obj.animate({ left: left, top: top },  configs.speed);
                        lastPos = st;
                    }

                }
            }
            setPosition();
            //处理滚动事件
            $(window).scroll(function () {
                setPosition();
            });
            //处理窗口大小改变事件
            $(window).resize(function () {
                setPosition();
            });
        });
    };
    $.fn.floatTo.defaults = { position: 'right', marginTop: 0, marginBottom: 0, marginLeft: 0, marginRight: 0, showSpeed:500, animate: 0, autohide:'in', refer:null, speed:100, fadeTo:1 };
})(jQuery);
//弹出框
(function ($) {
    var F = $.freebox = function () {
        return F.open.apply(this, arguments);
    };
    $.extend(F, {
        defaults: { type: '', content: '', modal: false, position: 'center', left: 0, top: 0, width: 'auto', height: 'auto', scrolling:'auto', loading: '正在加载...', afterClose: null },
        settings: {},
        overlay: null,
        helper: null,
        loaded: false,
        open: function (opts) {
            var myThis = this;
            F.settings = $.extend({}, F.defaults, opts);
            if (F.settings.modal) {
                if (F.overlay == null)
                    F.overlay = $('<div id="freebox-overlay" style="position:absolute;z-index:99998;left:0;top:0;width:100%;height:100%;background:#000;filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=10);opacity:0.1;"></div>').appendTo('body').hide();
                F.overlay.click(function (event) {
                    //event.stopPropagation();
                    F.close();
                });
                F.overlay.show();
            }
            if (F.helper == null) {
                F.helper = $('<div id="freebox-helper" style="position:absolute;left:0;top:0;width:auto;height:auto;z-index:99999;"><div class="freebox-loading" style="display:none;"></div></div>').appendTo(document.body).hide();
            }
            F.helper.css({ width: F.settings.width, height: F.settings.height });
            //初始化内容
            var data = "";
            var url = "";
            switch (F.settings.type) {
                case 'inline':
                    data = $(F.settings.content).html();
                    F.content(data);
                    F.display();
                    break;
                case 'iframe':
                    url = F.settings.content;
                    if (url.indexOf('?') == -1)
                        url += '?qmd=iframe';
                    else
                        url += '&qmd=iframe';
                    F.content('<iframe width="100%" height="100%" scrolling="' + F.settings.scrolling + '" frameborder="0" src="' + url +'"></iframe>');
                    var $frame = F.helper.find('iframe');
                    $frame.load(function () {
                        F.hideLoading();
                        F.display();
                    });
                    F.showLoading();
                    break;
                case 'ajax':
                    url = F.settings.content;
                    if (url.indexOf('?') == -1)
                        url += '?qmd=ajax';
                    else
                        url += '&qmd=ajax';
                    $.get(F.settings.content, function (s) {
                        F.hideLoading();
                        F.content(s);
                        F.display();
                    });
                    F.showLoading();
                    break;
                default:
                    F.content(F.settings.content);
                    F.display();
                    break;
            }
        },
        content: function (data) {
            var helper = F.helper;
            helper.html(data);
            helper.find('.close').click(function () { F.close(); });
        },
        display: function () {
            F.helper.show();
            F.resize();
            F.helper.fadeIn();
        },
        resize: function () {
            var helper = F.helper;
            var $win = $(window);
            var winWidth = $win.width();
            var winHeight = $win.height();
            var sl = $win.scrollLeft();
            var st = $win.scrollTop();
            var width = helper.width();
            var height = helper.height();
            var contentWindow = null;
            var $frame = null;
            if (F.settings.type == 'iframe') { 
                $frame = F.helper.find('iframe');
                var $contents = $frame.contents();
                //var $body = $contents.find('body');
                contentWindow = $frame.get(0).contentWindow;
                var frameDocument = contentWindow.document;
                var $frameDocument = $(frameDocument);
                var frameWidth = $frameDocument.width();
                var frameHeight = $frameDocument.height();
                if (F.settings.width=='auto') {
					if (frameWidth < winWidth)
						width = frameWidth;
					else
						width = winWidth - 20;
				}
                    
                if (F.settings.height=='auto') {
					if (frameHeight < winHeight)
						height = frameHeight;
					else
						height = winHeight - 20;
				}
                    
            }
            if (width > winWidth) width = winWidth;
            if (height > winHeight) height = winHeight;
            var left = 0;
            var top = 0;
            var position = F.settings.position;
            switch (position) {
                case 'center':
                    left = (winWidth - width) / 2;
                    top = (winHeight - height) / 2;
                    if (top > 100) top = 100;
                    left += sl;
                    top += st;
                    break;
                case 'fixed':
                    left = F.settings.left + sl;
                    top = F.settings.top + st;
                    break;
                case 'absolute':
                    left = F.settings.left;
                    top = F.settings.top;
                    break;
            }
            
            helper.css({ left: left, top: top, width: width, height: height });
            $frame.attr('scrolling', 'no');
        },
        showLoading: function () {
            var loading = F.helper.find('.freebox-loading');
            loading.html(F.settings.loading);
            loading.show();
            F.display();
        },
        hideLoading: function () {
            F.helper.find('.freebox-loading').hide();
        },
        close: function () {
            F.helper.fadeOut();
            if (F.overlay != null)
                F.overlay.hide();
            if (F.afterClose != null) {
                F.afterClose.apply(this);
            }
        }
    });
})(jQuery);
/*
* iSelect 
* 自定义select控件
*/
(function ($) {
    $.fn.iSelect = function (configs) {
        var configs = $.extend({}, $.fn.iSelect.defaults, configs || {});
        return this.each(function (index, element) {
            var myThis = this;
            var $this = $(this);
            
            var elId = $this.attr('name');
            if (elId == '' || typeof (elId) == 'undefined') {
                elId = 's00' + index;
                $this.attr('id', elId);
            }
            var $wrap = $('#iselect-' + elId);
            if ($wrap.length <= 0) {
                $wrap = $('<span class="iselect" id="iselect-' + elId + '"><div class="old"></div><div class="text"></div><div class="dropdown"><ul></ul></div></span>');
                $this.before($wrap);
                $this.prependTo($wrap.find('.old'));
            }
			var $text = $wrap.find('.text');
            var $dropdown = $wrap.find('.dropdown');
			var width = $this.width();
            var allwidth = configs.width;
            if (allwidth == 'auto') {
                allwidth =  width;
            }
            //$wrap.css({width:allwidth});
            $text.css({width:allwidth});
            $dropdown.css({width:allwidth + 2});
            var $list = $dropdown.find('ul');
            var html = '';
            var i = 0;
            var text = '';
            var value = '';
            var selected = false;
            var style = '';
            for (i = 0; i < myThis.options.length; i++) {
                text = myThis.options[i].text;
                value = myThis.options[i].value;
                selected = myThis.options[i].selected;
                if (selected) {
                    style = ' class="selected"';
                } else
                    style = '';
                html += '<li data-value="' + value + '"' + style + '><a href="javascript:;">' + text + '</a></li>';
            }
            $list.html(html);
            if (myThis.options.length>0) {
                text = myThis.options[myThis.selectedIndex].text;
                $text.html(text);
            }
			
            $dropdown.hide();
            $text.click(function (event) {
                event.stopPropagation();
                $dropdown.show();
            });
            $(document.body).click(function () {
                $dropdown.hide();
            });
            var $items = $list.find('li');
            $items.click(function (event) {
                var selectedIndex = $items.index($(this));
                myThis.options[selectedIndex].selected = true;
				$(myThis).change();
                $text.html(myThis.options[myThis.selectedIndex].text);
                $dropdown.hide();
            });
        });
    };
    $.fn.iSelect.defaults = {width:'auto'};
})(jQuery);
//平滑滚动
(function ($) {
    $.fn.marquee = function (configs) {
        configs = $.extend({}, $.fn.marquee.defaults, configs || {});
        return this.each(function () {
            var $obj = $(this);
            var timer = null;
            $obj.css({overflow:'hidden'});
            var $container = $obj.children().eq(0);
            var $children =  $container.children();
            var size = $children.size();
            var conWidth = $container.width();
            var conHeight = $obj.height();
            var subWidth = $children.first().outerWidth() * size
            var subHeight = $children.first().outerHeight() * size;
            var canScroll = false;
            if (configs.direction == 'left') {
                if (subWidth > conWidth ) {
                    var html = $container.html();
                    html += html;
                    $container.html(html);
                    var width = subWidth * size * 2;
                    $container.css({width:width});
                    canScroll = true;
                }

            } else {
                if (subHeight > conHeight ) {
                    var html = $container.html();
                    html += html;
                    $container.html(html);
                    canScroll = true;
                }
            }

            var onTimer = function() {
                if (configs.direction == 'left') {
                    var scrollLeft = $obj.scrollLeft();
                    var width = $container.width();
                    if (scrollLeft>= subWidth) {
                        $obj.scrollLeft(0);
                    } else {
                        $obj.scrollLeft(scrollLeft + 1);
                    }
                } else {
                    var scrollTop = $obj.scrollTop();
                    var height = $container.height();
                    if (scrollTop >= subHeight) {
                        $obj.scrollTop(0);
                    } else {
                        $obj.scrollTop(scrollTop + 1);
                    }
                }
            }
            var startTimer = function(){
                if (timer == null)
                    timer = setInterval(onTimer, configs.speed);
            }
           var stopTimer = function() {
               if (timer != null) {
                   clearInterval(timer);
                   timer = null;
               }
           }
            if (canScroll) {
                $obj.hover(function(){
                    stopTimer();
                },function(){
                    startTimer();
                });
                startTimer();
            }
        });
    };
    $.fn.marquee.defaults = { direction: 'left',speed:50 };
})(jQuery);

//动画滚动
(function ($) {
    $.fn.scrollTo = function (configs) {
        configs = $.extend({}, $.fn.scrollTo.defaults, configs || {});
        var lastScrollTop = 0;
        var tops = [];
        var $all = $(this);
        var lsstTop = 0;
        function setIndicator() {
            var size = tops.length;
            var scrollTop = $(window).scrollTop();
            var sub = scrollTop - lastScrollTop;
            if (sub<10 && sub>-10)
                return;
            lastScrollTop = scrollTop;
            for(var i = 0; i < size; i++) {
                if (!$all.eq(i).hasClass('current')) {
                    if (scrollTop >= tops[i]) {
                       // console.log(scrollTop + '-' + i + '-' + tops[i] + '-' + tops[i+1]);
                        if ((i == size-1) || (scrollTop < tops[i+1])) {
                            $all.removeClass('current');
                            $all.eq(i).addClass('current');
                        }
                    }
                }

            }
        }
        this.each(function () {
            var $obj = $(this);
            var id = $obj.attr('href');
            var top = 0;
            if (typeof(id) != 'undefined' && id.length>1 && id.substr(0, 1) == '#') {
                id = id.substr(1);
                var offset = $('[name=' + id + ']').offset();
                if (typeof(offset) != 'undefined') {
                    top = offset.top;
                }
            }
            if (configs.listen && top>0) {
                tops.push(top);
            }
            $obj.click(function(event){
                event.preventDefault();
                var id = $obj.attr('href');
                var top = 0;
                if (typeof(id) != 'undefined' && id.length>1 && id.substr(0, 1) == '#') {
                    id = id.substr(1);
                    var offset = $('[name=' + id + ']').offset();
                    if (typeof(offset) != 'undefined') {
                        top = offset.top;
                    }
                }
                $('html,body').animate({scrollTop:top}, configs.speed);
            });
        });
        if (configs.listen) {
            $(window).scroll(function(){
               setIndicator();
            });
        }
        setIndicator();
        return $all;;

    };
    $.fn.scrollTo.defaults = { speed:'fast', listen:false };
})(jQuery);

(function($){
    $.fn.dialog = function(configs) {
        return this.each(function(){
            var $this = $(this);
            var $dialog = $this.data('dialog');
            if ( $.type($dialog)=='undefined' || $dialog.length == 0) {
                var id = 'dialog-' + $.fn.dialog.count;
                $.fn.dialog.count ++;
                $dialog = $('<div class="dialog" id="' + id + '"><a href="javascript:;" class="close"></a><div class="dialog-content"></div></div>');
                $dialog.appendTo($(document.body));
                $dialog.on('click', '.close', function(){
                    $dialog.hide();
                });
                $dialog.find('.dialog-content').html($(this).html());
                $this.data('dialog', $dialog);
            }
            if (configs == 'show') {
                var $win = $(window);
                var scrollTop = $win.scrollTop();
                var scrollLeft = $win.scrollLeft();
                var winWidth = $win.width();
                var winHeight = $win.height();
                var width = $dialog.width();
                var height = $dialog.height();
                var left = scrollLeft + (winWidth - width)/2;
                var top = scrollTop + (winHeight - height)/2;
                $dialog.css({left: left, top: top });
                $dialog.show();
            } else if (configs == 'hide') {
                $dialog.hide();
            }
        });
    }

    $.fn.dialog.count = 0;
})(jQuery);

(function($){
    $.fn.thumbView = function(configs) {
        var upIndex = 999;
        var downIndex = 998;
        configs = $.extend({}, $.fn.thumbView.defaults, configs || {});
        return this.each(function () {
            var item = 0;
            var id = 0;
            var lastId = id;
            var $view = $(this);
            var $flash = $view.find('.flash');
            var $thumb = $view.find('.thumb');
			var $container = $flash.find('.container');
            var $items = $container.children();
            var albumNum = $items.size();
			$
			//调整图片
			if (configs.autoWidth) {
				$items.css({ height:'100%', width:'auto'});
				var conWidth = $container.width();
				$items.each(function(){
					var $item = $(this);
					var width = $item.width();
					var left = (conWidth - width)/2;
					$item.css({left: left});
					$item.load(function(){
						var width = $item.width();
						var left = (conWidth - width)/2;
						$item.css({left: left});
					});
					
				});
			}
			$items.hide();
			$items.css({opacity:0, zIndex:downIndex});
            $items.eq(0).css({opacity:1, zindex: upIndex});
			$items.eq(0).show();
			
            var liWidth = $thumb.find('li').width();
            var liMargin = parseInt($thumb.find('li').css('marginRight'));
            liWidth += liMargin;
            $thumb.find('ul').css({width: liWidth * $thumb.find('li').size()});
            $thumb.find('li').eq(0).addClass('current');
            var visibleItem = Math.floor($thumb.find('.tabs').width() / liWidth);
            var $old, $new;
			var $oldt, $newt;
            var hideItem = 0;
            var moveTo = function () {
                id = item;
				if (lastId == id)
					return;
                $oldt = $thumb.find('li').eq(lastId);
                $newt = $thumb.find('li').eq(id);
                $oldt.removeClass('current');
                $newt.addClass('current');
                $old = $items.eq(lastId);
                $new = $items.eq(id);
                $old.css({zIndex: downIndex});
                $old.animate({opacity: 0}, configs.mainSpeed, function(){$old.hide()});
                $new.css({zIndex: upIndex, opacity: 0});
				$new.show();
                $new.animate({opacity: 1}, configs.mainSpeed);
                $thumb.find('li.current').removeClass('current');
                $thumb.find('li').eq(item).addClass('current');
                if (item > visibleItem + hideItem) {
                    $thumb.find('.tabs').animate({scrollLeft: liWidth * (item - visibleItem)}, configs.thumbSpeed)
                    hideItem++;
                }
                if (item < hideItem) {
                    $thumb.find('.tabs').animate({scrollLeft: liWidth * item}, configs.thumbSpeed)
                    hideItem--;
                }
				lastId = id;
            };
            var prev = function() {
                var len = albumNum;
                lastId = item;
                if (item - 1 >= 0) {
                    item--;
                } else {
                    return;
                }
                moveTo();
            };
            var next = function() {
                var len = albumNum;
                lastId = item;
                if (item + 1 <= len - 1) {
                    item++;

                } else {
                    return;
                }
                moveTo();
            }
            $view.find('.prev').click(function () {
                prev();
            });
            $view.find('.next').click(function () {
                next();
            });
            $thumb.find('li').click(function () {
                lastId = item;
                item = $thumb.find('li').index($(this));
                moveTo();
            });

        });
    }
    $.fn.thumbView.defaults = {mainSpeed:1000, thumbSpeed:500, autoWidth: true};
})(jQuery);

(function($){
    $.fn.lightbox = function(configs) {
        var configs = $.extend({}, $.fn.lightbox.defaults, configs || {});
        var upIndex = 8889;
        var downIndex = 8888;
        return this.each(function(){
            var $this = $(this);
            var $box = $this.data('lightbox');
            var total = 0;
            if ( $.type($box)=='undefined' || $box.length == 0) {
                var id = 'lightbox-' + $.fn.lightbox.count;
                $.fn.lightbox.count ++;
                $box = $('<div class="lightbox" id="' + id + '"><div class="mask"></div><div class="lightbox-bd"></div><div class="lightbox-hd"><a href="javascript:;" class="indicator">0/0</a><a href="javascript:;" class="close" title="关闭">╳</a><a href="javascript:;" class="prev" title="上一个"><</a><a href="javascript:;" class="next" title="下一个">></a></div></div>');
                $box.appendTo($(document.body));
                $box.on('click', '.close', function(){
                    $box.hide();
                });
                $this.data('lightbox', $box);
                var images = '';
                var $orgImgs = $this.find('img');
                total = $orgImgs.size();
                $orgImgs.each(function(){
                    var src = this.src;
                    images += '<img src="' + src + '"/>';
                });
                
                $box.find('.lightbox-bd').html(images);
                $orgImgs.click(function(event){
					event.stopPropagation();
                    var index = $orgImgs.index($(this));
                    showBox();
                    moveTo(index);
                });
                var $images = $box.find('img');
				$box.data('current',$images.size()-1);
                //$images.css({opacity:0});
				$images.hide();
                $box.find('.next').click(function(){
                   var index = $box.data('current');
                    var count = $images.size();
                    if (index+1>=count) {
                        index = 0;
                    } else {
                        index ++;
                    }
                    moveTo(index);
                });
                $box.find('.prev').click(function(){
                    var index = $box.data('current');
                    var count = $images.size();
                    if (index-1 < 0) {
                        index = count -1;
                    } else {
                        index --;
                    }
                    moveTo(index);
                });
                $(window).scroll(function(){
                    var $this = $(this);
                    if ($box.is(':visible')) {
                        $box.css({left:$this.scrollLeft(), top: $this.scrollTop()});
                    }
                });
                $(window).resize(function(){
                    if ($box.is(':visible')) {
                        showBox();
                    }
                });
                $box.find('.indicator').html( '1/' + total );
            }


            var moveTo = function(index) {
                var lastId = $box.data('current');
                var id = index;
                if (total == 1 ) {
                    $box.find('img').show();
                    return;
                }

				if (lastId == index) {
                    return;
                }

                var $old = $box.find('img').eq(lastId);
                var $new = $box.find('img').eq(id);
                $old.css({zIndex: downIndex});
                $old.animate({opacity: 0}, configs.speed, function(){$old.hide();});
				$new.show();
                $new.css({zIndex: upIndex, opacity: 0});
                $new.animate({opacity: 1}, configs.speed);
                $box.find('.indicator').html((index+1) + '/' + total );
                $box.data('current', index);
            }
            var showBox = function() {
                var $win = $(window);
                var scrollTop = $win.scrollTop();
                var scrollLeft = $win.scrollLeft();
                var winWidth = $win.width();
                var winHeight = $win.height();
                var width = $box.width();
                var height = $box.height();
                var left = scrollLeft;
                var top = scrollTop;
				var cMargin = 0;
				var $toolbar =  $box.find('.lightbox-hd');
				var tHeight = $toolbar.height();
                var cWidth = winWidth;
                var cHeight = winHeight - tHeight;
                var $mask = $box.find('.mask');
                $box.show();
                $box.css({left: 0, top: 0, width:winWidth, height:winHeight});
                $box.find('.lightbox-bd').css({position:'relative',width:cWidth, height:cHeight, left: 0, top:cMargin, overflow:'hidden'});
                $box.find('img').each(function(){
                    var $img = $(this);
                    $img.css({height:cHeight, width:'auto'});
                    var orgWidth = $img.width();
                    var orgHeight = $img.height();
                    var imgWidth = cWidth;
                    var imgHeight = imgWidth * orgHeight/orgWidth;
                    $img.css({ position:'absolute', top: (cHeight - imgHeight)/2, left: (cWidth-imgWidth)/2, width: imgWidth, height: imgHeight});
                });
            }
            var hideBox = function() {
                $box.hide();
            }
            if (configs == 'show') {
                showBox();
            } else if (configs == 'hide') {
                hideBox();
            }
        });
    }
	$.fn.lightbox.defaults = { fullscreen:true, speed:1000 };
    $.fn.lightbox.count = 0;
})(jQuery);

(function($){
	$.fn.scrollEvent = function(configs){
		var configs = $.extend({}, $.fn.scrollEvent.defaults, configs || {});
		return this.each(function(){
			$obj = $(this);
			var scrolling = false;
			var lastTop = 0;
			$obj.scroll(function(){
			 lastTop = $obj.scrollTop();
			 if (!scrolling) {
				 $obj.triger('scrollstart',[st]);
			 }
			 scrolling = true;
			});
			var onTimer = function(){
				var st = $obj.scrollTop();
				if (scrolling && lastTop == st) {
					scrolling = false;
					$obj.triger('scrollend',[st]);
				}
			};
			setInterval(onTimer, configs.interval);
		});
		
	 }
	 $.fn.scrollEvent.defaults = { interval:50 };
	 
 })(jQuery);
 
 (function($){
	 $.fn.slider = function(configs) {
		 var configs = $.extend({}, $.fn.slider.defaults, configs || {});
		 return this.each(function(){
			var $obj = $(this);
			var $container = $obj.find('.container');
			var $items = $obj.find('li');
			var liWidth = $items.width();
			var liMargin = parseInt($items.css('marginRight'));
			liWidth += liMargin;
			$obj.find('ul').css({width: liWidth * $obj.find('li').size()});
			$items.eq(0).addClass('current');
			var prev = function() {
                var sl = $container.scrollLeft();
				$container.animate({scrollLeft: sl - liWidth}, configs.speed);
            };
            var next = function() {
                var sl = $container.scrollLeft();
				$container.animate({scrollLeft: sl + liWidth}, configs.speed);
            }
            $obj.find('.prev').click(function () {
                prev();
            });
            $obj.find('.next').click(function () {
                next();
            });
		 });
	 }
	 $.fn.slider.defaults = {speed:500};
 })(jQuery);

(function($){
    $.fn.autoComplate = function(configs) {
        var configs = $.extend({}, $.fn.scrollEvent.defaults, configs || {});
        return this.each(function(){
            var $this = $(this);
            var $box = $('.autocomplate-box');
            if ($box==null || $box.size()==0) {
                $box = $('<div class="autocomplate"><div class="autocomplate-content">' +
                    '</div></div>');
                $box.appendTo(document.body);
            }
            var width = $this.width();
            var height = $this.height();
            var $content = $box.find('.autocomplate-content');
            if (configs.url == '') //内容获取地址
                return;
            var showBox =function() {
                var html = $.trim($content.html());
                if (html == '')
                    return;
                var offset = $this.offset();
                $box.css({left: offset.left, top: offset.top + height, width: width});
                if (!$box.is(':visible'))
                    $box.show();
            }
            var hideBox = function() {
                $content.html('');
                $box.hide();
            }
            $this.keyup(function(){
                var text = $.trim($this.val());
                if (text == '')
                    return;
                var url = configs.url;
                var param = {keyword: text};
                $.get(url, param, function(res){
                    $content.html(res);
                    showBox();
                })
            });
            $this.focus(function(){
                var html = configs.content;
                $content.html(html);
                showBox();
            });
            /*$this.blur(function(){
                hideBox();
            });*/
            $this.click(function(event){
                event.stopPropagation();
            });
            $box.click(function(event){
                event.stopPropagation();
            })
            $(document.body).click(function(){
                setTimeout(hideBox, 200);
            });
        });
    }
    $.fn.autoComplate.defaults = {url:'', content:''};
})(jQuery);