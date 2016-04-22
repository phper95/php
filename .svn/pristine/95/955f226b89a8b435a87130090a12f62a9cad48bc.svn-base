<?php
class Api_Logger_Analyse {
	
	/** 超时时间 */
	private $_timeout = 15;
	
	/** 读取一行的Buffer大小 */
	private $_lineBuffer = 102400;
	
	/** 超时时返回的字符串*/
	private $_strTimeout = 'time out';
	
	/** 分隔符*/
	private $_ge = ' | ';
	
	
	/**
	 * 获取活跃用户留存
	 * Enter description here ...
	 * @param unknown_type $s_day
	 * @param unknown_type $e_day
	 * @param unknown_type $type
	 */
	function getActiveAliveByDay($day) {
		$month = substr($day, 0,7);
		$fileName = 'nar/'.$month.'/'.$day.'.txt';
		$handle = $this->openFile2 ($fileName);
		if ($handle != NULL) {
			$rst = array();
			$time = ''; //记录行的时间。
			for($line = 1; ! feof ( $handle ); $line ++) {
				$buffer = trim ( fgets ( $handle ) );
				if (empty ( $buffer )) {
					$line --;
					continue;
				}
				$tmp = explode ( ' | ', $buffer );
				if ($tmp[1] == '0') continue;
				$rst[$tmp[0]]['active'] = $tmp[1];
				$now = time ();
				if ($now - NOW_TIME > $this->_timeout) { // 超时
					fclose ( $handle );
					return $this->_strTimeout;
				}
			}
			fclose ( $handle );
			return $rst;
		} else {
			return NULL;
		}
	}
	
	/**
	 * 获取活跃用户留存
	 * Enter description here ...
	 * @param unknown_type $s_day
	 * @param unknown_type $e_day
	 * @param unknown_type $type
	 */
	function getActiveAlive($s_day, $e_day, $type = "day") {
		$fileName = 'ant_day.txt';
		$handle = $this->openFile2 ($fileName);
		if ($handle != NULL) {
			$rst = array();
			$time = ''; //记录行的时间。
			for($line = 1; ! feof ( $handle ); $line ++) {
				$buffer = trim ( fgets ( $handle ) );
				if (empty ( $buffer )) {
					$line --;
					continue;
				}
				$tmp = explode ( ' | ', $buffer );
				$rst[$tmp[0]]['active'] = $tmp[1];
				$rst[$tmp[0]]['news'] = $tmp[2];
				$rst[$tmp[0]]['total'] = $tmp[3];
				$now = time ();
				if ($now - NOW_TIME > $this->_timeout) { // 超时
					fclose ( $handle );
					return $this->_strTimeout;
				}
			}
			fclose ( $handle );
			return $rst;
		} else {
			return NULL;
		}
	}
	
	/**
	 * 获取弹幕
	 * Enter description here ...
	 * @param unknown_type $day
	 */
	function getPoptxt($day) {
		$fileName = 'mv_poptxt/stat.txt';
		return $this->getStat($fileName, $day); // 最原始的返回
	}
	
	/**
	 * 获取活跃IP地址用户量
	 * Enter description here ...
	 * @param unknown_type $day
	 */
	function getIpAddrActive($day){
		$fileName = 'active_user_ip_address/day.txt';
		$rst = array();
		$handle = $this->openFile ( $fileName, $day );
		if ($handle != NULL) {
			for($line = 1; ! feof ( $handle ); $line ++) {
				$buffer = trim ( fgets ( $handle ) );
				if (empty ( $buffer )) {
					$line --;
					continue;
				}
				$tmp = explode(' | ', $buffer);
				$users = explode(',', $tmp[1]);
				$item = array($tmp[0],count($users));
				$rst[] = $item;
				$now = time();
				if ($now - NOW_TIME > $this->_timeout) { // 超时
					return $this->_strTimeout;
				}
			}
			fclose ( $handle );
		} else {
			return NULL;
		}
		return $rst;
	}
	
	/**
	 * 获取IMEI活跃情况
	 * Enter description here ...
	 * @param unknown_type $day
	 */
	function getImeiActive ($s_day='',$e_day=''){
		$fileName = 'user_imei/stat.txt';
		if (empty($e_day)) { // 查询一天的数据
			$rst = $this->getStat($fileName, $s_day); // 最原始的返回
			if (is_array($rst)) {
				$result = array(); // 总共IMEI个数
				$ex_imei = 0; // 错误的IMEI
				$rst2 = array();
		    	for ($i=0; $i<24; $i++) {
		    		$rst2[$i] = 0;
		    	}
				foreach ($rst as $time=>$lev1) {
			    	$hour = intval(substr($time, -2));
			    	foreach ($lev1 as $pvc=>$lev2) {
			    		$total_num += count($lev2);
			    		$rst2[$hour] += count($lev2);
			    		foreach ($lev2 as $item) {
			    			$tmp = explode(' | ', $item);
			    			if (!isset($result[$tmp[1]]) && strlen($tmp[1])>20) {
			    				$ex_imei ++;
			    			}
			    			$result[$tmp[1]] = $tmp[0];
			    		}
			    	}
			    }
			    $data = $this->getTotalFile($fileName);
			    $data[$s_day] = array('total'=>count($result),'ex_imei'=>$ex_imei);
			    $this->setTotalFile($fileName, $data);
			    return $rst2;
			} else {
				return $rst;
			}
		} else { // 查询时间段数据
			$data = $this->getTotalFile($fileName);
			$s_time = strtotime($s_day);
			$e_time = strtotime($e_day);
			$rst = array();
			for ($i=$s_time; $i<=$e_time; $i+=86400) {
				$day = date('Y-m-d',$i);
				$rst[$day] = array('total'=>0,'ex_imei'=>0);
			}
			if (!empty($data)) {
				foreach ($data as $day=>$value) {
					if ($day>=$s_day && $day <= $e_day) { // 落在了查询区间
						$rst[$day] = $value;
					}
				}
			} else {
				return '没有你要的数据啊~~';
			}
			return $rst;
		}
	}
	
	/**
	 * 2014年5月26日17:58:02
	 * 获取活跃IP数
	 * Enter description here ...
	 * @param unknown_type $day
	 */
	function getIpActive($day) {
		$fileName = 'ip_active_user/stat.txt';
		$rst = $this->getStat($fileName, $day);
		if (is_array($rst)) {
			$result = array();
			foreach ($rst as $time=>$xx) {
				foreach ($xx as $pvc =>$xx2) {
					foreach ($xx2 as $key => $item) { // 渠道-时间-IP => 数据
						$tmp = explode($this->_ge,$item);
						$result[$pvc][$time][$tmp[0]] = $item[1]; 
					}
				}
			}
			return $result;
		} else {
			return $rst;
		}
	}
	
	/**
	 * 获取某一天分渠道的活跃用户
	 * Enter description here ...
	 * @param unknown_type $day
	 */
	function getPvcActiveMember($day){
		$fileName = 'pvc_active_user/stat.txt';
		$rst = $this->getStat($fileName, $day);
		if (is_array($rst)) {
			$result = array();
			foreach ($rst as $time=>$xx) {
				foreach ($xx as $pvc => $xx2) {
					foreach ($xx2 as $key => $item) {
						$result[$time][$pvc] = explode(',', $item);
//						$result[$time][$pvc] = substr_count($item,',')+1;
					}
				}
			}
			return $result;
		} else {
			return $rst;
		}
	}
	
	/**
	 * 获取渠道电影播放情况
	 * Enter description here ...
	 * @param unknown_type $day
	 */
	function getPvcMoviePlayed($day){
		$fileName = 'pvc_played_movie/stat.txt';
		$rst = $this->getStat($fileName, $day);
		if (is_array($rst)){
			$result = array();
			foreach ($rst as $time => $xx) { // 组织数据 电影-时间-pvc -- 数据
				foreach ($xx as $pvc => $xx2) {
					foreach ($xx2 as $key => $item) {
						$tmp = explode(',', $item);
						foreach ($tmp as $val) {
							$tmp2 = explode('|', $val);
							if (count($tmp2) != 4) {
								return $val.'附近有错哦';
							} 
							$result[$tmp2[0]][$time][$pvc] = array('play'=>$tmp2[1],'user'=>$tmp2[2],'max_p'=>$tmp2[3]);
						}
					}
				}
			}
			return $result;
		} else {
			return $rst;
		}
	}
	
	
	/**
	 * 获取电影播放情况
	 * Enter description here ...
	 * @param unknown_type $s_day
	 * @param unknown_type $e_day
	 */
	function getMoviePlayed($s_day='',$e_day=''){
		$dir = 'played_movie/';
		if (empty($e_day)) { // 为空，则代表只查询一天
			$rst = array ();
			for ($i=0; $i<24; $i++) {
				//str_pad($i,2,"0",STR_PAD_LEFT);
				$fileName = $dir . str_pad ( $i, 2, "0", STR_PAD_LEFT ) . '.txt';
				$handle = $this->openFile ( $fileName, $s_day );
				if ($handle != NULL) {
					$time = ''; //记录行的时间。
					for($line = 1; ! feof ( $handle ); $line ++) {
						$buffer = trim ( fgets ( $handle ) );
						if (empty ( $buffer )) {
							$line --;
							continue;
						}
						$tmp = explode(',', $buffer);
						foreach ($tmp as $val) {
							$tmp1 = explode('|', $val);
							if (count($tmp1) != 4) {return $i.' 小时 '.$val.'附近有错';}
							$rst[$i][] = array(
								'id' => $tmp1[0],
								'play' => $tmp1[1],
								'user' => $tmp1[2],
								'max_p' => $tmp1[3]	
							);
						}
						$now = time();
						if ($now - NOW_TIME > $this->_timeout) { // 超时
							return $this->_strTimeout;
						}
					}
					fclose ( $handle );
				} else {
					$rst[$i] = 0;
				}
			}
			return $rst;
		} else { // 查询时间区间
			$rst = array();
			$b_time = strtotime($s_day); $e_time = strtotime($e_day) + 1;
			for ($i = $b_time; $i<$e_time; $i+=86400) {
				$fileName = $dir . 'day.txt';
				$day = date('Y-m-d', $i);
				$handle = $this->openFile ( $fileName, $day );
				if ($handle != NULL) {
					$time = ''; //记录行的时间。
					for($line = 1; ! feof ( $handle ); $line ++) {
						$buffer = trim ( fgets ( $handle ) );
						if (empty ( $buffer )) {
							$line --;
							continue;
						}
						$tmp = explode(',', $buffer);
						foreach ($tmp as $val) {
							$tmp1 = explode('|', $val);
							if (count($tmp1) != 4) {return $i.' 小时 '.$val.'附近有错';}
							$rst[$i][] = array(
								'id' => $tmp1[0],
								'play' => $tmp1[1],
								'user' => $tmp1[2],
								'max_p' => $tmp1[3]	
							);
						}
						$now = time();
						if ($now - NOW_TIME > $this->_timeout) { // 超时
							return $this->_strTimeout;
						}
					}
					fclose ( $handle );
				} else {
					$rst[$day] = 0;
				}
			}
			return $rst;
		}
	}
	
	/**
	 * 这里获取电影列表用户习惯 
	 * @param unknown_type $day
	 */
	function getMovieList($day){
		$fileName = 'movie_list/stat.txt';
		$rst = $this->getStat($fileName, $day);
		if (is_array($rst)){
			$need_key = array (
				'p_id' => array (0, '' ), // 频道ID
				'z_id' => array (1, '' ), // 地区ZoneId
				'st_id'=> array (2, '' ), // 上映时间ShowTimeID
				't_id' => array (3, '' ), // 类型TagId
				'jin'  => array (4, '' ), // 是否精品，0全部 ，1精品
				'l_px' => array (5, '' ), // 用户请求影片列表的排序，0-最新，1-最热，2-最赞，3-热评
				'skip' => array (6, '' ), // skip
				'time' => array (7,''), //请求时间
				'post' => array	(8,''), // 请求次数
				'user' => array (9,''), // 请求用户数
				'u_max'=> array	(10,'') // 单个用户最大请求次数
			);
			$result = array();
			foreach ($rst as $time => $xx) { // 组织数据 第几屏幕-pvc-时间-各项数据
				foreach ($xx as $pvc => $xx2) {
					foreach ($xx2 as $key => $item) {
						$rst[$time][$pvc][$key] = array();
						$tmp = explode($this->_ge, $item);
						$p_num = ceil($tmp[6]/20) + 1; // 得到第几屏
						$tmp_x = array();
						foreach ($need_key as $key2=>$i) {
							$tmp_x[$key2] = $tmp[$i[0]];
						}
						$result[$p_num][$pvc][$time][] = $tmp_x; 
					}
				}
			}
			return $result;
		} else {
			return $rst;
		}
	}
	
	/**
	 * 获取接口错误日志
	 * @param unknown_type $day
	 */
	function getApiError($day){
		$dir = 'error/';
		$apis = C('API_LAN_NAME');
		$rst = array();
		$need_key = array (
			'time' => array (0, '' ), // 错误时间
			'u_id' => array (2, '' ), // 请求用户ID
			'st_id'=> array (3, '' ), // 上映时间ShowTimeID
			'plat' => array (4, '' ), // 平台
			'chan' => array (5, '' ), // 渠道
			'ver'  => array (6, '' ), // 版本
			'ip'   => array (7, '' ), // IP
			'us_t' => array (8,''), //耗时
			'j_len'=> array (9,''), // json长度
			'err'  => array (10,''), // 错误标记
			'j_sts'=> array	(11,'') // json 中status值
		);
		foreach ($apis as $api=>$name) { // 针对每个PAI循环
			$fileName = $dir.$api.".error.txt";
			$handle = $this->openFile($fileName, $day);
			for ($i=0; $i<24; $i++) { // 生成数据 小时-api- 其他信息
				$rst[$i][$api] = array();
			}
			if ($handle != NULL) {
				$time = 0;
				for($line = 1; ! feof ( $handle ); $line ++) {
					$buffer = trim ( fgets ( $handle ) );
					if (empty ( $buffer )) {
						$line --;
						continue;
					}
					$tmp = explode($this->_ge, $buffer);
					$tmp_value = array();
					foreach ($need_key as $key => $v) {
						$tmp_value[$key] = $tmp[$v[0]];
					}
					$t = date('G',strtotime($tmp[0]));
					$rst[$t][$api][] = $tmp_value;
					$now = time();
					if ($now - NOW_TIME > $this->_timeout) { // 超时
						return $this->_strTimeout;
					}
				}
				fclose ( $handle );
			}
		}
		return $rst;
	}
	
	/**
	 * 活跃用户查询
	 * 
	 * @param $s_day
	 * @param $e_day 为空代表查询一天数据，不为空，查询每天数据
	 */
	function getActiveMember($s_day='',$e_day=''){
		$dir = 'active_user/';
		if (empty($e_day)) { // 为空，则代表只查询一天
			$rst = array ();
			for ($i=0; $i<24; $i++) {
				//str_pad($i,2,"0",STR_PAD_LEFT);
				$fileName = $dir . str_pad ( $i, 2, "0", STR_PAD_LEFT ) . '.txt';
				$handle = $this->openFile ( $fileName, $s_day );
				if ($handle != NULL) {
					$time = ''; //记录行的时间。
					for($line = 1; ! feof ( $handle ); $line ++) {
						$buffer = trim ( fgets ( $handle ) );
						if (empty ( $buffer )) {
							$line --;
							continue;
						}
						$rst[$i] = count(explode(',', $buffer));
						$now = time();
						if ($now - NOW_TIME > $this->_timeout) { // 超时
							return $this->_strTimeout;
						}
					}
					fclose ( $handle );
				} else {
					$rst[$i] = 0;
				}
			}
			return $rst;
		} else { // 统计第几天到第几天的活跃用户量
			$rst = array();
			$b_time = strtotime($s_day); $e_time = strtotime($e_day) + 1;
			for ($i = $b_time; $i<$e_time; $i+=86400) {
				$fileName = $dir . 'day.txt';
				$day = date('Y-m-d', $i);
				$handle = $this->openFile ( $fileName, $day );
				if ($handle != NULL) {
					$time = ''; //记录行的时间。
					for($line = 1; ! feof ( $handle ); $line ++) {
						$buffer = trim ( fgets ( $handle ) );
						if (empty ( $buffer )) {
							$line --;
							continue;
						}
						$rst[$day] = count(explode(',', $buffer));
						$now = time();
						if ($now - NOW_TIME > $this->_timeout) { // 超时
							return $this->_strTimeout;
						}
					}
					fclose ( $handle );
				} else {
					$rst[$day] = 0;
				}
			}
			return $rst;
		}
	}
	
	/** 获取接口访问数据*/
	function getTotalStat($day='') {
		$need_key = array(
						'post' => array(1,'array_sum'), // 请求次数
						'memc' => array(2,'array_sum'), // memcache 请求次数
						'user' => array(3,'array_sum'), // 请求用户数
						'u_max'=> array(4,'max'), // 单个用户最大请求次数
						'err'  => array(5,'array_sum'), // 错误次数
						'json' => array(6,'array_sum'), // 该接口全部请求返回JSON的总计字符长度
						'j_max'=> array(7,'max'), // 该接口全部请求返回JSON中，最长的字符长度
						'j_min'=> array(8,'min'), // 该接口全部请求返回JSON中，最短的字符长度
						'time' => array(9,'array_sum'), // 该接口该时段请求总计耗时
						't_max'=> array(10,'max'),// 该接口该时段请求最大耗时
						't_min'=> array(11,'min') // 该接口该时段请求最小耗时
					);
		$fileName = 'total_stat/stat.txt';
		$rst = $this->getStat($fileName, $day);
		$return = array();
		$fuck = 0;
		if (is_array($rst)){
			foreach ($rst as $time => $xx) { // 转化数据 维度 为 接口名称-平台-渠道-版本-时间-统计次数
				foreach ($xx as $dvc=>$xx2) {
					foreach ($xx2 as $nums) {
						$tmp = explode($this->_ge, $nums);
						$apiName = $tmp[0]; // 接口名称
						foreach ($need_key as $key=>$i) {
							if (isset($return[$apiName][$dvc][$time][$key])){ // 如果存在就合并数据，正常情况下，不存在该情况
								$return[$apiName][$dvc][$time][$key] = $i[1](array($tmp[$i[0]],$return[$apiName][$dvc][$time][$key]));
							}else {
								$return[$apiName][$dvc][$time][$key] = $tmp[$i[0]];	
							}
						}
					}
				}
			}
			return $return;
		} else {
			return $rst;
		}
	}
	
	/**
	 * 通用获取方法
	 * Enter description here ...
	 */
	function getStat($fileName, $day=''){
		$handle = $this->openFile($fileName, $day);
		if ($handle != NULL) { 
			$time = ''; //记录行的时间。
			$pvc = ''; //记录行的平台渠道版本号
			$rst = array();
			for ($line = 1; !feof($handle); $line++) {
		        $buffer = trim(fgets($handle, $this->_lineBuffer)); 
		        if (empty($buffer)) {$line--;continue;}
		        $tmp = explode($this->_ge, $buffer);
		        if ($tmp[0] == '@TIME'){ // 时间
		        	if (count($tmp) != 5) {
		        		return "$fileName 中 $line 行格式不对";
		        	}
		        	$time = $tmp[1].$tmp[2].$tmp[3].$tmp[4];
		        } else if($tmp[0] == '@PCV'){
		        	if (count($tmp) != 4) {
		        		return "$fileName 中 $line 行格式不对";
		        	}
		        	$pvc = $tmp[1].$this->_ge.$tmp[2].$this->_ge.$tmp[3];
		        } else {
		        	if (empty($time) || empty($pvc)) { return "$fileName 中 $line 行格式不对"; }
		        	$rst[$time][$pvc][] = $buffer;
		        }
		        $now = time();
		        if ($now - NOW_TIME > $this->_timeout) { // 超时
		        	return $this->_strTimeout;
		        }
		    }
		    fclose($handle);
		    return $rst;
		} else {
			return NULL;
		}
	}
	
	function openFile($fileName, $day=''){
		$day = empty($day) ? toDate(NOW_TIME, 'Y-m-d') : $day;
		$day_xx = explode('-', $day);
		
		$file = '../../logger/'.$day_xx[0].$day_xx[1].'/'.$day.'/logger_manager_deal/'.$fileName;
		$handle = @fopen($file, "r");
		if ($handle) {
		    return $handle;
		} else {
			return NULL;
		}
	}
	
	function openFile2($fileName){
		$file = '../../logger/dwmynar_users/'.$fileName;
		$handle = @fopen($file, "r");
		if ($handle) {
		    return $handle;
		} else {
			return NULL;
		}
	}
	
	function getTotalFile($fileName){
		$file = '../../logger/total/'.$fileName;
		if (file_exists($file)){
			return json_decode(file_get_contents($file),true);
		} else {
			return array();
		}
	}
	
	function setTotalFile($fileName,$data) {
		$file = '../../logger/total/'.$fileName;
		$keys = array_keys($data);
		sort($keys);
		$new_data = array();
		foreach ($keys as $key) {
			$new_data[$key] = $data[$key];
		}
		file_put_contents($file, json_encode($new_data));
	}
}