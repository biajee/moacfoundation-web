/**
 *  快速上传插件
 *  @author WangXianFeng
 *  @support  365102010@qq.com
 */
(function ($) {
    'use strict';

    // Prevent to read twice
    if ($.fastUploadSettings) {
        return;
    }

    $.fastUploadSettings = {
        onprogress: function (e) {
            console.log('progress');
        },
        onabort: function (e) {
            console.log('abort');
        },
        onerror: function (e) {
            console.log('error');
            console.log(e);
        },
        beforeSend: function() {
            console.log('beforeSend');
        },
        success: function (e) {
            console.log('success');
        },
		beforeUpload: function(e) {
			console.log('beforeUpload');
		},
        beforeCompress: function(e) {
            console.log('beforeCompress');
        },
        afterCompress: function(e) {
            console.log('afterCompress');
        },
        afterSelect: function(e) {
            console.log('select file:' + e.name);
        },
		multiple: false,
		maxnum: 9,
        maxsize: 150 * 1024,
		field: '',
        name: 'file',
        param: {},
        width: 720,
        quality: 0.8
    };

    $.fastUploader = {
        upload: function (settings) {
            var url = settings.url;
            var param = settings.param;
            var setting = {
                url: url,
                type: 'post',
                data: param,
                beforeSend: settings.beforeSend,
                error: settings.error,
                success: settings.success
            };
            return $.ajax(url, setting);
        },
        compress: function (file, settings, callback) {
            var that = this;
            var img = new Image();
            var results = {};
            var reader = new FileReader();

            reader.onload = function(e) {
                settings.beforeCompress(e.target.result);
                img.src = e.target.result;
            };
            img.onload = function () {
                // 获得图片缩放尺寸
                var w = settings.width,
                    h = settings.height,
                    scale = img.width / img.height,
                    resize = {w: img.width, h: img.height};
                if (file.size > settings.maxsize) {
                    if (w & h) {
                        resize.w = w;
                        resize.h = h;
                    }
                    else if (w) {
                        resize.w = w;
                        resize.h = Math.ceil(w / scale);
                    }

                    else if (h) {
                        resize.w = Math.ceil(h * scale);
                        resize.h = h;
                    }
                }


                // 初始化canva
                var canvas = document.createElement('canvas'), ctx;
                canvas.width = resize.w;
                canvas.height = resize.h;
                ctx = canvas.getContext('2d');

                // 调整正确的拍摄方向
                var mpImg = new MegaPixImage(img);
                EXIF.getData(img, function () {
                    mpImg.render(canvas, {
                        width: canvas.width,
                        height: canvas.height,
                        orientation: EXIF.getTag(this, "Orientation")
                    });

                    // 设置白色背景
                    ctx.fillStyle = '#fff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    // 生成结果
                    //results.blob = blob;
                    results.origin = file;

                    ctx.drawImage(img, 0, 0, resize.w, resize.h);

                    // 兼容 Android
                    var userAgent = navigator.userAgent;
                    if (/Android/i.test(userAgent)) {
                        try {
                            var encoder = new JPEGEncoder();
                            results.base64 = encoder.encode(ctx.getImageData(0, 0, canvas.width, canvas.height), settings.quality * 100);
                        } catch (_error) {
                            alert('未引用mobile补丁，无法生成图片。');
                        }
                    }

                    // 其他情况&IOS
                    else {
                        results.base64 = canvas.toDataURL('image/jpeg', settings.quality);
                    }
                    settings.afterCompress(results);
                    // 执行回调
                    callback(results);
                });
            };

            reader.readAsDataURL(file);
        },

    };

    $.fn.fastUpload = function (settings) {
        var settings = $.extend(true, {}, $.fastUploadSettings, settings)
		if (settings.field == '') {
			settings.field = settings.name;
		}
        return this.each(function () {
            var that = this;
            $(this).change(function () {
				var selEvent = {files: this.files, cancel: false};
				var canUpload = settings.afterSelect(selEvent);
				if (selEvent.cancel) {
					return;
				}
				var limit = this.files.length;
				var i;
				for (i=0; i<limit; i++) {
					var file = this.files[i];
					var upEvent = {file: file, cancel: false};
					settings.beforeUpload(file);
					if (!upEvent.cancel) {
						var newSettings = $.extend({}, settings);
						$.fastUploader.compress(file, newSettings, function (results) {
							newSettings.param[newSettings.field] = results.base64;
							$.fastUploader.upload(newSettings);
						});
					}	
				}
                
            });
        });
    }
    $.fastUploadPrompt = function(settings) {
        var name = settings.name;
        var id = 'fud-' + name;
        var $file = $('#'+ id);
        if ($file.size() == 0) {
			var extro = '';
			if (settings.multiple) {
				extro = ' multiple="multiple"';
			}
            $file = $('<input type="file" id="' + id +'" accept="image/*"' + extro + ' />');
            $file.appendTo($(document.body))
            $file.css({
                position: 'relative',
                top: -1000,
                left: -1000,
                opacity: 0
            });
            $file.fastUpload(settings);
        };
        $file.click();
    }
    $.fn.fastUploadPrompt = function(settings){
        return this.each(function(){
            $(this).click(function(){
                $.fastUploadPrompt(settings);
            });

        });

    }

})(jQuery);
