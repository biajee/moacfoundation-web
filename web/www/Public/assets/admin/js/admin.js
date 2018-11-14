var basePage = {
    remote: function ($url) {
        return $.ajax({
            url: $url,
            sync: false
        }).responseText;
    },
    layout: function () {
        var headH = $('.mainbox-head').height();
        var bodyH = $(window).height() - headH;
        $('.mainbox-body').css({ height: bodyH });
        this.onLayout();
    },
    init: function () {
        var myThis = this;
        $(document.body).on('click', '[data-rel="delete"]', function () {
			var text = $(this).text();
            return confirm('此操作不可恢复!\n\n真的要执行【' + text + '】操作吗？');
        });
        $('[data-rel="selectall"]').on('ifChecked', function () {
            $('[name="id[]"]').iCheck("check");
        });
        $('[data-rel="selectall"]').on('ifUnchecked', function () {
            $('[name="id[]"]').iCheck("uncheck");
        });
        //对话框
        $(document.body).on('click','[data-rel="dialog"]',function (event) {
            var $this = $(this);
            var href = $this.attr('href');
            if (href.indexOf('javascript')<0)
                event.preventDefault();
            var url = this.href;
            var settings = { type: 'iframe', content: url, modal: true};
            var options = $(this).data('options');
            if (options != null) {
                settings.width = options.width;
                settings.height = options.height;
            }

            $.freebox(settings);
        });
        $('.listtable tbody tr:even').addClass('even');
        //美化单选框和多选框
        $('input').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue',
            increaseArea: '20%' // optional
        });
        //美化下拉框
        $('select').iSelect();
        //初始化验证控件
        $.Tipmsg.r = '';
        this.onInit();
        //调整布局
        var $win = $(window);
        $win.resize(function () {
            myThis.layout();
        });
        myThis.layout();
    },
    success: function(message) {
        alert(message);
    },
    error: function (message) {
        alert(message);
    },
    onInit: function () { },
    onLayout: function() { },
    districtList: null,
    realmList: null,
    fillCountry: function(obj, country) {
        var districtList = this.districtList;
        var htm = '<option value="">选择国家/地区</option>';
        if (districtList != null) {
            var item;
            for(var k in districtList) {
                item = districtList[k];
                if (item.upid == '0') {
                    htm += '<option value="' + item.id + '"';
                    if (country == item.id + '') {
                        htm += ' selected';
                    }
                    htm += '>' + item.title + '</option>'
                }
            }
            $(obj).html(htm);
            $(obj).iSelect();
        }
    },
    fillCity: function(obj, country, city) {
        var htm = '<option value="">选择城市</option>';
        var districtList = this.districtList;
        if (districtList != null) {
            var item;
            for(var k in districtList) {
                item = districtList[k];
                if (item.upid == country) {
                    htm += '<option value="' + item.id + '"';
                    if (city == item.id + '') {
                        htm += ' selected';
                    }
                    htm += '>' + item.title + '</option>'
                }
            }
            $(obj).html(htm);
            $(obj).iSelect();
        }
    },
    fillRealm: function(obj, realm) {
        var realmList = this.realmList;
        var htm = '<option value="">选择领域</option>';
        if (realmList != null) {
            var item;
            for(var k in realmList) {
                item = realmList[k];
                if (item.upid == '0') {
                    htm += '<option value="' + item.id + '"';
                    if (realm == item.id + '') {
                        htm += ' selected';
                    }
                    htm += '>' + item.title + '</option>'
                }
            }
            $(obj).html(htm);
            $(obj).iSelect();
        }
    },
    fillRealm2: function(obj, realm, realm2) {
        var realmList = this.realmList;
        var htm = '<option value="">选择领域细分</option>';
        if (realmList != null) {
            var item;
            for(var k in realmList) {
                item = realmList[k];
                if (item.upid == realm) {
                    htm += '<option value="' + item.id + '"';
                    if (realm2 == item.id + '') {
                        htm += ' selected';
                    }
                    htm += '>' + item.title + '</option>'
                }
            }
            $(obj).html(htm);
            $(obj).iSelect();
        }
    },
    isEmpty: function (data) {
        return typeof(data) == 'undefined' || data == null || data == 'null' || data == '';
    },
    scrollToBottom: function() {
        var top = $(document.body).height() - $(window).height()
        console.log(top);
        $(window).scrollTop(top);
    },
};
$(function(){
       basePage.init();
 });
