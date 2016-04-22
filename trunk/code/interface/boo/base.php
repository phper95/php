<?php
// 定义错误级别，不输出错误，api不输出错误
error_reporting(E_ALL);
// error_reporting(0);
header("Content-Type:text/html;charset=UTF-8");
ini_set("mssql.datetimeconvert","0");
date_default_timezone_set('Asia/Shanghai');

define('BOO', dirname(__FILE__).'/');
define('DB_PATH', BOO.'DB/');
define('FC_PATH', BOO.'function/');

require_once CONFIG_PATH.'ErrorCode.php';
require_once CONFIG_PATH.'Message.php';
require_once FC_PATH.'common.function.php';
require_once FC_PATH.'Logger_Writter.php';

$BOO_GLOBALA = array(); 
$BOO_GLOBALA['config'] = include CONFIG_PATH.'config.php';
$tmpConfig = include BOO.'config/config.php';

foreach ($tmpConfig as $key => $c) {
	if (!isset($BOO_GLOBALA['config'][$key])) {
		$BOO_GLOBALA['config'][$key] = $c;
	} else {
		if (is_array($BOO_GLOBALA['config'][$key]) && is_array($c)) {
			$BOO_GLOBALA['config'][$key] = array_merge($c, $BOO_GLOBALA['config'][$key]);
		}
	}
}
unset($tmpConfig);

if ($_GET) {
	foreach ($_GET as $key => $g) {
		$BOO_GLOBALA['input'][$key] = $g;
	}
}

if ($_POST) {
	foreach ($_POST as $key => $g) {
		$BOO_GLOBALA['input'][$key] = $g;
	}
}

class BooBase {
	private $_current = array();
	private $_time = 0;
	private $_type = 0; // 类型，0 APP， 1 4S后台 ， 2 终端调用
	
	function BooBase($type='app') {
		$this->_current['url'] = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		$this->_current['ip'] = GetIP();
		$this->_time = time();
		$conf = array('app'=>0, '4s'=>1, 'terminal'=>2);
		$this->_type = isset($conf[$type]) ? $conf[$type] : 0;
	}
	
	public function run() {
		$logconf = SC('log');
		if (isset($logconf['url']) && $logconf['url']) {
			$w_l = new Logger_Writer(CHECK_PATH.$logconf['path']);
			$w_l -> add ('['.date('Y-m-d H:i:s',$this->_time).']  '.ArrayToString($this->_current).PHP_EOL, $logconf['filename']);
		}
	}
	
	public function check($str, $str2, $key){
		if (empty($str) || empty($str2)) return FALSE;
		if ($str2 == md5 ( $str . $key )) {
			return true;
		}
		return FALSE;
	}
	
	public function success($arr = array()){
		if ($this->_type==0){
			$this->success_app($arr);
		} elseif ($this->_type == 1) {
			$this->success_4s($arr);
		} elseif ($this->_type ==2) {
			$this->success_ter($arr);
		}
		
		$logconf = SC('log');
		if (isset($logconf['succ']) && $logconf['succ']) {
			$w_l = new Logger_Writer(CHECK_PATH.$logconf['path']);
			$w_l -> add ('['.date('Y-m-d H:i:s',$this->_time).']  '.ArrayToString($this->_current).PHP_EOL, 'success.log');
		}
	}
	
	private function success_app($arr = array()){
		$arr['succ'] = '1';
		echo json_encode($arr);
	}
	
	private function success_4s($arr = array()) {
		$arr['succ'] = '0';
		echo json_encode($arr);
	}
	
	private function success_ter($arr = array()){
		echo 0;
	}
	
	public function error($code = NULL, $arr = array(), $DB = null){
		if (!empty($DB)) { // 如果提供数据库连接，则关闭数据库连接
			$DB->close();
		}
		
		if ($this->_type==0){
			$this->error_app($code, $arr);
		} elseif ($this->_type == 1) {
			$this->error_4s($code, $arr);
		} elseif ($this->_type == 2) {
			$this->error_ter($code, $arr);
		}
		
		$logconf = SC('log');
		if (isset($logconf['error']) && $logconf['error']) {
			$w_l = new Logger_Writer(CHECK_PATH.$logconf['path']);
			$w_l -> add ('['.date('Y-m-d H:i:s',$this->_time).']  '.ArrayToString($this->_current).PHP_EOL, 'error.log');
		}
		exit();
	}
	
	private function error_app ($code, $arr=array()){
		$arr['succ'] = '0';
		if ($code != NULL){
			$arr['err'] = $code;
		}
		$arr['reason'] = isset($arr['reason'])?$arr['reason']:Message::DEFAULT_ERROR;
		echo json_encode($arr);
	}
	
	private function error_4s ($code, $arr=array()){
		$arr['succ'] = isset($code)?$code:1;
		$arr['reason'] = isset($arr['reason'])?$arr['reason'] : Message::DEFAULT_ERROR;
		echo json_encode($arr);
	}
	
	private function error_ter ($code, $arr=array()){
		echo $code;
	}
	
	/**
	 * ajax 跨域输出
	 * @param string $code
	 * @param string $callback
	 * @param unknown $arr
	 */
	public function callbackError($code = NULL,$callback = '_callback',$arr = array(), $DB = null){
		if (!empty($DB)){
			$DB->close();
		}
		$arr['succ'] = '0';
		if ($code != NULL){
			$arr['err'] = $code;
		}
		$arr['reason'] = isset($arr['reason'])?$arr['reason']:Message::DEFAULT_ERROR;
		echo $callback.'('.json_encode($arr).')';
		$logconf = SC('log');
		if (isset($logconf['error']) && $logconf['error']) {
			$w_l = new Logger_Writer(CHECK_PATH.$logconf['path']);
			$w_l -> add ('['.date('Y-m-d H:i:s',$this->_time).']  '.ArrayToString($this->_current).PHP_EOL, 'error.log');
		}
		exit();
	}
	/**
	 * ajax跨域返回
	 * @param unknown $arr
	 * @param string $callback
	 */
	public function callbackSuccess($arr = array(),$callback = '_callback'){
		$arr['succ'] = '1';
		echo $callback.'('.json_encode($arr).')';
		$logconf = SC('log');
		if (isset($logconf['succ']) && $logconf['succ']) {
			$w_l = new Logger_Writer(CHECK_PATH.$logconf['path']);
			$w_l -> add (ArrayToString($this->_current).PHP_EOL, 'success.log');
		}
	}
	
	public function getTime(){
		return $this->_time;
	}
}

/**
 * 获取 config 信息 
 * @param string $key
 * @return Ambigous <>
 */
function SC ($key='') {
	global $BOO_GLOBALA;
	if (empty($key)) {
		return $BOO_GLOBALA['config'];
	}
	return isset($BOO_GLOBALA['config'][$key]) ? $BOO_GLOBALA['config'][$key] : null;
}

/**
 * 获取参数
 * @param string $key
 * @return Ambigous <NULL, string>
 */
function SI ($key='') {
	global $BOO_GLOBALA;
	return isset($BOO_GLOBALA['input'][$key]) ? addslashes($BOO_GLOBALA['input'][$key]) : null;
}


