<?php
return array(
    'ArchiveType' => array(
        'title' => '存档栏目',
        'source' => 'table',
        'params' => array(
            'table' => 'arctype',
            'fields'=>'*',
            'orderby'=>'sortno ASC',
            'items' => array(
                'tree'=>array('idfield'=>'id','upfield'=>'upid'),
                'mlt'=>array('keyfield'=>'id'),
                'list'=>array('keyfield'=>'id'),
                'map'=>array('keyfield'=>'id','valfield'=>'title'),
                ),
            ),
        ),
    'ArchiveModel' => array(
        'title' => '存档模型',
        'source' => 'table',
        'params' => array(
            'table' => 'arcmodel',
            'items' => array(
                'map'=>array('keyfield'=>'code','valfield'=>'title'),
                ),
            ),
        ),
    'Setting' => array(
        'title' => '系统设置',
        'source' => 'table',
        'params' => array(
            'table' => 'setting',
            'items' => array(
                'map'=>array('keyfield'=>'var','valfield'=>'val'),
                ),
            ),
        ),
    'AdvertType' => array(
        'title' => '广告分组',
        'source' => 'table',
        'params' => array(
            'table' => 'advtype',
            'items' => array(
                'list'=>array('keyfield'=>'id'),
                'mlt'=>array('keyfield'=>'id'),
                'map'=>array('keyfield'=>'code','valfield'=>'id'),
                ),
            ),
        ),
    
    'BlockType' => array(
        'title' => '版块分组',
        'source' => 'table',
        'params' => array(
            'table' => 'blocktype',
            'items' => array(
                'list'=>array('keyfield'=>'id'),
                'mlt'=>array('keyfield'=>'id'),
                ),
            ),
        ),
    'District' => array(
        'title' => '地区信息',
        'source' => 'table',
        'params' => array(
            'table' => 'district',
            'orderby'=>'sortno DESC,id ASC',
            'items' => array(
                'list'=>array('keyfield'=>'id'),
                'mlt'=>array('keyfield'=>'id'),
            ),
        ),
    ),
    'Realm' => array(
        'title' => '领域信息',
        'source' => 'table',
        'params' => array(
            'table' => 'realm',
            'orderby'=>'sortno DESC,id ASC',
            'items' => array(
                'list'=>array('keyfield'=>'id'),
                'mlt'=>array('keyfield'=>'id'),
            ),
        ),
    ),
     'Role' => array(
        'title' => '后台角色',
        'source' => 'table',
        'params' => array(
            'table' => 'role',
            'orderby'=>'sortno,id',
            'items' => array(
                'mlt'=>array('keyfield'=>'id'),
                'map'=>array('keyfield'=>'id','valfield'=>'title')
            ),
        ),
    ),

    'BaseList' => array(
        'title' => '常用列表',
        'source' => 'table2',
        'params' => array(
            ),
        ),

    );
