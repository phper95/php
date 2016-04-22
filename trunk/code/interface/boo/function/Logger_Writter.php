<?php

class Logger_Writer
{

	private $_rootPath = '/'; // 日志根路径
	private $_maxSize = 104857600; // 单个日志文件大小 100M
	
	public function __construct($rootPath = '', $maxSize = '')
	{
		if ($rootPath != '') {
			$this->_rootPath = $rootPath;
		}
		if ($maxSize != '') {
			$this->_maxSize = $maxSize;
		}
	}
	
	/**
	 * @param String $log
	 * @param String $actionSuffix
	 */
	public function add($log, $actionSuffix = '')
	{
		$filePath = $this->_rootPath . date ( "Ym", time () ) . '/' . date ( 'Y-m-d') . '/';
// 		$fileName = date ( 'Y-m-d', time () ) . (($actionSuffix != '') ? ".$actionSuffix" : '') . '.log';
		$fileName = (($actionSuffix != '') ? $actionSuffix : (date ( 'Y-m-d')));
		
		$this->mkdirs($filePath);
		clearstatcache ();
		
		$lock = new File_Lock ();
		$lock->writeLock ();
		
		// ------------------------临界代码 BEGIN-------------------------//
		$fileInfo = @stat ( $filePath . $fileName );
		if (($fileInfo != FALSE) && ($fileInfo ['size'] >= $this->_maxSize)) {
			for($suffix = 1;; ++ $suffix)
				if (! file_exists ( $filePath . $fileName . '.' . $suffix ))
					break;
			rename ( $filePath . $fileName, $filePath . $fileName . '.' . $suffix );
		}
		
		$handle = fopen ( $filePath . $fileName, "a+" ); // 创建文件
		if ($handle) {
			fwrite ( $handle, $log );
			fclose ( $handle );
		}
		// ------------------------临界代码 END-------------------------//
		$lock->unlock ();
	}
	
	private function mkdirs($dir, $mode = 0777)
	{
		if (is_dir($dir) || @mkdir($dir, $mode, true))
			return true;
		if (!$this->mkdirs(dirname($dir), $mode))
			return false;
		return @mkdir($dir, $mode, true);
	}
}

class File_Lock
{
	private $name;
	private $handle;
	private $mode;
	
	function __construct($filename = '/tmp/log.lock', $mode = 'a+b')
	{
		$this->name = $filename;
		$this->mode = $mode;
		$this->handle = @fopen ( $this->name, $mode );
	}
	
	public function close()
	{
		if ($this->handle !== null && $this->handle !== false) {
			@fclose ( $this->handle );
			$this->handle = null;
		}
	}
	
	public function __destruct()
	{
		$this->close ();
	}
	
	public function lock($lockType, $nonBlockingLock = false)
	{
		if ($nonBlockingLock) {
			return flock ( $this->handle, $lockType | LOCK_NB );
		} else {
			return flock ( $this->handle, $lockType );
		}
	}
	
	public function readLock()
	{
		return $this->lock ( LOCK_SH );
	}
	
	public function writeLock($wait = 0.1)
	{
		// ------------fopen fail return false;
		if ($this->handle === null || $this->handle === false) {
			return;
		}
		// -----------------------------
		
		$startTime = microtime ( true );
		$canWrite = false;
		do {
			$canWrite = flock ( $this->handle, LOCK_EX );
			if (! $canWrite) {
				usleep ( rand ( 10, 1000 ) );
			}
		} while ( (! $canWrite) && ( (microtime(true) - $startTime) < $wait) );
	}
	
	public function unlock()
	{
		if ($this->handle !== null && $this->handle !== false) {
			return flock ( $this->handle, LOCK_UN );
		} else {
			return true;
		}
	}

}

?>