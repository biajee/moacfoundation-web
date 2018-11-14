<?php
return array(
    'cn' => array(
            //通知
            'mail_trade_title' => '[海外帮] 交易提醒',

            'mail_trade_new' => '<a href="{$url}" target="_blank">{$othername} 向您发起交易，请到 我的交易 页查看</a>',
            'mail_trade_accept' => '<a href="{$url}" target="_blank">交易【{$tradeno}】 {$title}：对方已确认交易请求，请到交易详情页查看</a>',
            'mail_trade_refuse' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：对方拒绝交易，请到交易详情页查看</a>',
            'mail_trade_pay' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：需求方已支付，请到交易详情页查看</a>',
            'mail_trade_start' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：服务方开始服务，请到交易详情页查看</a>',
            'mail_trade_finish' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：服务方完成服务，请到交易详情页查看</a>',
            'mail_trade_inspect' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：需求方已验收，请到交易详情页查看</a>',
            'mail_trade_complate' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：交易完成，系统已将款项转入您的账户，请到交易详情页查看</a>',
            'mail_trade_not_review' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：还未评价，请到交易详情页查看</a>',
            //'trade_status_changed' => '您的交易 {$title} 有新的变化，请到 我的交易 页查看',
            //短信通知
            'sms_trade_new' => '【海外邦】亲爱的{$name}, 您有一个订单，请登录海外邦网站查看吧 www.hiwibang.com.',
            'sms_trade_accept' => '【海外邦】亲爱的{$name}, 您的订单{$tradeno}，服务方已经确认，请将约定款项托管到海外邦平台。 www.hiwibang.com.',
            'sms_trade_pay' => '【海外邦】亲爱的{$name}, 您的订单{$tradeno}，需求方已经将约定款项托管，请开始服务。海外邦 www.hiwibang.com.',
            'sms_trade_finish' => '【海外邦】亲爱的{$name}, 您的订单{$tradeno}，服务已经完成，请尽快确认。海外邦 www.hiwibang.com.',
            'sms_trade_complate' => '【海外邦】亲爱的{$name}, 您的交易已完成，托管款已经付给服务方，请互相评价一下吧。海外邦www.hiwibang.com.',
            //系统消息
            'chat_register_welcome' => '欢迎注册海外邦会员',
            'chat_follow_new' => '{$name} 关注了您，<a href="{$url}">查看</a>',
            'chat_comment_new' => '{$name} 给您留言，<a href="{$url}">查看</a>',
            'chat_trade_new' => '<a href="{$url}" target="_blank">{$othername} 向您发起交易，请到 我的交易 页查看</a>',
            'chat_trade_accept' => '<a href="{$url}" target="_blank">交易【{$tradeno}】 {$title}：对方已确认交易请求，请到交易详情页查看</a>',
            'chat_trade_refuse' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：对方拒绝交易，请到交易详情页查看</a>',
            'chat_trade_pay' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：需求方已支付，请到交易详情页查看</a>',
            'chat_trade_start' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：服务方开始服务，请到交易详情页查看</a>',
            'chat_trade_finish' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：服务方完成服务，请到交易详情页查看</a>',
            'chat_trade_inspect' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：需求方已验收，请到交易详情页查看</a>',
            'chat_trade_complate' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：交易完成，系统已将款项转入您的账户，请到交易详情页查看</a>',
            'chat_trade_not_review' => '<a href="{$url}" target="_blank">交易 【{$tradeno}】{$title}：还未评价，请到交易详情页查看</a>',
        ),
    'en' => array(
        //短信通知
        'sms_trade_new' => '【HiWiBANG】Dear {$name}, you have an order, please login HiWiBANG to check.',
        'sms_trade_accept' => '【HiWiBANG】Dear {$name}, please deposit your agreed payment to HiWiBANG',
        'sms_trade_pay' => '【HiWiBANG】Dear {$name}, please start your service, www.hiwibang.com',
        'sms_trade_finish' => '【HiWiBANG】Dear {$name}, please confirm your service. www.hiwibang.com',
        'sms_trade_complate' => '【HiWiBANG】Dear {$name},  transaction has completed, please make evaluation.',
    )

);