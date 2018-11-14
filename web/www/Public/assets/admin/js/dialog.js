/* global $ */
var basePage = {
    resizing: 0,
    showDialog: function (url, options) {
        var settings = { type: 'iframe', content: url, modal: true };
        if (options != null) {
            settings.width = options.width;
            settings.height = options.height;
        }
        $.freebox(settings);
    },
    init: function () {
        var myThis = this;
        $('.close').click(function(){
            parent.$.freebox.close();
        });
        $('.ok').click(function(){
            parent.location.reload();
        });
        $('.back').click(function(){
            window.history.back();
        });
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
        //对话框
        $('[data-rel="dialog"]').click(function (event) {
            event.preventDefault();
            event.stopPropagation();
            var url = this.href;
            var options = $(this).data('options');
            myThis.showDialog(url,options);
        });
        this.onInit();
        $(window).resize(function () {
            myThis.resizing++;
            myThis.layout();
        });
        setTimeout(function () {
            myThis.layout();
        }, 50);
    },
    layout: function () {
        var $win = $(window);
        var winHeight = $win.height();
        var $box = $('.freebox');
        var headHeight = $('.freebox-head').height();
        var footHeight = $('.freebox-foot').outerHeight();
        var $boxBody = $('.freebox-body');
        var boxHeight = $boxBody.height();
        //if (boxHeight > winHeight) {
        $box.css({ overflow: 'hidden', height: winHeight });
        $boxBody.css({ overflow: 'auto', height: winHeight - headHeight - footHeight - 22 });
        //}
        this.onLayout();
    },
    isEmpty: function (data) {
        return typeof(data) == 'undefined' || data == null || data == 'null' || data == '';
    },
    sscrollToBottom: function () {
        var $body = $(document.body);
        $body.scrollTop($body.scrollHeight());
    },
    onInit: function () { },
    onLayout: function () { },
    error: function(message) {
        alert(message);
    }
};

$(document).ready(function(){
    basePage.init();
});
