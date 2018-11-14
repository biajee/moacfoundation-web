<?php
// +----------------------------------------------------------------------+
// | ThinkPHP [ Memcached Session 驱动 ]
// +----------------------------------------------------------------------+
// | PHP version 5.5                                                       
// +----------------------------------------------------------------------+
// | Copyright (c) 2013-2015                              
// +----------------------------------------------------------------------+
// | Authors: Original Author <tech@33hl.cn>
// |          Sinda <QQ365102010>
// +----------------------------------------------------------------------+
//
namespace Think\Session\Driver;
class Memcached{
	
	protected $lifeTime     = 7200;
	protected $sessionName  = '';
	protected $handler       = null;
	
	
    /**
     * 打开Session
	 * @Sinda admin@ipingtai.com
     * @access public 
     * @param string $savePath 
     * @param mixed $sessName
	 * @return void
     */

	public function open($savePath, $sessName) {
		$this->lifeTime     = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : $this->lifeTime;
		$this->handler       = new \Memcached();
		//dump($this->handle);exit;
        $servers = C('MEMCACHED_SERVER') ? : null;
		$options = C('MEMCACHED_LIB') ? : null;

		$servers && $this->handler->addServers($servers);
		$options && $this->handler->setOptions($options);
		return true;
	}

    /**
     * 关闭Session 
     * @access public 
	 * 感谢@muyuto 已经TP官方朋友的提出
     */
	public function close() {
		$this->handler->close();
		return true;
	}

    /**
     * 读取Session 
     * @access public 
     * @param string $sessID 
     */
	public function read($sessID) {
        return $this->handler->get($this->sessionName.$sessID);
	}

    /**
     * 写入Session 
     * @access public 
     * @param string $sessID 
     * @param String $sessData  
     */
	public function write($sessID, $sessData) {
		return $this->handler->set($this->sessionName.$sessID, $sessData, 0, $this->lifeTime);
	}

    /**
     * 删除Session 
     * @access public 
     * @param string $sessID 
     */
	public function destroy($sessID) {
		return $this->handler->delete($this->sessionName.$sessID);
	}

    /**
     * Session 垃圾回收
     * @access public 
     * @param string $sessMaxLifeTime
	 * @return void
     */
	public function gc($sessMaxLifeTime) {
		return true;
	}
}//End
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 