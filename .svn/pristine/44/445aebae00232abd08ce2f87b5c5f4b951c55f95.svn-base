<?php
class OutPut{
	public static function error($code = NULL,$arr = array()){
		global $DB;
		if (!empty($DB)){
			$DB->close();
		}
		$arr['succ'] = '0';
		if ($code != NULL){
			$arr['err'] = $code;
		}
		$arr['reason'] = isset($arr['reason'])?$arr['reason']:Message::DEFAULT_ERROR;
		echo json_encode($arr);
		exit();
	}
	
	public static function success($arr = array()){
		$arr['succ'] = '1';
		echo json_encode($arr);
	}
	
	/**
	 * ajax 跨域输出
	 * @param string $code
	 * @param string $callback
	 * @param unknown $arr
	 */
	public static function callbackError($code = NULL,$callback = '_callback',$arr = array()){
		global $DB;
		if (!empty($DB)){
			$DB->close();
		}
		$arr['succ'] = '0';
		if ($code != NULL){
			$arr['err'] = $code;
		}
		$arr['reason'] = isset($arr['reason'])?$arr['reason']:Message::DEFAULT_ERROR;
		echo $callback.'('.json_encode($arr).')';
		exit();
	}
	/**
	 * ajax跨域返回
	 * @param unknown $arr
	 * @param string $callback
	 */
	public static function callbackSuccess($arr = array(),$callback = '_callback'){
		$arr['succ'] = '1';
		echo $callback.'('.json_encode($arr).')';
	}
}

/*
 * 获取参数类
 */
class GetParm{
	/*
	 * post get 整合
	 */
	public static function request($parm){
		return isset($_POST[$parm])?$_POST[$parm]:$_GET[$parm];
	}
	
	// 整合 post get 并过滤
	public static function saveRequest($parm){
		$rst = isset($_POST[$parm])?$_POST[$parm]:$_GET[$parm];
		return GetParm::saveStr($rst);
	}
	
	// 过滤参数
	public static function saveStr($str){
		return addslashes($str);
	}
}

class WirteLog{
	public static function w($filename,$data) {
		if (is_array($data)){
			foreach ($data as $k=>$v) {
				$str[] = $k ." => " .$v;
			}
		} else {
			$str[] = $data;
		}
		$filename = 'log/'.date("Y-m-d").'/'.$filename;
		self::_mkSavePath(dirname($filename));
		file_put_contents($filename, "[".date('Y-m-d H:i:s')."]  ".implode(',  ', $str).PHP_EOL, FILE_APPEND);
	}
	
	/**
	 * 递归生成文件保存目录
	 * @access public
	 * @param string $path 文件保存目录
	 * @return void
	 */
	private static function _mkSavePath($path)
	{
		if (!file_exists($path))
		{
			self::_mkSavePath(dirname($path));
			mkdir($path, 0777);
		}
	}
}

class S4 {
	public static function getErrorInfo($code) {
		$code = intval($code);
		switch ($code) {
			case 0x0001: // 输入参数错误
				return '参数错误';
			case 0x0003: // 找不到Meta
				return '呃。。。碰到错误了';
			case 0x0010: // 内部参数错误
				return '系统内部参数错误';
			case 0x0011: // 推荐客户已经存在
				return '推荐失败，您推荐客户已经存在';
			case 0x0012: // 提醒已经完成，
				return '提醒已经完成，请勿重复提交';
			case 0x0014: // 不能推荐自己
				return '不能推荐自己';
			case 0x0015: // 已经完成评价
				return '请勿重复提交评价';
			case 0x0016: // 重复预约
				return '你已成功预约，无需再次预约';
			default:
				return '未知错误'.$code;
		}
	}
}

/**
 * 终端类
 * @author Administrator
 *
 */
class Terminal {
	/**
	 * 是否具有发送消息功能
	 * @param unknown $type
	 */
	static function IsEnableCode($type){
		if (empty($type) || !is_string($type)) {
			return false;
		}
		//H-650，EX309-A-1，X309-A-1，EX309-A-2，X309-A-2，EX309-A-3，X309-A-3，EX301-C-2，X301-C-2，EX301-C-1，X301-C-1，EX310-B-1，X310-B-1，EX310-B-2，X310-B-2，ERD-B-1，RD-B-1，ER-C-1，R-C-1，ED-B-1，D-B-1

		$terArr = array (
				'EX309-A-1', // 全能加强版
				'EX309-A-2', // 全能胎压加强版
				'EX309-A-3', // 全能精简版
				'EX301-C-2', // 基本型
				'EX310-B-1', // 云版
				'EX310-B-2', // 普及版
					
				'ERD-B-1', // 车机加强型
				'ER-C-1', // 单一行车记录型
				'ED-B-1', // 云电子狗
				
				'X301-C-2', // 单一云版（电阻屏）
				'X310-B-1', // 云版
				'D-B-1', // 单一云电子狗
				'YUNDOG', // 云狗
				'ICARDOG', // 爱车狗
		);

		return in_array($type, $terArr);
	}
}

/**
 * 解析类
 * @author Administrator
 *
 */
class Traslat {
	private $_Bd_pi;
	private $_a;
	private $_ee;


	//初始化
	function Traslat(){
		$this->_Bd_pi = M_PI * 3000.0 / 180.0;
		$this->_a = 6378245.0;
		$this->_ee = 0.00669342162296594323;
	}

	/**
	 * 判断是否在天朝境内
	 * @param lat
	 * @param lon
	 * @returns
	 */
	public function outOfChina ($lat, $lon) {
		if ($lon < 72.004 || $lon > 137.8347)
			return 1;
		if ($lat < 0.8293 || $lat > 55.8271)
			return 1;
		return 0;
	}

	/**
	 * 转化纬度
	 * @param x
	 * @param y
	 * @returns
	 */
	private function transformLat ($x, $y) {
		$ret = -100.0 + 2.0*$x + 3.0*$y + 0.2*$y*$y + 0.1*$x*$y + 0.2 * sqrt(abs($x));
		$ret += (20.0 * sin(6.0 * $x * M_PI) + 20.0 * sin(2.0 * $x * M_PI)) * 2.0/3.0;
		$ret += (20.0 * sin($y * M_PI) + 40.0 * sin($y / 3.0 * M_PI)) * 2.0/3.0;
		$ret += (160.0 * sin($y / 12.0 * M_PI) + 320 * sin($y * M_PI / 30.0)) * 2.0/3.0;

		return $ret;
	}

	/**
	 * 转化经度
	 * @param x
	 * @param y
	 * @returns
	 */
	private function transformLon($x, $y) {
		$ret = 300.0 + $x + 2.0*$y + 0.1*$x*$x + 0.1*$x*$y + 0.1*sqrt(abs($x));
		$ret += (20.0 * sin(6.0 * $x * M_PI) + 20.0 * sin(2.0 * $x * M_PI)) * 2.0/3.0;
		$ret += (20.0 * sin($x * M_PI) + 40.0 * sin($x / 3.0 * M_PI)) * 2.0/3.0;
		$ret += (150.0 * sin($x / 12.0 * M_PI) + 300.0 * sin($x / 30.0*M_PI)) * 2.0/3.0;

		return $ret;
	}

	/**gps转火星坐标*/
	public function wgs2gcj($wgLat, $wgLon) {
		$rst = array('lat'=>0.0, 'lon'=>0.0);
		if ($this->outOfChina($wgLat, $wgLon)) {
			$rst['lat'] = $wgLat;
			$rst['lon'] = $wgLon;
		}

		$dLat = $this->transformLat($wgLon - 105.0, $wgLat - 35.0);
		$dLon = $this->transformLon($wgLon - 105.0, $wgLat - 35.0);

		$radLat = $wgLat / 180.0 * M_PI;
		$magic = sin($radLat);
		$magic = 1 - $this->_ee * $magic * $magic;

		$sqrtMagic = sqrt($magic);
		$dLat = ($dLat * 180.0) / (($this->_a* (1-$this->_ee)) / ($magic*$sqrtMagic) * M_PI);
		$dLon = ($dLon * 180.0) / ($this->_a / $sqrtMagic * cos($radLat) * M_PI);

		$rst['lat'] = $wgLat + $dLat;
		$rst['lon'] = $wgLon + $dLon;

		return $rst;
	}

	/**火星转GPS坐标, 反推,误差1－2米*/
	public function gcj2wgs($mgLat, $mgLon) {
		$rst = array('lat'=>0.0, 'lon'=>0.0);
		$dLat = $this->transformLat($mgLon - 105.0, $mgLat - 35.0);
		$dLon = $this->transformLon($mgLon - 105.0, $mgLat - 35.0);

		$radLat = $mgLat / 180.0 * M_PI;
		$magic = sin($radLat);
		$magic = 1 - $this->_ee * $magic*$magic;

		$sqrtMagic = sqrt($magic);
		$dLat = ($dLat * 180.0) / (($this->_a* (1-$this->_ee)) / ($magic*$sqrtMagic) * M_PI);
		$dLon = ($dLon * 180.0) / ($this->_a / $sqrtMagic * cos($radLat) * M_PI);

		$rst['lat'] = $mgLat - $dLat;
		$rst['lon'] = $mgLon - $dLon;
		return $rst;
	}

	//	///**火星转GPS坐标，二分法*/
	//	function gcj2wgs_1(mgLat, mgLon)
	//	{
	//		//取得合适区域
	//		var mLat, mLon;
	//		var nLat, nLon;
	//
	//		var i = 0, j = 0, z = 0;
	//		var ret = 360;
	//		var tmp;
	//		var rst = {};
	//		for (j = 0; j < 5; j++) {
	//			for (i = 0; i < 41; i++) {
	//				nLon = mgLon + (0.01*(10 - i))*Math.pow(0.1, j);
	//				for (z = 0; z < 41; z++) {
	//					nLat = mgLat + (0.01*(10 - z))*Math.pow(0.1, j);
	//					var tmprst = wgs2gcj(nLat, nLon);
	//					tmp = Math.sqrt((tmprst.lat - mgLat)*(tmprst.lat - mgLat) + (tmprst.lon - mgLon)*(tmprst.lon - mgLon));
	//					if ( tmp < ret) {
	//						rst.lat = nLat;
	//						rst.lon = nLon;
	//						ret = tmp;
	//					}
	//				}
	//			}
	//		}
	//		return rst;
	//	}

	/**火星转百度*/
	public function gcj2bd($ggLat, $ggLon) {
		$x = $ggLon;
		$y = $ggLat;
		$z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $this->_Bd_pi);
		$theta = atan2($y, $x) + 0.000003 * cos($x * $this->_Bd_pi);
		$rst = array();
		$rst['lat'] = $z * sin($theta) + 0.006;
		$rst['lon'] = $z * cos($theta) + 0.0065;

		return $rst;
	}

	/**百度转火星*/
	public function bd2gcj ($bgLat, $bgLon) {
		$x = $bgLon - 0.0065;
		$y = $bgLat - 0.006;
		$z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $this->_Bd_pi);
		$theta = atan2($y, $x) - 0.000003 * cos($x * $this->_Bd_pi);
		$rst = array();
		$rst['lon'] = $z * cos($theta);
		$rst['lat'] = $z * sin($theta);

		return $rst;
	}

	/**百度转GPS*/
	public function bd2wgs ($bgLat, $bgLon) {
		$bgrst = $this->bd2gcj($bgLat, $bgLon);
		return $this->gcj2wgs($bgrst['lat'], $bgrst['lon']);
	}

	/**GPS转百度*/
	public function wgs2bd ($wgLat, $wgLon) {
		$wgrst = $this->wgs2gcj($wgLat, $wgLon);
		return $this->gcj2bd($wgrst['lat'], $wgrst['lon']);
	}
}



function GetIP(){
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return($ip);
}

function ArrayToString($arr) {
	if (is_array ( $arr )) {
		foreach ( $arr as $k => $v ) {
			$str [] = $k . " => " . $v;
		}
	} else {
		return $arr;
	}
	return implode(',  ', $str);
}

function getHttpClient ($url, $data) {
	require_once 'HttpClient.class.php';
	$arr_url = parse_url($url);
	$port = isset($arr_url['port']) ? $arr_url['port'] : 80;
	$request = new HttpClient($arr_url['host'], $port);
	$result = $request->get($arr_url['path'],$data);
	if (empty($result)) {
		return NULL;
	} else {
		return $request;
	}
}


function getTerminaDB(){
	$conf = @strtolower(SC('terminal'));
	if ($conf == 'mysql') {
		
	} else { //默认sqlserver
		require_once DB_PATH.'ms_db.class.php';
		return new MssqlDB ();
	}
}

function getCProtocal(){
	require_once FC_PATH.'cProtocal.class.php';
	return new CProtocal();
}

function getXCommand(){
	require_once FC_PATH.'XCommand.class.php';
	return new XCommand();
}

function unicode_decode($name) {
	$rst = array ();
	$ca = explode('.', $name);
	foreach ($ca as $a) {
		$rst[] = caca_decode($a);
	}
	return implode('.', $rst);
}

// 将UNICODE编码后的内容进行解码
function caca_decode($name) {
	// 转换编码，将Unicode编码转换成可以浏览的utf-8编码
	$pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
	preg_match_all ( $pattern, $name, $matches );
	if (! empty ( $matches )) {
		$name = '';
		for($j = 0; $j < count ( $matches [0] ); $j ++) {
			$str = $matches [0] [$j];
			if (strpos ( $str, '\\u' ) === 0) {
				$code = base_convert ( substr ( $str, 2, 2 ), 16, 10 );
				$code2 = base_convert ( substr ( $str, 4 ), 16, 10 );
				$c = chr ( $code ) . chr ( $code2 );
				$c = iconv ( 'UCS-2', 'UTF-8', $c );
				$name .= $c;
			} else {
				$name .= $str;
			}
		}
	}
	return $name;
}

/**
 * 根据方位度判断方向
 * @param unknown_type $d
 */
function getDirection($d){
	$arr = array(1=>'北',2=>'东',3=>'南',4=>'西');
	$du = "°";
	$f = ceil($d/90);
	$yd = $d%90;
	if ($yd<10){
		$w = $f==0 ? 1 : $f;
		$str = $arr[$w];
	} else if ($yd>80) {
		$w = ($f+1) > 4 ? 1 : ($f+1);
		$str = $arr[$w];
	} else {
		switch ($f){
			case 1:
				$str = $arr[2].$arr[1]; break;
			case 2:
				$str = $arr[2].$arr[3]; break;
			case 3:
				$str = $arr[4].$arr[3]; break;
			case 4:
				$str = $arr[4].$arr[1]; break;
		}
	}
	$str =  $d.$du."($str)";
	return $str;
}