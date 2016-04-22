<?php
//凯撒加密解密
//用于在线看影片
function movieIdOnlineKeyEncode($movieid) {
	//先转换为16进制
	$movieid = dechex ( $movieid );
	//
	

	//不足8位补足8位
	$fill_8 = 8 - strlen ( $movieid );
	if ($fill_8 > 0) {
		while ( $fill_8 > 0 ) {
			$movieid = '0' . $movieid;
			$fill_8 --;
		}
	}
	
	$online_key = '';
	//开始逐个转码
	for($i = 0; $i < strlen ( $movieid ); $i ++) {
		$font = substr ( $movieid, $i, 1 );
		//if($font){
		$font = caesarEncode ( $font );
		$online_key .= $font;
	
		//}
	}
	
	return $online_key;
}

function movieIdOnlineKeyDecode($onlinekey) {
	//先转换为全大写
	$onlinekey = strtoupper ( $onlinekey );
	$movieid = '';
	//逐个解码
	for($i = 0; $i < strlen ( $onlinekey ); $i ++) {
		$font = substr ( $onlinekey, $i, 1 );
		//if($font){
		$font = caesarDecode ( $font );
		$movieid .= $font;
	
		//}
	}
	//转化为小写
	$movieid = strtolower ( $movieid );
	
	//十六进制转换为十进制
	$movieid = hexdec ( $movieid );
	
	return $movieid;
}

//凯撒加密解密
//用于处理用户ID
function userIdKeyEncode($userid) {
	//先转换为16进制
	$userid = dechex ( $userid );
	//
	

	//不足8位补足8位
	$fill_8 = 8 - strlen ( $userid );
	if ($fill_8 > 0) {
		while ( $fill_8 > 0 ) {
			$userid = '0' . $userid;
			$fill_8 --;
		}
	}
	
	$show_key = '';
	//开始逐个转码
	for($i = 0; $i < strlen ( $userid ); $i ++) {
		$font = substr ( $userid, $i, 1 );
		//if($font){
		$font = caesarStaticEncode ( $font );
		$show_key .= $font;
	
		//}
	}
	
	return $show_key;
}

function userIdKeyDecode($userkey) {
	//先转换为全大写
	$userkey = strtoupper ( $userkey );
	$userid = '';
	//逐个解码
	for($i = 0; $i < strlen ( $userkey ); $i ++) {
		$font = substr ( $userkey, $i, 1 );
		//if($font){
		$font = caesarStaticDecode ( $font );
		$userid .= $font;
	
		//}
	}
	
	//如果ID转化前后没变就不用再转换进制了
	//发的就是ID
	if ($userkey != $userid) {
		//转化为小写
		$userid = strtolower ( $userid );
		
		//十六进制转换为十进制
		$userid = hexdec ( $userid );
	}
	
	return $userid;
}

//凯撒转化
function caesarEncode($font) {
	$font = strtoupper ( $font );
	if ($font == '0') {
		$rand_num = rand ( 0, 5 );
		switch ($rand_num) {
			case 0 :
				return 'X';
			case 1 :
				return 'S';
			case 2 :
				return '7';
			case 3 :
				return '8';
			case 4 :
				return '9';
			case 5 :
				return '0';
			default :
				return 'S';
		}
	}
	if ($font == '1') {
		return rand ( 0, 1 ) > 0 ? 'H' : 'V';
	}
	if ($font == '2') {
		return rand ( 0, 1 ) > 0 ? 'E' : 'O';
	}
	if ($font == '3') {
		return rand ( 0, 1 ) > 0 ? 'G' : 'T';
	}
	if ($font == '4') {
		return rand ( 0, 1 ) > 0 ? 'M' : 'Y';
	}
	if ($font == '5') {
		return rand ( 0, 1 ) > 0 ? 'Z' : 'R';
	}
	if ($font == '6') {
		return rand ( 0, 1 ) > 0 ? 'W' : 'I';
	}
	if ($font == '7') {
		return rand ( 0, 1 ) > 0 ? 'B' : '6';
	}
	if ($font == '8') {
		return rand ( 0, 1 ) > 0 ? 'C' : '4';
	}
	if ($font == '9') {
		return rand ( 0, 1 ) > 0 ? 'A' : 'L';
	}
	if ($font == 'A' || $font == 'a') {
		return rand ( 0, 1 ) > 0 ? 'D' : 'N';
	}
	if ($font == 'B' || $font == 'b') {
		return rand ( 0, 1 ) > 0 ? 'J' : 'U';
	}
	if ($font == 'C' || $font == 'c') {
		return rand ( 0, 1 ) > 0 ? 'K' : '2';
	}
	if ($font == 'D' || $font == 'd') {
		return rand ( 0, 1 ) > 0 ? 'Q' : '3';
	}
	if ($font == 'E' || $font == 'e') {
		return rand ( 0, 1 ) > 0 ? 'F' : '5';
	}
	if ($font == 'F' || $font == 'f') {
		return rand ( 0, 1 ) > 0 ? 'P' : '1';
	}
	return $font;
}

function caesarDecode($font) {
	$font = strtoupper ( $font );
	if ($font == 'X' || $font == 'S' || $font == '7' || $font == '8' || $font == '9' || $font == '0') {
		return '0';
	}
	if ($font == 'H' || $font == 'V') {
		return '1';
	}
	if ($font == 'E' || $font == 'O') {
		return '2';
	}
	if ($font == 'G' || $font == 'T') {
		return '3';
	}
	if ($font == 'M' || $font == 'Y') {
		return '4';
	}
	if ($font == 'Z' || $font == 'R') {
		return '5';
	}
	if ($font == 'W' || $font == 'I') {
		return '6';
	}
	if ($font == 'B' || $font == '6') {
		return '7';
	}
	if ($font == 'C' || $font == '4') {
		return '8';
	}
	if ($font == 'A' || $font == 'L') {
		return '9';
	}
	if ($font == 'D' || $font == 'N') {
		return 'A';
	}
	if ($font == 'J' || $font == 'U') {
		return 'B';
	}
	if ($font == 'K' || $font == '2') {
		return 'C';
	}
	if ($font == 'Q' || $font == '3') {
		return 'D';
	}
	if ($font == 'F' || $font == '5') {
		return 'E';
	}
	if ($font == 'P' || $font == '1') {
		return 'F';
	}
	return $font;
}

//凯撒静态转化
//每次转化出来的编码是相同的
//用于处理userid
function caesarStaticEncode($font) {
	$font = strtoupper ( $font );
	if ($font == '0') {
		return '9';
	}
	if ($font == '1') {
		return 'H';
	}
	if ($font == '2') {
		return 'O';
	}
	if ($font == '3') {
		return 'G';
	}
	if ($font == '4') {
		return 'M';
	}
	if ($font == '5') {
		return 'Z';
	}
	if ($font == '6') {
		return 'W';
	}
	if ($font == '7') {
		return '6';
	}
	if ($font == '8') {
		return '4';
	}
	if ($font == '9') {
		return 'A';
	}
	if ($font == 'A' || $font == 'a') {
		return 'D';
	}
	if ($font == 'B' || $font == 'b') {
		return 'J';
	}
	if ($font == 'C' || $font == 'c') {
		return '2';
	}
	if ($font == 'D' || $font == 'd') {
		return '3';
	}
	if ($font == 'E' || $font == 'e') {
		return 'F';
	}
	if ($font == 'F' || $font == 'f') {
		return 'P';
	}
	return $font;
}

function caesarStaticDecode($font) {
	$font = strtoupper ( $font );
	if ($font == 'X' || $font == 'S' || $font == '7' || $font == '8' || $font == '9' || $font == '0') {
		return '0';
	}
	if ($font == 'H' || $font == 'V') {
		return '1';
	}
	if ($font == 'E' || $font == 'O') {
		return '2';
	}
	if ($font == 'G' || $font == 'T') {
		return '3';
	}
	if ($font == 'M' || $font == 'Y') {
		return '4';
	}
	if ($font == 'Z' || $font == 'R') {
		return '5';
	}
	if ($font == 'W' || $font == 'I') {
		return '6';
	}
	if ($font == 'B' || $font == '6') {
		return '7';
	}
	if ($font == 'C' || $font == '4') {
		return '8';
	}
	if ($font == 'A' || $font == 'L') {
		return '9';
	}
	if ($font == 'D' || $font == 'N') {
		return 'A';
	}
	if ($font == 'J' || $font == 'U') {
		return 'B';
	}
	if ($font == 'K' || $font == '2') {
		return 'C';
	}
	if ($font == 'Q' || $font == '3') {
		return 'D';
	}
	if ($font == 'F' || $font == '5') {
		return 'E';
	}
	if ($font == 'P' || $font == '1') {
		return 'F';
	}
	return $font;
}

