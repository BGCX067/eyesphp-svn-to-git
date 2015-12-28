<?php

class Cache extends Base {

	public $cacheDir = '/cache';
	public $lifeTime = 3600;
	public $dirLevels = 0;	//缓存保存时的目录层数
	
	private $md5id;	//md5后的缓存id

	const MAX_CACHE_DIR_LEVELS = 16;	//最大缓存目录层次
	
	public function __construct($config=array())
	{
		foreach ($config as $key=>$val) {
			$this->$key=$val;
		}
		$this->cacheDir=rtrim($this->cacheDir,'/');
		
		if ($this->dirLevels>self::MAX_CACHE_DIR_LEVELS) {
			$this->dirLevels=self::MAX_CACHE_DIR_LEVELS;
		}
	}
	
	/**
	 * 读取缓存
	 *
	 * @param string $id
	 * @return mixed|boolean
	 */
	public function get($id)
	{
		$this->md5id=md5($id);
		$file=$this->getCacheFile($this->md5id);
		if (is_file($file) && filemtime($file)>time()) {
			return unserialize(file_get_contents($file));
		} else {
			return false;
		}
	}
	
	/**
	 * 保存缓存
	 *
	 * @param mixed $data
	 * @param string $id
	 */
	public function save($data,$lifeTime=null,$id=null)
	{
		$lifeTime=time() + (strlen($lifeTime) ? $lifeTime : $this->lifeTime);
		$md5id=strlen($id) ? md5($id) : $this->md5id;
		$dir=$this->cacheDir.$this->getDirLevel($md5id);
		if (!file_exists($dir)) {
			mkdir($dir,0777,true);
		}
		$cacheFile=$dir.'/cache_'.$md5id;
		file_put_contents($cacheFile,serialize($data),LOCK_EX);
		touch($cacheFile,$lifeTime);
	}
	
	/**
	 * 获取缓存文件名
	 *
	 */
	private function getCacheFile($md5id)
	{
		return $this->cacheDir.$this->getDirLevel($md5id).'/cache_'.$md5id;
	}
	
	/**
	 * 获取缓存目录层次
	 *
	 */
	private function getDirLevel($md5id)
	{
		$levels=array();
		$levelLen=2;
		for ($i=0; $i<$this->dirLevels; $i++) {
			$levels[]='cache_'.substr($md5id,$i*$levelLen,$levelLen);
		}
		return !count($levels) ? '' : '/'.implode('/',$levels);
	}
}//class


?>