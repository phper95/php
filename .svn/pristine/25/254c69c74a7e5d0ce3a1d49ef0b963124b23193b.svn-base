<?php
/*****************************************
功能：协议内容数据组包解包 
说明：所有参数均采用Unicode编码， 并且低位在前
 * 
*****************************************/
class CProtocal {
	public static $str_pack_tou = 0xFF; // 包头
	public static $str_pack_len = 0x0B; // 包头长
	public $str_data_len = 0;  // 整形，4个字节，数据内容长 （包头+数据）总长度，低位在前
	public $str_tou_jiaoy = 0x00; // 头校验,1个字节，表示从包头的第0个字节，到“头校验”前一个字节，按顺序依次按位异或的值
	
	/* 指令序号，调用时自增 ，整形，低位在前*/
	public static $str_zl_num = 1; //

	/* 指令类型 = 指令优先级   指令类型	指令名称*/
	
	/*指令优先级*/
	public static $str_zllx_dyx = 0x00; // 指令级别低
	public static $str_zllx_gyx = 0x01; // 指令级别高
	
	/*指令类型 */
	public static $str_zllx_gly = 0x00;		// 管理员指令
	public static $str_zllx_zdsc = 0x10;		// 终端主动上传
	public static $str_zllx_kzzd = 0x11;		// 控制终端指令
	public static $str_zllx_zdyd = 0x12;		// 终端应答指令
	public static $str_zllx_dbxz = 0x20;		// 从数据库下载数据
	public static $str_zllx_tjsj = 0x21;		// 向数据库提交数据，只需要返回处理结果
	public static $str_zllx_qqsj = 0x22;		// 向主服务请求数据
	public static $str_zllx_zdxz = 0x30;		// 主服务程序主动下发数据
	public static $str_zllx_xfyj = 0x42;		// 下发数据到邮件服务
	public static $str_zllx_xfck = 0x60;		// 下发数据到串口
	
	public static $str_zllx_xfzl = 0x70;        //  接口下发指令
	public static $str_zllx_zlyd = 0x71;        //  接口指令应答
	public static $str_zllx_ydsj = 0x72;        //  设备应答数据
	
	/*指令名称 16进制字符串 2字节*/
// 	public static $str_zlmc_dw = '0201';  // 定位指令
// 	public static $str_zlmc_code = '0301';  // 发送验证码
// 	public static $str_zlmc_dh = '0302';  // 导航指令
// 	public static $str_zlmc_clzt = '0303';  // 获取车辆状态
// 	public static $str_zlmc_clyk = '0304';   // 车辆远控指令
// 	public static $str_zlmc_obd = '0305';  // 获取车辆OBD数据
// 	public static $str_zlmc_dg = '0306';     // 车辆定格 
	
	/*
	 * 1. 设置监听号码:	指令名0x0202	参数:用户名, 终端序列号, 号码内容
2. 设置回传间隔:	指令名0x020A	参数:用户名, 终端序列号, 间隔秒数
3. 设置APN指令:		指令名0x022D	参数:用户名, 终端序列号, APN, APN用户, APN密码
4. 设置服务器:		指令名0x020D	参数:用户名, 终端序列号, IP地址, 端口号

	 */
	
	public static $str_zlmc_dw = 0x0201;  // 定位指令
	public static $str_zlmc_szjthm = 0x0202; // 设置监听号码
	public static $str_zlmc_szhcjg = 0x020A; // 设置回传间隔
	public static $str_zlmc_szapn = 0x022D; // 设置APN
	public static $str_zlmc_szfwq = 0x020D; // 设置服务器
	public static $str_zlmc_tjwl = 0x0030; // 添加围栏
	public static $str_zlmc_scwl = 0x0031; // 删除围栏
	public static $str_zlmc_fpwl = 0x0033; // 给车辆分配围栏
	public static $str_zlmc_jcwl = 0x0034; // 围栏解除
	public static $str_zlmc_code = 0x0301;  // 发送验证码
	public static $str_zlmc_dh = 0x0302;  // 导航指令
	public static $str_zlmc_clzt = 0x0303;  // 汽车体检
	public static $str_zlmc_clyk = 0x0304;   // 车辆远控指令
	public static $str_zlmc_obd = 0x0305;  // 获取车辆OBD数据
	public static $str_zlmc_xftz = 0x0307; // 下发通知
	public static $str_zlmc_sos = 0x0308; // 下发4s救援信息
	public static $str_zlmc_szzlc = 0x0309; // 设置OBD总里程
// 	public static $str_zlmc_dg = 0x0306;     // 车辆定格
	public static $str_zlmc_dg = 0x007A;     // 车辆定格
	public static $str_zlmc_iccid = 0x030A; // 获取SIM卡的ICCID
	public static $str_zlmc_tel = 0x1016; // 获取ICCID对应的手机号

	/*指令类型，或结果，默认为0x00*/
	public static $str_zljg_mr = 0x00;	//请求默认
	public static $str_zljg_sb = 0x00;	//失败
	public static $str_zljg_cg = 0x01;	//成功
	
	/*数据编码类型*/
	public static $str_sjbm = 0x01;	 // 编码类型 Unicode
	
	/*数据偏移量*/
	public static $str_sjpy_len = 0x0B;	// 数据偏移量( 内容包头长度 ) 暂时固定
	
	
	/*数据分隔符*/
	static public $str_fenge_zd = '0800';	// 字段	参数分隔符
	static public $str_fenge_h  = "1100";	// 行	参数分隔符
	static public $str_fenge_b  = "1b00";	// 表	参数分隔符
	
	/**
	 * 获取发送指令ID
	 */
	public function getSendCmdID(){
		CProtocal::$str_zl_num++;
		return CProtocal::$str_zl_num;
	}
	
	/**
	 * 根据数据格式，返回二进制数据包 
	 * @param unknown_type $data
	 * @param 16进制2位，一字节 $zllx 指令类型
	 * @param 16进制字符串,4位，两字节 $zlmc 指令名称
	 */
	public function getPackData($data,$zllx,$zlmc){
		$ryz = (time() - 1375286400); // 随机因子
		$pack_num = $ryz.rand(10,99);
		$zl_num = $ryz.rand(10,99);
		
		$data = $this->getDataBuffer($data);
		$rst = pack('c',CProtocal::$str_pack_tou); //  包头
		$rst .= pack('c',CProtocal::$str_pack_len); // 包头长
		$rst .= pack('V',(11+strlen($data))); // 数据内容长
// 		$rst .= pack('V',CProtocal::$str_zl_num);  // 包序号
		$rst .= pack('V', $pack_num);  // 包序号
		$rst .= $this->getTouJY($rst);  // 包头校验
		
// 		$rst .= pack('V',CProtocal::$str_zl_num); // 指令序号
		$rst .= pack('V',$zl_num); // 指令序号
		
		$rst .= pack('c',CProtocal::$str_zllx_gyx); // 指令优先级
		$rst .= pack('c',$zllx); //指令类型
// 		$rst .= pack('H*',$zlmc); // 指令名称
		$rst .= pack('v',$zlmc); // 指令名称
		
		$rst .= pack('c',CProtocal::$str_zljg_mr);// 指令请求结果
		$rst .= pack('c',CProtocal::$str_sjbm); // 指令编码格式
		$rst .= pack('c',CProtocal::$str_sjpy_len); // 数据偏移量
		
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
		$ge = pack('H*',CProtocal::$str_fenge_zd); //分隔符
//		$rst1 = implode($ge, $rst);
		$wei = pack('H*','0000'); // 字符串尾部
		return implode($ge, $rst).$wei;
	}
	
	/**
	 * 获取头校验的值
	 * @param unknown_type $data
	 */
	private function getTouJY($data){
		$length = strlen($data);
		$rst = substr($data,0,1);
		for($i = 0; $i < $length-1; $i++){
			$next = substr($data,$i+1,1);
			$rst = $rst ^ $next;
		}
		return $rst;
	}
	
	/**
	 * 获取低位在前的Unicode编码  Unicode 编码其实就是UCS-2
	 */
	public function getBigEndUnicode($name){
		$name = iconv('UTF-8', 'UCS-2', $name);
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
		return $rst;
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
		if (!is_array($rst))return $rst;
		
		// 下面获取数据内容
		$len = strlen($data);
		$tempData = NULL;
		$wei = pack('H*','0000'); // 字符串尾部标识
		if (substr($data,-2) == $wei) $len-=2; // 如果有，则丢弃
		$ge = pack('H*',CProtocal::$str_fenge_zd); //数据分隔符
		
		for ($i = 22; $i < $len; $i += 2) {
			$bytes = substr($data, $i,2);
			if ($bytes == $ge){
				$rst['data'][] = $this->getUTF8FromBigEndUnicode($tempData);
				$tempData = NULL;
				continue; // 丢掉$ge分隔符
			}
			$tempData .= $bytes;
		}
		if ($tempData != NULL){
			$rst['data'][] = $this->getUTF8FromBigEndUnicode($tempData);
			$tempData = NULL;
		}
		return $rst;
	}
	
	/**
	 * 判断数据包是否合法 ,如合法，则返回数据包信息,不合法，则返回空字符串
	 * @param unknown_type $data
	 */
	public function checkOKData($data){
		$rst = array();
		$len = strlen($data);
		if ($len == 0) return '';
		$packTou = substr($data, 0,1); // 获取包头，并判断
		if ($packTou != pack('c',CProtocal::$str_pack_tou)) return '1';
		
		$packLen = substr($data, 1,1); // 获取包头长，并判断
		if ($packLen != pack('c',CProtocal::$str_pack_len)) return '2';
		
		$dataLen = unpack('V',substr($data, 2,4));  // 获取数据内容长，并判断
		if (($dataLen['1']+11) != $len) return '3';
		
		$baoNum = unpack('V',substr($data, 6,4)); // 获取包序号
		$rst['bao_num'] = $baoNum['1'];
		
		$touJY = substr($data,10,1);  // 获取包校验，并进行校验
		if ($touJY != $this->getTouJY(substr($data,0,10))) return '4';
		
		$zlNum = unpack('V',substr($data, 11,4));  // 获取指令序号
		$rst['zl_num'] = $zlNum['1'];
		
		$zlYxj = substr($data, 15,1);  // 指令优先级
		$zlLx = substr($data, 16,1);   // 指令类型
		$zlName = substr($data, 17,2); // 指令名称
		
		$zljg = substr($data, 19,1);   // 指令结果，判断指令结果0失败，1成功
		if ($zljg == pack('c',CProtocal::$str_zljg_sb)){
			$rst['result'] = 0;
		} elseif ($zljg == pack('c',CProtocal::$str_zljg_cg)) {
			$rst['result'] = 1;
		}
		
		$sjbm = substr($data, 20,1);   // 获取数据编码，并判断
		if ($sjbm != pack('c',CProtocal::$str_sjbm)) return '5';
		
		$sjpy = substr($data,21,1);    // 获取数据偏移量，并判断
		if ($sjpy != pack('c',CProtocal::$str_sjpy_len)) return '6';
		return $rst;
	}
	
	/**
	 * 返回获取GPS信息，指令数据包
	 * @param unknown_type $data
	 */
	public function getGPSLocation($data){
		return $this->getPackData($data, CProtocal::$str_zllx_qqsj, CProtocal::$str_zlmc_dw);
	}
	
	/**
	 * 返回下发验证码，指令数据包 
	 * @param unknown_type $data
	 */
	public function sendCode($data){
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_code);
	}
	
	
	/**
	 * 返回导航，指令数据包 
	 * @param unknown_type $data
	 */
	public function navigate($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_dh);
	}
	
	/**
	 * 返回获取车辆状态，指令数据包 
	 * @param unknown_type $data
	 */
	public function getCarInfo($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_clzt);
	}
	
	/**
	 * 返回汽车体检，指令数据包
	 * @param $data
	 */
	public function controlCar ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_clyk);
	}
	
	/**
	 * 返回获取OBD数据，指令数据包 
	 * @param unknown_type $data
	 */
	public function getOBD($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_obd);
	}
	
	/**
	 * 返回下发通知，指令数据包
	 * @param unknown_type $data
	 * @return string
	 */
	public function sendNotice($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_xftz);
	}
	
	/**
	 * 返回设置定格状态，指令数据包
	 * @param unknown_type $data
	 */
	public function lockSetState($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_tjsj, CProtocal::$str_zlmc_dg);
	}
	
	/**
	 * 返回添加围栏，指令数据包
	 * @param unknown_type $data
	 */
	public function addFence($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_tjsj, CProtocal::$str_zlmc_tjwl);
	}
	
	/**
	 * 返回删除围栏，指令数据包
	 * @param unknown_type $data
	 * @return string
	 */
	public function delFence($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_tjsj, CProtocal::$str_zlmc_scwl);
	}
	
	/**
	 * 返回分配围栏到终端，指令数据包
	 * @param unknown_type $data
	 * @return string
	 */
	public function fenceBandTer ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_tjsj, CProtocal::$str_zlmc_fpwl);
	}
	
	/**
	 * 返回解除终端围栏，指令数据包
	 * @param unknown_type $data
	 * @return string
	 */
	public function fenceUnbandTer ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_tjsj, CProtocal::$str_zlmc_jcwl);
	}
	
	
	/**
	 * 下发4S救援信息
	 * @param unknown_type $data
	 * @return string
	 */
	public function get4sSosInfo ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_sos);
	}
	
	
	/**
	 * 设置OBD总里程指令
	 * @param unknown $data
	 * @return string
	 */
	public function setObdKm ($data) {
		return $this->getPackData($data, self::$str_zllx_xfzl,self::$str_zlmc_szzlc);
	}
	
	/**
	 * 获取终端SIM卡的ICCID
	 * @param unknown $data
	 * @return string
	 */
	public function getIccid ($data) {
		return $this->getPackData($data, self::$str_zllx_xfzl, self::$str_zlmc_iccid);
	}
	
	
	/**
	 * 通过ICCID下发电话号码
	 * @param unknown $data
	 * @return string
	 */
	public function getTel ($data) {
		return $this->getPackData($data, self::$str_zllx_xfzl, self::$str_zlmc_tel);
	}
	
	/*
	 * 1. 设置监听号码:	指令名0x0202	参数:用户名, 终端序列号, 号码内容
2. 设置回传间隔:	指令名0x020A	参数:用户名, 终端序列号, 间隔秒数
3. 设置APN指令:		指令名0x022D	参数:用户名, 终端序列号, APN, APN用户, APN密码
4. 设置服务器:		指令名0x020D	参数:用户名, 终端序列号, IP地址, 端口号

	 */
	
	/**
	 * 设置监听号码
	 * @param unknown_type $data
	 * @return string
	 */
	public function setJTNumber ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_szjthm);
	}
	
	/**
	 * 设置回传间隔
	 * @param unknown_type $data
	 * @return string
	 */
	public function setHCTime ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_szhcjg);
	}
	
	/**
	 * 设置APN
	 * @param unknown_type $data
	 * @return string
	 */
	public function setAPN ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_szapn); 
	}
	
	/**
	 * 设置服务器
	 * @param unknown_type $data
	 * @return string
	 */
	public function setServer ($data) {
		return $this->getPackData($data, CProtocal::$str_zllx_xfzl, CProtocal::$str_zlmc_szfwq);
	}
	
	
	/**
	 * 分析车辆状态
	 * @param int $data
	 */
	public function fenxiCarState($data){
		$data = pack('V',$data);
		$byte1 = substr($data, 0,1);
		for ($i = 7; $i >= 0; $i--) {
			$rst[7-$i] = $this->getstatus($byte1, $i);
		}
		print_r($rst);
		
		$byte2 = substr($data, 1,1);
		$byte3 = substr($data, 2,1);
		$byte4 = substr($data, 3,1);
	}
	
	/**
	 * 借鉴discuz方法，获取某个数代表的第N个权限标志位有没有置位
	 * @param unknown $status
	 * @param unknown $position
	 * @return number
	 */
	private function getstatus($status, $position) {
// 		$t = $status & pow(2, $position - 1) ? 1 : 0;
		$t = $status & pow(2, $position) ? 1 : 0;  // 改，我们这里是0~7位
		return $t;
	}
	
// 	private function setstatus($position, $value, $baseon = null) {
// 		$t = pow(2, $position - 1);
// 		if($value) {
// 			$t = $baseon | $t;
// 		} elseif ($baseon !== null) {
// 			$t = $baseon & ~$t;
// 		} else {
// 			$t = ~$t;
// 		}
// 		return $t & 0xFFFF;
// 	}
			
	
}