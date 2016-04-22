<?php

	/**************************************************************
	* 
	*
	使用特定function对数组中所有元素做处理 
	* @param string &$array 要处理的字符串
	* @param string $function 要执行的函数 
	* @return boolean $apply_to_keys_also 是否也应用到key上 
	* @access public 
	* 
	*************************************************************/ 
	function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
		static $recursive_counter = 0;    
		if (++$recursive_counter > 1000) {        
			die('possible deep recursion attack');    
		}    
		foreach ($array as $key => $value) {        
			if (is_array($value)) {            
				arrayRecursive($array[$key], $function, $apply_to_keys_also);        
			} else {            
				$array[$key] = $function($value);        
			}        
			if ($apply_to_keys_also && is_string($key)) {             
				$new_key = $function($key);            
				if ($new_key != $key) {                
					$array[$new_key] = $array[$key];                
					unset($array[$key]);            
				}        
			}    
		}    
		$recursive_counter--; 
	} 
	
	/************************************************************** 
	* 
	* 将数组转换为JSON字符串（兼容中文）
	* @param array $array 要转换的数组 
	* @return string 转换得到的json字符串
	* @access public 
	* 
	*************************************************************/ 
	function JSON($array) { 
		arrayRecursive($array, 'urlencode', true); 
		$json = json_encode($array);
		return urldecode($json); 
	} 
	 
	function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
	{
		if ($code == 'UTF-8') {
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all ( $pa, $string, $t_string );
			if (count ( $t_string [0] ) - $start > $sublen)
				return join ( '', array_slice ( $t_string [0], $start, $sublen ) ) ;
			return join ( '', array_slice ( $t_string [0], $start, $sublen ) );
		} else {
			$start = $start * 2;
			$sublen = $sublen * 2;
			$strlen = strlen ( $string );
			$tmpstr = '';
			for($i = 0; $i < $strlen; $i ++) {
				if ($i >= $start && $i < ($start + $sublen)) {
					if (ord ( substr ( $string, $i, 1 ) ) > 129) {
						$tmpstr .= substr ( $string, $i, 2 );
					} else {
						$tmpstr .= substr ( $string, $i, 1 );
					}
				}
				if (ord ( substr ( $string, $i, 1 ) ) > 129)
					$i ++;
			}
			if (strlen ( $tmpstr ) < $strlen)
				$tmpstr .= '';
			return $tmpstr;
		}
	}

	function getFomartDate($time=NOW_TIME, $fomart='Y-m-d H:i:s'){
		return date($fomart, $time);
	}
	
	function getDjsBytime($time, $formart = 'd天h小时i分'){
		$str = 0;
		if ($time>0){
			$d = floor($time/86400);
			$las = $time % 86400;
			$h = floor($las / 3600);
			$las = $las % 3600;
			$i = floor($las / 60);
			$formart = str_replace('d', $d, $formart);
			$formart = str_replace('h', $h, $formart);
			$formart = str_replace('i', $i, $formart);
			$str = $formart;
		}
		return $str;
	}
	
	function getPitInfo($arr){
		$user = session('user');
		foreach ($arr as $key => $v) {
			$arr[$key]['right_b_str'] = $v['add_time'];
			switch ($v['state']) {
				case C('PIT_STATE.APPLY'): // 申请中
					$arr[$key]['info_str'] = '申请中';
					break;
				case C('PIT_STATE.PITTIN'): // 这里会有三个状态 （填坑ing，缓冲期，冻结期）
					$deadline = strtotime($v['deadline']);
					$cha_time = $deadline - strtotime(date('Y-m-d'));
					if ($cha_time > 864000){ // 大于 10 天  填坑ING
						$arr[$key]['right_str'] = '剩余 '.floor(($cha_time-864000)/86400).' 天';
						$arr[$key]['info_str'] = isset($v['user_name']) ? $v['user_name'] : (isset($user['name'])?$user['name']:'匿名君');
					}else if($cha_time > 604800){ // 大于 7 天 缓冲期 
						$arr[$key]['info_str'] = '缓冲期';
						$arr[$key]['right_b_str'] = '截稿：'.date('Y/m/d',($deadline-604800));
					}else if ($cha_time > 0) { // 冻结期
						$arr[$key]['info_str'] = '冷冻期开放投稿，按投稿时间先后上线。';
						$arr[$key]['right_str'] = '已冻结';
						$arr[$key]['right_b_str'] = '剩余：'.getDjsBytime($cha_time,'d天');
					} else { // 已经过了截稿期
						$arr[$key]['info_str'] = '审核中';
						$arr[$key]['right_str'] = '已过期';
						$arr[$key]['right_b_str'] = $v['deadline'];
						deadlinePit($v['id']);
					}
					break;
				case C('PIT_STATE.APPLY_UNDO'): // 申请弃坑
					$arr[$key]['info_str'] = '申请中';
					$arr[$key]['right_str'] = '弃坑申请';
					break;
				case C('PIT_STATE.APPLY_FAIL'):
					$arr[$key]['right_str']= '申请失败';
					$arr[$key]['info_str'] = $v['remark'];
					$arr[$key]['right_b_str'] = $v['add_time'];
					break;
				case C('PIT_STATE.APPLY_UNDO_SUCC') :
					$arr[$key]['right_str']= '弃坑';
					$arr[$key]['right_b_str'] = $v['add_time'];
					break;
				case C('PIT_STATE.APPLY_UNDO_FAIL') :
					$arr[$key]['right_str']= '弃坑失败';
					$arr[$key]['right_b_str'] = $v['add_time'];
					break;
				case C('PIT_STATE.DEADLINE') :
					$arr[$key]['right_str']= '过期';
					$arr[$key]['right_b_str'] = $v['add_time'];
					break;
				case C('PIT_STATE.SUCC') :
					$arr[$key]['right_str']= '作品已提交';
					$arr[$key]['right_b_str'] = $v['add_time'];
					break;
				default:
			}
		}
		return $arr;
	}
	
	function deadlinePit($id){
		$data['state'] = C('PIT_STATE.DEADLINE');
		$data['id'] = $id;
		$model = M('UserVPit');
		$model->save($data);
		
		$user_id = $model->where(array('id'=>$id))->getField('user_id');  
		$map = array('user_id'=>$user_id);
		$data = array(
			//'allow_pit'=>array('exp','allow_pit+1'),
			'doing_pit'=>array('exp','doing_pit-1'),
			'undone_pit'=>array('exp','undone_pit+1')
		);
		M('PitUser')->where($map)->save($data);
	}
	
	function isBadword($name, $arr=array()){
		$badword = include CONF_PATH.'badword.php';
    	$badword1 = array_combine($badword,array_fill(0,count($badword),'*'));
		$p_content = strtr($name, $badword1);
		if ($p_content != $name) {
			return '艾玛，不和谐啊（'.$p_content.'）！.';
		}
		
		$strlen = mb_strlen($p_content,'utf8'); 
		if($strlen==0){
			return '名字中含有非法字符哟！';
		} else if($strlen < 2) {
			return '名字太短啦[不能小于2个字了啦]！';
		} else if ($strlen > 15) {
			return  '名字太长啦[不能大于15个字了啦]！';
		}
		
		if (!empty($arr)) {
			foreach ($arr as $val) {
				if(mb_strpos($p_content,$val,0,'utf8')!==false){
					return '名字中不能使用"'.$val.'"哟！';
				}
			}
		}
		return true;
	}

	// 	参数解释
	// $string： 明文 或 密文
	// $operation：DECODE表示解密,其它表示加密
	// $key： 密匙
	// $expiry：密文有效期 (秒)
	function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
		$ckey_length = 4;
		
		// 密匙
		$key = md5 ( $key ? $key : 'boo' );
		
		// 密匙a会参与加解密
		$keya = md5 ( substr ( $key, 0, 16 ) );
		// 密匙b会用来做数据完整性验证
		$keyb = md5 ( substr ( $key, 16, 16 ) );
		// 密匙c用于变化生成的密文
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr ( $string, 0, $ckey_length ) : substr ( md5 ( microtime () ), - $ckey_length )) : '';
		// 参与运算的密匙
		$cryptkey = $keya . md5 ( $keya . $keyc );
		$key_length = strlen ( $cryptkey );
		// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
		// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
		$string = $operation == 'DECODE' ? base64_decode ( substr ( $string, $ckey_length ) ) : sprintf ( '%010d', $expiry ? $expiry + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
		$string_length = strlen ( $string );
		$result = '';
		$box = range ( 0, 255 );
		$rndkey = array ();
		// 产生密匙簿
		for($i = 0; $i <= 255; $i ++) {
			$rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
		}
		// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
		for($j = $i = 0; $i < 256; $i ++) {
			$j = ($j + $box [$i] + $rndkey [$i]) % 256;
			$tmp = $box [$i];
			$box [$i] = $box [$j];
			$box [$j] = $tmp;
		}
		// 核心加解密部分
		for($a = $j = $i = 0; $i < $string_length; $i ++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box [$a]) % 256;
			$tmp = $box [$a];
			$box [$a] = $box [$j];
			$box [$j] = $tmp;
			// 从密匙簿得出密匙进行异或，再转成字符
			$result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
		}
		if ($operation == 'DECODE') {
			// substr($result, 0, 10) == 0 验证数据有效性
			// substr($result, 0, 10) - time() > 0 验证数据有效性
			// substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
			// 验证数据有效性，请看未加密明文的格式
			if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {
				return substr ( $result, 26 );
			} else {
				return '';
			}
		} else {
			// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
			// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
			return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
		}
	}