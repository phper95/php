<?php
/*****************************************
功能：协议内容数据组包解包 
说明：所有参数均采用Unicode编码， 并且低位在前
 * 
*****************************************/
class XCommand {
	public static $str_pack_tou = 'FF00'; // 包头
	
	/* 指令类型 = 指令优先级   指令类型	指令名称*/
	
	/* 指令代码*/
	public static $str_zldm_gps = 0x0001;  // 定位指令
	public static $str_zldm_gps_area = 0x0002; // 定位区间
	public static $str_zldm_gps_id = 0x0003; // 定位区间，通过ID查询
	public static $str_zldm_teid = 0x0004; // 获取TEID 
	
	public static $str_zlbb = 0x01; // 指令版本
	
	/*指令服务器编码*/
	public static $str_fwq_001 = 0x01; // 服务器1
	
	private $_x_num;
	private $_head_len;
	
	function XCommand($x_num = null){
		$this->_x_num = empty($x_num) ? time() : $x_num;
		$this->_head_len = 12;
	}
	
	/**
	 * 根据数据格式，返回二进制数据包 
	 * @param unknown_type $data
	 * @param 16进制2位，一字节 $zllx 指令类型
	 * @param 16进制字符串,4位，两字节 $zlmc 指令名称
	 */
	public function getPackData($data,$zldm){
		$ryz = (time() - 1375286400); // 随机因子
		
		$data = $this->getDataBuffer($data);
		$rst = pack('H*',self::$str_pack_tou); //  包头
		$rst .= pack('v',$zldm); // 指令代码
		
		$rst .= pack('c',self::$str_zlbb); // 指令版本
		$rst .= pack('c',self::$str_fwq_001); // 服务器编码
		
		$rst .= pack('V',$this->_x_num); // 指令序号 这里给出时间戳
		
		$rst .= pack('V',(strlen($data))); // 数据长度
		
		
		$rst .= $data; // 数据内容
		return $rst;
	}
	
	/**
	 * 生成包内容的数据内容部分 
	 * @param stringArray $data
	 */
	public function getDataBuffer($data){
		if (empty($data)) return '';
		$rst = NULl;
		if (is_array($data)){
			$len = count($data);
			for ($i=0; $i<$len; $i++) {
				$rst[] = $this->getBigEndUnicode($data[$i]);
			}
		} else {
			$rst[] = $this->getBigEndUnicode($data);
		}
		return implode('', $rst);
	}
	
	/**
	 * 获取低位在前的Unicode编码  Unicode 编码其实就是UCS-2
	 */
	public function getBigEndUnicode($name){
// 		$name = iconv('UTF-8', 'UCS-2', $name);
		$len = strlen($name);
		$rst = NULL;
		for ($i = 0; $i < $len; $i += 2) {
			$p = substr($name,$i,1);
			$n = substr($name,$i+1,1);
			if (PHP_OS == "WINNT") {
				$rst = $rst.$p.$n;
// 				$rst = $rst.$n.$p;
			} else {
				$rst = $rst.$p.$n;
			}
		}
		return $rst;
		return $name;
	}
	
	/**
	 * 根据低位在前的Unicode编码获取UTF-8编码格式 
	 */
	public function getUTF8FromBigEndUnicode($name){
		$len = strlen($name);
		$rst = NULL;
		for ($i = 0; $i < $len; $i += 2) {
			$p = substr($name,$i,1);
			$n = substr($name,$i+1,1);
			if (PHP_OS == "WINNT") {
				$rst = $rst.$n.$p;
			} else {
				$rst = $rst.$p.$n;
			}
		}
//		$rst = iconv('USC-2', 'UTF-8', $rst); // 这里解析不了
		$rst = @iconv('', 'UTF-8', $rst); 
		return $rst;
	}
	
	/**
	 * 分析数据包，解包 
	 * @param unknown_type $data
	 */
	public function fenxiData($data){
		$rst = $this->checkOKData($data);
		if ($rst !== true)return $rst;
		
		// 下面获取数据内容
		$len = strlen($data);
		return substr($data, $this->_head_len, ($len-$this->_head_len));
	}
	
	/**
	 * 判断数据包是否合法 ,如合法，则返回数据包信息,不合法，则返回空字符串
	 * @param unknown_type $data
	 */
	public function checkOKData($data){
		$rst = array();
		$len = strlen($data);
		if ($len == 0) return '';
		$packTou = substr($data, 0,2); // 获取包头，并判断
		if ($packTou != pack('H*',self::$str_pack_tou)) return '1';
		
		return true;
	}
	
	
	
	private function getLenString ($str, $co=16){
		$len = strlen($str);
		$ge = pack('c',0x00);
		for($i=$len; $i<$co; $i++) {
			$str .= $ge;
		}
		return $str;
	}
	
	
	/**
	 * 
	 * @param unknown $data
	 * @return string|NULL
	 */
	public function getGPSLocation($data){
		if (isset($data['imei'])) {
			$rst[] = $this->getLenString($data['imei']);
			return $this->getPackData($rst, self::$str_zldm_gps);
		}
		return NULL;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return string
	 */
	public function getGPSArea($data) {
		if (isset($data['imei'])) {
			$rst[] = $this->getLenString($data['imei']);
			$rst[] = pack('V', $data['stime']);
			$rst[] = pack('V', $data['etime']);
			$sort  = (isset($data['sort']) && $data['sort'] == 'desc') ? 0x01 : 0x00;
			$rst[] = pack('c', $sort);
			$count = isset($data['count']) ? abs($data['count']) : 0;
			$rst[] = pack('v', $count);
			return $this->getPackData($rst, self::$str_zldm_gps_area);
		}
	}
	
	public function getGPSAreaForId($data){
		if (isset($data['imei'])) {
			$rst[] = $this->getLenString($data['imei']);
			$rst[] = pack('V', $data['id']);
			$count = isset($data['count']) ? $data['count'] : 0;
			$rst[] = pack('V', $count);
			return $this->getPackData($rst, self::$str_zldm_gps_id);
		}
	}
	
	public function getImeiByIccid($data){
		if (isset($data['iccid'])) {
			$rst[] = $this->getLenString($data['iccid'], 20);
			return $this->getPackData($rst, self::$str_zldm_teid);
		}
	}
	
			
	
}