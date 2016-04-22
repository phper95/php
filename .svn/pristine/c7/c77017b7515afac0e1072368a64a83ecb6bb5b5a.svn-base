<?php

class QiniuUp {
	static $accessKey = 'odHUHoZGHamGZIG1a-NrBar-tk6GOYBlh1r6Ay7R';
	static $secretKey = 'V0R_cfpI66xtfmbPqw7lWwQILohiHPGKjqlRuZxm';
	static $bucket = array('wei'=>'a619aj66');
	
	function upload($file){
		require_once("qiniu/io.php");
		require_once("qiniu/rs.php");
		
		$bucket = self::$bucket['wei'];  
		$key1 = null;
		
		Qiniu_SetKeys(self::$accessKey, self::$secretKey);
		$putPolicy = new Qiniu_RS_PutPolicy($bucket);
		$putPolicy->CallbackBody = 'bucket=$(bucket)&name=$(fname)&key=$(key)&fsize=$(fsize)&mimeType=$(mimeType)&exif=$(exif)&imageInfo=$(imageInfo)&imageAve=$(imageAve)&ext=$(ext)';
		$putPolicy->CallbackUrl = 'http://ser3.graphmovie.com/boo/interface/api/qi/cb.php';
		$upToken = $putPolicy->Token(null);
		
		$putExtra = new Qiniu_PutExtra();
		$putExtra->Crc32 = 1;
		list($ret, $err) = Qiniu_PutFile($upToken, $key1, $file, $putExtra);
		
		$rst = array('rst'=>0);
		if ($err !== null) {
		    $rst['err'] = $err->Err;
		} else {
			$rst['rst'] = 1;
		    $rst['data'] = $ret;
		}
		return $rst;
	}
}