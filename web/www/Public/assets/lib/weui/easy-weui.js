(function($){
    $.verify = function(config) {
        var defaults = $.verify.prototype.defaults = {
            buttonOK: config.buttonOK || '确定',
            buttonCancel: config.buttonCancel || '取消'
        }
        var modal = $.modal({
            text: '<p class="weui-prompt-text">'+(config.text || '')+'</p>' +
            '<input type="text" class="weui_input weui-verify-seccode" id="weui-verify-seccode" value="" placeholder="输入验证码" />' +
            '<img class="weui-verify-img" src="' + (config.imgUrl||'') + '" onclick="this.src += \'?\';" />',
            title: config.title,
            autoClose: false,
            buttons: [
                {
                    text: defaults.buttonCancel,
                    className: "default",
                    onClick: function () {
                        $.closeModal();
                        config.onCancel && config.onCancel.call(modal);
                    }
                }, {
                    text: defaults.buttonOK,
                    className: "primary",
                    onClick: function() {
                        var seccode = $("#weui-verify-seccode").val();
                        if (!config.empty && (seccode === "" || seccode === null)) {
                            modal.find('#weui-verify-seccode').focus()[0].select();
                            return false;
                        }
                        $.closeModal();
                        config.onOK && config.onOK.call(modal, seccode);
                    }
                }]
        }, function () {
            this.find('#weui-verify-seccode').focus()[0].select();
        });

        return modal;
    };
})(jQuery)
