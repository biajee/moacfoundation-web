<?php
namespace Common\Service;
class CacheService {
    protected $_configs = null;
    protected $_cache = array();
	
    public function __construct() {
        $file = COMMON_PATH.'Conf/cache.php';
        $this->_configs = load_config($file);    
    }
    
    protected function writeCache($key, $val, $expire = 7200) {
        S($key, $val, $expire);
    }
    
    protected function readCache($key) {
        return S($key);
    }
    
    public function cacheData($name) {
        $config = $this->_configs[$name];
        if (empty($config)) {
            $name = 'BaseList';
            $config = $this->_configs[$name];
        }

        $list = array();
        $source = $config['source'];
        $params = $config['params'];
        $expire = $config['expire'];
        if (empty($expire))
            $expire = null;
		$model = M();
        $prefix = C('DB_PREFIX');
        switch($source) {
            case 'table':
                $table = $params['table'];
                $items = $params['items'];
				$model->table('__'.strtoupper($table).'__');
				if ($params['orderby'])
					$model->order($params['orderby']);
                $_list = $model->select();
                if (empty($_list)) 
                    return false;
                
                $data = array();
                $alias = array(
                    'list' => 'Lst',
                    'mlt' => 'Mlt',
                    'map' => 'Map',
                    'tree' => 'Tlt'
                );
                foreach($items as $key => $item) {
                    switch($key) {
                    	case 'list':
                    		$list =  $_list;
                    		break;
                        case 'mlt': 
                        	foreach($_list as $v) {
                        		$list[$v[$item['keyfield']]] = $v;
                        	}
                            break;
                        case 'map':
                            foreach($_list as $v) {
                                $list[$v[$item['keyfield']]] = $v[$item['valfield']];
                            }
                            break;
                        case 'tree':
                            $list = tree_build($_list, 0, $item['idfield'], $item['upfield']);
                            break;
                    }
                    $k = $name.$alias[$key];
                    $this->writeCache($k, $list, $expire);
                    unset($list);
                }

                break;
            case 'table2':
                $model->table('__LISTTYPE__');
                if ($name == 'BaseList')
                    $where = null;
                else
                    $where = array('code'=>array('eq', $name));
                $typeList = $model->where($where)->select();
                foreach ($typeList as $key => $val) {
                    $mod = $val['model'];
                    $name = $val['code'];
                    $where = array('catid' => array('eq', $val['id']));
                    $order = 'sortno ASC';
                    $_list = $model->table('__LISTITEM__')->where($where)->order($order)->select();
                    $list = array();
                    foreach($_list as $k => $v) {
                        if (!empty($v['param'])) {
                            $v['param'] = explode(',', $v['param']);
                        }
                        $list[$v['key']] = $v;
                    }
                    $k = $name.'Mlt';
                    $this->writeCache($k, $list, $expire);
                }
                break;
        }
        return $list;
        
    }
    
    public function initCache() {
        foreach($this->_configs as $k=>$v) {
            $this->cacheData($k);
        }
    }
    
    public function getData($key) {
        if (empty($this->_cache[$key])) {
        	$this->_cache[$key] = $this->readCache($key);
        	if (empty($this->_cache[$key])) {
                $name = substr($key,0, -3);
        		$this->cacheData($name);
        		$this->_cache[$key] = $this->readCache($key);
        	}
        }
        return $this->_cache[$key];
    }
    
    public function getConfig() {
        return $this->_configs;
    }
}