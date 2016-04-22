<?php

// 根据gw2c 字符串得到可读信息
function getGW2CTxt($gw2c){
	$tmp = str_replace('gw2c://', '', $gw2c);
	$obj = json_decode($tmp, true);
	$txt = '';
	if ($obj['a'] == '1') {
		$txt = '打开画报【ID：'.$obj['p']['nid'].'】';
	} else if ($obj['a'] == '2') {
		$txt = '打开电影【ID：'.$obj['p']['mid'].'】';
	} else if ($obj['a'] == '3') {
		$txt = '打开广告【ID：'.$obj['p']['aid'].'】';
	} else if ($obj['a'] == '4') {
		$txt = '打开专题【ID：'.$obj['p']['pid'].'】';
	} else if ($obj['a'] == '5') {
		$txt = '打开个人中心【ID：'.$obj['p']['uid'].'】';
	} else if ($obj['a'] == '11') {
		$txt = '打开网页';
	} else if ($obj['a'] == '61') {
		$txt = '打开微图解【ID：'.$obj['p']['wid'].'】';
	} else if ($obj['a'] == '64') {
		$txt = '调用系统浏览器打开连接';
	} else if ($obj['a'] == '65') {
		$txt = '打开今日茶点';
	}
	return '<div onclick="$(this).next().toggle();">'.$txt.'</div><div style="display:none;">'.$gw2c.'</div>';
}

/**
 * 返回七牛处理结果url
 * @param unknown $url
 * @return string|unknown
 */
function getImageUrl($url){
	if (strstr($url, 'img7n1.graphmovie.com')) {
		return $url.'-mw800.jpg';
	} else {
		return $url;
	}
}

/**
 * html 内链转化
 * @param unknown_type $html
 */
function htmlExchangeOurUrl($html, $nei=true){
	// step1.先隐掉a标签
	preg_match_all('/<a[\s\S]*?>([\s\S]*?)<\/a>/i', $html, $match);
	$back_arr = array();
	if (!empty($match)) {
		$rep_str = 'gm_replace_'.NOW_TIME;
		foreach ($match[0] as $key=>$dom_a) {
			$back_arr[$rep_str.$key.'-end-'] = $dom_a;
			$html = str_replace($dom_a, $rep_str.$key.'-end-', $html);
		}
	}
	
	// step1.1 去掉img 去掉alt 和 title 属性 
	/*
	 * 这里解决img 等标签里含有 电影名称时被转化的可能 
	 */
	$html = preg_replace('#alt="[^"]*"#i',' ',$html);
	$html = preg_replace('#title="[^"]*"#i',' ',$html);
	
	// step1.2 更换img标签
	$html = preg_replace('#(<img)[\s\S]*?( src=\")([\s\S]*? \/>)#i',"\${1} class=\"img-scorll-load\" src=\"img/pixel.gif\" data-src=\" \${3} ",$html);
	
//	preg_match_all('/<img[\s\S]*? \/>/i', $html, $match);
//	if (!empty($match)) {
//		foreach ($match[0] as $dom_img) {
//			
//		}
//	}
	
	// step2.内容内链化
	if ($nei) {
		$map = array('played'=>array('GT',1000));
		$tmpMovies = D('Movie')->where($map)->getField('id,name');
		foreach ($tmpMovies as $id=>$name) {
			$sord_id[$id] = strlen($name);
		}
		arsort($sord_id);
		$MovieNames = array();
		foreach ($sord_id as $id=>$tmp) {
			$MovieNames[$tmpMovies[$id]] = $id;
		}
		unset($sord_id,$tmpMovies);
		$inner_html_list = array();
		$script_json_arr = array(); // 记录在A标签里的链接
		foreach ($MovieNames as $name=>$id) {
			$json = 'gw2c://{"v":"1","a":"2","p":{"mid":"'.$id.'"}}';
			//$html = str_replace($name, "<a href='$json'>$name</a>", $html);
			if(($position = strpos($html,$name)) !== false){
			    $leng = strlen($name);
			    // 这里遍历已经变化过的链接，如果老词变化过，则新词就不变了
			    $continue = FALSE;
			    foreach ($inner_html_list as $replace_name =>$tmp) {
			    	if(strpos($replace_name,$name) !== false && abs($position-$tmp)<10){
			    		$continue = true; break;
			    	}
			    }
			    if ($continue) { continue; }
			    
			    $script_json_arr[$id] = $json;
			    $rep_str = "<a href='$json' class='goto-movie'>";
			    $html = substr_replace($html,$rep_str."$name</a>",$position,$leng);
			    $cha_len = strlen($rep_str."</a>");
			    // 这里加上差值
			    foreach ($inner_html_list as $key=>$pre_position) {
			    	if ($pre_position > $position) {
			    		$inner_html_list[$key] += $cha_len;
			    	}
			    }
			    $inner_html_list[$name] = $position + strlen($rep_str);
			}
		}
	}
	
	// step3.把隐掉的a标签还原
	foreach ($back_arr as $key=>$dom_a) {
		$html = str_replace($key, $dom_a, $html);
	}
	
	preg_match_all('/\[movie:\d+\]/i', $html, $match);
	if (!empty($match)) {
		$movie_list = array();
		foreach ($match[0] as $movie_str) {
			$movie_id = str_replace(']', '', str_replace('[movie:', '', $movie_str));
			if (is_numeric($movie_id) && !isset($movie_list[$movie_id])) {
				$one = D('Movie')->where(array('id'=>$movie_id))->field('id,bpic,name,played,ding,comment_count,cellcover')->find();
				if (empty($one)) continue;
				$json = 'gw2c://{"v":"1","a":"2","p":{"mid":"'.$movie_id.'"}}';
				$script_json_arr[$movie_id] = $json;
				$movie_list[$movie_id] = true;
				$rep_str = '<div style="position:relative;line-height:0px;">'
								.'<a href=\''.$json.'\'><img class="img-scorll-load" src="img/pixel.gif" data-src="img/det_play.png" style="position:absolute;top:28%;left:34%;background:none;width:30%;"/></a>'
								.'<img class="img-scorll-load" style="width:100%" src="img/pixel.gif" data-src="'.getImgServerURL(8).'/graphmovie/'.$one['bpic'].'"/>'
								.($one['cellcover']>0?('<img style="position:absolute;right:-0.18%;top:2%;background:none;width:15%;" src="img/hc_rank_'.$one['cellcover'].'.png"/>'):'')
								.'<div style="line-height:2em;background-color:#111;background-color:rgba(10,10,10,0.8);position:absolute;bottom:0px;left:0.5%;width:99%; color:#fff;padding:0px 1em;">'
									.$one['name'] 
									.'<div style="float:right;font-size:0.7em;color:#aaa;">'
										.'<span style="margin-left:0.5em;background:url(\'img/hc_botbar_play.png\') no-repeat center left;background-size:auto 80%;padding-left:1.2em;">'.getFriendlyNum($one['played']).'</span>'
										.'<span style="margin-left:0.5em;background:url(\'img/hc_botbar_like.png\') no-repeat center left;background-size:auto 80%;padding-left:1.2em;">'.getFriendlyNum($one['ding']).'</span>'
										.'<span style="margin-left:0.5em;background:url(\'img/hc_botbar_comment.png\') no-repeat center left;background-size:auto 80%;padding-left:1.2em;">'.getFriendlyNum($one['comment_count']).'</span>'
									.'</div>'
								.'</div>'
							.'</div>';
				$html = str_replace($movie_str, $rep_str, $html);
			}
		}
	}
	
	$script_wei_json_arr = array();
	preg_match_all('/\[wei:\d+\]/i', $html, $match);
	if (!empty($match)) {
		$wei_list = array();
		foreach ($match[0] as $wei_str) {
			$wei_id = str_replace(']', '', str_replace('[wei:', '', $wei_str));
			if (is_numeric($wei_id) && !isset($wei_list[$wei_id])) {
				$one = D('Wei')->where(array('id'=>$wei_id))->field('id,pic,title,played,ding,comment_count')->find();
				if (empty($one)) continue;
				$json = 'gw2c://{"v":"1","a":"61","p":{"wid":"'.$wei_id.'"}}';
				$script_wei_json_arr[$wei_id] = $json;
				$wei_list[$wei_id] = true;
				if (strstr($one['pic'], 'img7n1.graphmovie.com')){
					$one['pic'] .= '-640.460.gm'; 
				}
				$rep_str = '<div style="position:relative;line-height:0px;">'
								.'<a href=\''.$json.'\'><img class="img-scorll-load" src="img/pixel.gif" data-src="img/det_play.png" style="position:absolute;top:28%;left:34%;background:none;width:30%;"/></a>'
								.'<img class="img-scorll-load" style="width:100%" src="img/pixel.gif" data-src="'.$one['pic'].'"/>'
								.'<div style="line-height:2em;background-color:#111;background-color:rgba(10,10,10,0.8);position:absolute;bottom:0px;left:0.5%;width:99%; color:#fff;padding:0px 1em;">'
									.$one['title']
									.'<div style="float:right;font-size:0.7em;color:#aaa;">'
										.'<span style="margin-left:0.5em;background:url(\'img/hc_botbar_play.png\') no-repeat center left;background-size:auto 80%;padding-left:1.2em;">'.getFriendlyNum($one['played']).'</span>'
										.'<span style="margin-left:0.5em;background:url(\'img/hc_botbar_like.png\') no-repeat center left;background-size:auto 80%;padding-left:1.2em;">'.getFriendlyNum($one['ding']).'</span>'
										.'<span style="margin-left:0.5em;background:url(\'img/hc_botbar_comment.png\') no-repeat center left;background-size:auto 80%;padding-left:1.2em;">'.getFriendlyNum($one['comment_count']).'</span>'
									.'</div>'
								.'</div>'
						  .'</div>';
				$html = str_replace($wei_str, $rep_str, $html);
			}
		}
	}
	
	
	$script_comment_json_arr = array();
	preg_match_all('/\[comment-movie:\d+\]/i', $html, $match);
	if (!empty($match)) {
		$comment_list = array();
		$user_list = array();
		foreach ($match[0] as $comment_str) {
			$comment_id = str_replace(']', '', str_replace('[comment-movie:', '', $comment_str));
			if (is_numeric($comment_id) && !isset($comment_list[$comment_id])) {
				$one = D('CommentMovie')->where(array('id'=>$comment_id))->field('user_id,comment_content')->find();
				if (empty($one)) continue;
				$user_id = $one['user_id'];
				$json = 'gw2c://{"v":"1","a":"5","p":{"uid":"'.userIdKeyEncode($user_id).'"}}';
				$script_comment_json_arr[$user_id] = $json;
				
				if (!isset($user_list[$user_id])) {
					$user_list[$user_id] = D('Member')->where(array('id'=>$user_id))->field('name,avatar')->find();
				}
				$str_user_name = $user_list[$user_id]['name'];
				$str_user_avatar = $user_list[$user_id]['avatar'];
				var_dump($user_list);
				
				$tmp = explode('//@{replyuserid:', $one['comment_content']);
				foreach ($tmp as $tmp2) {
					$tmp3 = explode('}', $tmp2);
					$user_id = $tmp3[0];
					if (!isset($user_list[$user_id])) {
						$user_list[$user_id] = D('Member')->where(array('id'=>$user_id))->field('name,avatar')->find();
					}
					$user_name = $user_list[$user_id]['name'];
					$json2 = 'gw2c://{"v":"1","a":"5","p":{"uid":"'.userIdKeyEncode($user_id).'"}}';
					$script_comment_json_arr[$user_id] = $json2;
					$one['comment_content'] = str_replace('//@{replyuserid:'.$user_id.'}', '<a href=\''.$json.'\'>//@'.$user_name.'</a>', $one['comment_content']);
				}
				
				$rep_str = '<div class="comment">'
								.'<div class="avatar"><a href=\''.$json.'\'><img src="'.$str_user_avatar.'"></a></div>'
								.'<div class="right">'
									.'<div class="user-name"><a href=\''.$json.'\'>'.$str_user_name.'</a></div>'
									.'<div style="height:4px;" class="clear"></div>'
									.'<div style="padding-top:7px;">'.$one['comment_content'].'</div>'
								.'</div>'
								.'<div class="clear"></div>'
						 .'</div><hr />';
				$html = str_replace($comment_str, $rep_str, $html);
			}
		}
	}
	
	
	$share_html = $html;
	$view_url = 'http://ser3.graphmovie.com/gmspanel/olr/detail.php?r=';
	foreach ($script_json_arr as $id=>$json) {
		$share_html = str_replace($json, $view_url.movieIdOnlineKeyEncode($id).',0,0,1', $share_html);
	}
	
	$wei_url = 'http://web.graphmovie.com/home/app/loadwei_see.php?k=';
	foreach ($script_wei_json_arr as $id=>$json) {
		$share_html = str_replace($json, $wei_url.urlencode(authcode(json_encode(array('wei_id'=>$id)),'ENCODE','123#@!123')), $share_html);
	}
	
	$user_url = 'javascript:void(0);';
	foreach ($script_comment_json_arr as $id=>$json) {
		$share_html = str_replace($json, $user_url, $share_html);
	}
	
	return array('html'=>$html,'share'=>$share_html);
}

/**
 * 添加配置信息
 * Enter description here ...
 * @param unknown_type $key
 * @param unknown_type $value
 * @param unknown_type $type
 */
function addConfigItem($key,$type,$value=''){
	$map = array('key'=>$key,'type'=>$type);
	$Config = D('Config');
	$tmp = $Config->where($map)->find();
	if (empty($tmp)) {
		$data = array('key'=>$key,'value'=>$value,'type'=>$type);
		$Config->create($data);
		$Config->add();
	}
}
/**
 * 获取配置信息
 * Enter description here ...
 * @param unknown_type $type
 */
function getConfigList($type){
	$map = array('type'=>$type);
	return D('Config')->where($map)->getField('key,value');
}

/**
 * 发送图片文件到指定服务器，并返回信息
 * Enter description here ...
 * @param unknown_type $type
 * @param unknown_type $id
 * @param unknown_type $files
 */
function sendFileToImgSevr($type, $id, $files){
	import("@.ORG.SingleFileSender");
	$file = array();
	$sfs = new SingleFileSender(C('POST_API.upload_img_serv'));
	$post = array();
	foreach ($files as $file) {
		$handle = @fopen($file['file'], "r");
    	$post[] = array(
    		"postName"=>$file['key'],
            "fileName"=>$file['file'],
            "file"=>fread($handle,filesize($file['file'])),
            "type"=>"image/pjpeg"
    	);
    	fclose($handle);
	}
	$post[] = array('name'=>"type","value"=>$type);
	$post[] = array('name'=>"id","value"=>$id);
	
	$rst = $sfs->post($post);
	if ($rst){
    	$content = $sfs->getContent();
    	$json = json_decode($content, true);
    	if (is_array($json)) {
    		if ($json['rst'] == '0') {
    			return $json['msg'];
    		} else {
    			return $json['data'];
    		}
    	} else {
    		return $content;
    	}
    } else {
    	return null;
    }
}

function toPercent($value, $dian=2){
	if (is_numeric($value)) {
		return round($value*100,$dian)."%";
	} else {
		return '0.0%';
	}
}

function getFriendlyNum($num,$deep=0){
	$zi = array('','万','亿');
	if ($num>9000) {
		$deep++;
		return getFriendlyNum($num/10000,$deep);
	}else {
		return round($num,1).$zi[$deep];
	}
}

function getFomartFileSize($size,$type='b'){
	$type = strtolower($type);
	$arr = array(
		'b' => array('','kb'),
		'kb' => array('b','mb'),
		'mb' => array('kb','gb'),
		'gb' => array('mb','tb'),
		'tb' => array('gb','')
	);
	if ($size>2048) { 
		//echo $size."$type<br />";
		if (empty($arr[$type][1])) {
			return round($size,2).ucfirst($type);
		}
		return getFomartFileSize(($size/1024), $arr[$type][1]);
	}elseif ($size<1) {
		if (empty($arr[$type][0])) {
			return round($size,2).ucfirst($type);
		}
		return getFomartFileSize($size*1024, $arr[$type][0]);
	} else {
		return round($size,2).ucfirst($type);
	}
}
function getHttpClient($url,$data,$type='post'){
	$type = strtolower($type);
	import("@.ORG.Net.HttpClient");
	$arr_url = parse_url($url);
	$port = isset($arr_url['port']) ? $arr_url['port'] : 80;
	$request = new HttpClient($arr_url['host'], $port);
	if ($type=="post") {
		$result = $request->post($arr_url['path'], $data);
	} else {
		$result = $request->get($arr_url['path'],$data);
	}
	if (empty($result)) {
		return NULL;
	} else {
		return $request;
	}
}


function getMovieCellcover($cellcover=0){
	$arr = array(
		'1'=>'<span class="greenBack" style="float:right;">略屌</span>', 
		'2'=>'<span class="blueBack" style="float:right;">震精</span>', 
		'3'=>'<span class="redBack" style="float:right;">神作</span>'
	);
	return isset($arr[$cellcover]) ? $arr[$cellcover] : ''; 
}

/**
 * 比较两个Tag是否相等
 * Enter description here ...
 * @param unknown_type $tags1
 * @param unknown_type $tags2
 * @return  
 * Array(
 *     'rst' => false, //  结果为false时，才有参数diff
 *     'diff' => array(
 *         array(), // 在tags1 中，tags2 木油
 *         array() // 在tags2 中，tags1 木油
 *     )
 * )
 */
function isEqueTags($tags1, $tags2, $ge="|"){
	$rst = array('rst'=>false);
	if ($tags1 == $tags2){
		$rst['rst'] = true;
		return $rst;
	}
	$tmp1 = explode($ge, $tags1);
	$tmp2 = explode($ge, $tags2);
	foreach ($tmp1 as $k1=>$v1) {
		foreach ($tmp2 as $k2=>$v2) {
			if ($v1 == $v2) {
				unset($tmp1[$k1], $tmp2[$k2]); break;
			}
		}
	}
	if (empty($tmp1) && empty($tmp2)) {
		$rst['rst'] = true;
		return $rst;
	} else {
		$rst['rst'] = false;
		$rst['diff'] = array($tmp1,$tmp2);
		return $rst;
	}
}

/**
 * 根据电影ID判断影片是否上线
 * Enter description here ...
 * @param unknown_type $movieId
 */
function isOnlineByMovieId($movieId){
	if (empty($movieId) && !is_numeric($movieId)) return false;
	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    $onlineMapPC = D('OnlineMapPC');
	$tmp = $onlineMapPC->where('id=1')->field('pub_channel,pub_platform,online_movie')->find();
	if (!empty($tmp)) {
		$online_movie = empty($tmp['online_movie']) ? array() : explode(',', $tmp['online_movie']);
		return in_array($movieId, $online_movie);
	}
	return false;
}

function mk_dir($folder) {
	$reval = false;
	
	if (! file_exists ( $folder )) {
		/* 如果目录不存在则尝试创建该目录 */
		@umask ( 0 );
		
		/* 将目录路径拆分成数组 */
		preg_match_all ( '/([^\/]*)\/?/i', $folder, $atmp );
		
		/* 如果第一个字符为/则当作物理路径处理 */
		$base = ($atmp [0] [0] == '/') ? '/' : '';
		
		/* 遍历包含路径信息的数组 */
		foreach ( $atmp [1] as $val ) {
			if ('' != $val) {
				$base .= $val;
				
				if ('..' == $val || '.' == $val) {
					/* 如果目录为.或者..则直接补/继续下一个循环 */
					$base .= '/';
					
					continue;
				}
			} else {
				continue;
			}
			
			$base .= '/';
			
			if (! file_exists ( $base )) {
				/* 尝试创建目录，如果创建失败则继续循环 */
				if (@mkdir ( rtrim ( $base, '/' ), 0777 )) {
					@chmod ( $base, 0777 );
					$reval = true;
				}
			}
		}
	} else {
		/* 路径已经存在。返回该路径是不是一个目录 */
		$reval = is_dir ( $folder );
	}
	clearstatcache ();
	return $reval;
}

/**
 * 获取图片服务器ID解说信息
 * Enter description here ...
 * @param unknown_type $imgserver_id
 */
function getScriptImgInfoUrl($id,$imgserver_id,$type="movies"){
	$url = '';
	switch ($imgserver_id) {
		case 2:
			$url = 'http://113.107.72.248:8099';
			break;
		case 4:
			$url = 'http://imgs3.graphmovie.com:8099';
			break;
		case 5:
			$url = 'http://imgs2.graphmovie.com:8099';
			break;
		case 6:
			$url = 'http://imgs2.graphmovie.com:8099';
			break;
		case 7:
			$url = 'http://imgs4.graphmovie.com';
			break;
		case 8:
			$url = 'http://imgs5.graphmovie.com';
			break;
	}
	if (!empty($url)) {
		$_private_key = '123!@#123';
		$key = md5($type.$id.$_private_key);
		$data = array('id'=>$id,'type'=>$type,'key'=>$key);
		$request = getHttpClient($url.C('POST_API.get_script_img_info'), $data);
		if (!empty($request)) {
			$rst = json_decode($request->getContent(),true);
			if (is_array($rst)) {
				if ($rst['rst'] == '1') {
					return $rst['data'];
				} else {
					return $rst['msg'];
				}
			}
		}
	}
	return null;
}


function otherURL2ServerUrl($other_url,$movieid,$imgserver_id){
	switch($imgserver_id){
			case 0:
				return $other_url;
				break;
			case 1:
				return otherURL_2_Server_1_URL($other_url,$movieid);
				break;
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
			case 7:
			case 8:
				return otherURL_2_Server_2_URL($other_url,$movieid,$imgserver_id);
				break;
			default:
				return $other_url;
				break;
		}
}

	//	替换url中的domin
	function otherURL_2_Server_1_URL($other_url,$movieid){
		
		if(strlen($other_url)==0 || $movieid<=0){
			return $other_url;
		}
		
		$img_name = getUrlFileName($other_url);
		
		return 'http://gaoqing.mobi/BackStage2/movies/'.$movieid.'/'.$img_name;
	}
	
	//替换url中的domin
	function otherURL_2_Server_2_URL($other_url,$movieid,$imgserver_id=2){
		
		if(strlen($other_url)==0 || $movieid<=0){
			return $other_url;
		}	
		$img_name = getUrlFileName($other_url);
		return getImgServerURL2($imgserver_id).'/movies/'.$movieid.'/'.$img_name;
	}
	
	function getUrlFileName($other_url){
		//图片名称
		$url_parts = explode('/',$other_url);
		if(count($url_parts)<=1){
			//没有找到'/',错误的URL
			return $other_url;
		}
		//最后一个元素即为图片名称
		return $url_parts[count($url_parts)-1];
	}

//获取服务器地址
function getImgServerURL($imgserverid) {
	switch ($imgserverid) {
		case 0 :
			return 'http://ser3.graphmovie.com/boo';
//			return 'http://gaoqing.mobi/BackStage2';
			break;
		case 1 :
			return 'http://ser3.graphmovie.com/boo';
//			return 'http://gaoqing.mobi/BackStage2';
			break;
		case 2 :
			return 'http://113.107.72.248:8099';
			break;
		case 3 :
			return 'http://ser3.graphmovie.com/boo';
			break;
		case 4:
			return 'http://imgs3.graphmovie.com:8099';
			break;
		case 5:
			return 'http://imgs2.graphmovie.com:8099';
			break;
		case 6:
			return 'http://imgs2.graphmovie.com:8099/graphmovie';
			break;
		case 7:
			return 'http://imgs4.graphmovie.com';
			break;
		case 8:
			return 'http://imgs5.graphmovie.com';
			break;
		default :
			return '';
	}
}

/**
 * 最新方法，由于老的处理有点问题。用这个新方法代替
 * Enter description here ...
 */
function getImgServerURL2($imgserverid) {
	switch ($imgserverid) {
		case 0 :
			return 'http://gaoqing.mobi/BackStage2';
			break;
		case 1 :
			return 'http://gaoqing.mobi/BackStage2';
			break;
		case 2 :
			return 'http://113.107.72.248:8099';
			break;
		case 3 :
			return 'http://ser3.graphmovie.com/boo';
			break;
		case 4:
			return 'http://imgs3.graphmovie.com:8099';
			break;
		case 5:
			return 'http://imgs2.graphmovie.com:8099';
			break;
		case 6:
			return 'http://imgs2.graphmovie.com:8099/graphmovie';
			break;
		case 7:
			return 'http://imgs4.graphmovie.com';
			break;
		case 8:
			return 'http://imgs5.graphmovie.com';
			break;
		default :
			return '';
	}
}

function getAdvCommentImgUrl($other_url,$adv_id,$imgserver_id){
	if ($imgserver_id < 2) {
		return $other_url;
	}
	return getImgServerURL2($imgserver_id) .'/adv/'. $adv_id.'/'.getUrlFileName($other_url);
}


// 获取渠道列表
function getChannelList($id = 0, $formartKey = 'key', $formartValue='val'){
	$channel = D('Channel');
	$map = array('open' => 1);
	$list = $channel->where($map)->field('id,name')->select();
	return getSelectList($id, $list, $formartKey, $formartValue);
}

function getSelectList ($id, $list, $formartKey = 'key', $formartValue='val'){
	$rst = '';
	$k = is_string($id) ? 'name' : 'id'; // 比较标签  这里不要用is_numeric($var) 
	if (!empty($list)) 
	foreach ($list as $item) {
		$selected = $id == $item[$k] ? "selected" : "";
		$key = str_replace('val', $item['name'],str_replace('key', $item['id'], $formartKey));
		$value = str_replace('val', $item['name'],str_replace('key', $item['id'], $formartValue));
		$rst .= '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
	}
	return $rst;
}

function getCheckBoxList ($ids, $list, $name="check[]", $formartKey = 'key', $formartValue='val'){
	$rst = '';
	$k = is_numeric($ids[0]) ? 'id' : 'name'; // 比较标签 
	if (!empty($list))
	foreach ($list as $item) {
		$checked = in_array($item[$k], $ids) ?'checked="checked"' : "";
		$key = str_replace('val', $item['name'],str_replace('key', $item['id'], $formartKey));
		$value = str_replace('val', $item['name'],str_replace('key', $item['id'], $formartValue));
		$rst = $rst . '<label style="width:100px;"><input type="checkbox" name="'.$name.'" class="check" value="'.$key.'" '.$checked.' />'.$value.'</label>';
	}
	return $rst;
}

// 获取电影类型 checkBox列表
function getMovieTypeCheckList($ids, $ge=',', $domName, $formartKey = 'key', $formartValue='val'){
	$ids = explode($ge, $ids);
	$type = D('MovieTag');
	$map = array('level' => 1, 'open' => 1);
	$list = $type->where($map)->field('id,name')->select();
	return getCheckBoxList($ids, $list, $domName, $formartKey, $formartValue);
}

// 获取电影二级标签 checkBox列表
function getMovieLevelCheckList($ids, $ge=',', $domName, $formartKey = 'key', $formartValue='val'){
	$ids = explode($ge, $ids);
	$type = D('MovieTag');
	$map = array('level' => 2, 'open' => 1);
	$list = $type->where($map)->field('id,name')->select();
	return getCheckBoxList($ids, $list, $domName, $formartKey, $formartValue);
}

// 获取平台列表
function getPlatformList($id = 0 ,$formartKey = 'key', $formartValue='val'){
	$platform = D('Platform');
	$map = array('open' => 1);
	$list = $platform->where($map)->field('id,name')->select();
	return getSelectList($id, $list, $formartKey, $formartValue);
}

// 获取电影类型列表
function getMovieTypeList($id = 0,$formartKey = 'key', $formartValue='val'){
	$type = D('MovieTag');
	$map = array('level' => 1, 'open' => 1);
	$list = $type->where($map)->field('id,name')->select();
	return getSelectList($id, $list, $formartKey, $formartValue);
}

// 获取电影区域列表
function getMovieZoneList($id = 0,$formartKey = 'key', $formartValue='val'){
	$zone = D('MZone');
	$list = $zone->field('id,name')->select();
	return getSelectList($id, $list, $formartKey, $formartValue);
}

// 获取电影上映时间列表
function getMovieShowtimeList($id = 0,$formartKey = 'key', $formartValue='val'){
	$showtime = D('MShowtime');
	$list = $showtime->field('id,name')->select();
	return getSelectList($id, $list, $formartKey, $formartValue);
}

// 返回字符串表示性别
function getSexTxt($sex=0){
	$arr = array('妖','男','女');
	return isset($arr[$sex]) ? $arr[$sex] : $arr[0];
}

// 获取当前的URL 用于操作返回上一层
function getCurrentUrl() {
	return Cookie::get('_currentUrl_');
}

function getItemSize($total, $pages){
	return sprintf("%.1f", (($total * 1024)/$pages));
}

//将数组转化为树形数组   
 function arrToTree($data,$pid){  
        $tree = array();  
        foreach($data as $k => $v){  
            if($v['pid'] == $pid){  
                $v['pid'] = arrToTree($data,$v['id']);  
                $tree[] = $v;  
            }  
        }     

        return $tree;  
 }

 //输出菜单
 function outNode($tree, $class=''){
    $html = '';
    foreach($tree as $key=>$t){
        $editurl=U('Node/edit',array('id'=>$t['id']));//__URL__/edit/id/'.$t['id'].'
        $addurl=U('Node/add',array('id'=>$t['id']));//__URL__/add/id/'.$t['id'].'
        $delurl=U('Node/foreverdelete',array('id'=>$t['id']));//__URL__/foreverdel/id/'.$t['id'].'
        if(empty($t['pid'])){
        	$display = strlen($class)<10 ? "":' style="display:none;"';
        	$html.='<tr class="row '.$class.'" '.$display.'>';
            $html.='<td><input type="checkbox" name="key" value="'.$t['id'].'"></td>';
            $html.='<td>'.$t['id'].'</td>';
            if($t['level']==1){
                 $html.='<td style="text-align:left;"><a style="padding-left: '.(($t['level']-1)*30).'px; font-weight: bold;" href="'.$editurl.'">'.$t['title'].'</a></td>';
            }  else {
                 $html.='<td style="text-align:left;"><a style="padding-left: '.(($t['level']-1)*30).'px;" href="'.$editurl.'">|- '.$t['title'].'</a></td>';
           
            }
            $html.='<td>'.$t['sort'].'</td>';
            $html.='<td align="center"> &nbsp;&nbsp;<a style="margin-left: 20px;" href="'.$addurl.'">添加子菜单</a>&nbsp;&nbsp;<a style="margin-left: 10px;" href="'.$editurl.'">修改</a>&nbsp;&nbsp;<a style="margin-left: 10px;" href="'.$delurl.'" onclick="foreverdel('.$t['id'].', this); return false;">删除</a></td>';
            $html.='</tr>';
        }else{
        	$click = empty($class) ? '' : ('onclick="$(\'.ooxx'.$t['id'].'\').toggle();"');
            $html.='<tr class="row '.$class.'">';
            $html.='<td><input type="checkbox" name="key" value="'.$t['id'].'"></td>';
            $html.='<td style="cursor:pointer;" '.$click.'>'.$t['id'].'</td>';
            if($t['level']==1){
                 $html.='<td style="text-align:left;"><a style="padding-left: '.(($t['level']-1)*30).'px; font-weight: bold;" href="'.$editurl.'">'.$t['title'].'</a></td>';
            }  else {
                 $html.='<td style="text-align:left;"><a style="padding-left: '.(($t['level']-1)*30).'px;" href="'.$editurl.'">|- '.$t['title'].'</a></td>';
            }
            $html.='<td>'.$t['sort'].'</td>';     
            $html.='<td align="center"> &nbsp;&nbsp;<a style="margin-left: 20px;" href="'.$addurl.'">添加子菜单</a>&nbsp;&nbsp;<a style="margin-left: 10px;" href="'.$editurl.'">修改</a>&nbsp;&nbsp;<a style="margin-left: 10px;" href="'.$delurl.'" onclick="foreverdel('.$t['id'].', this); return false;">删除</a></td>';
            $html.='</tr>';
            $html.=outNode($t['pid'], $class." ooxx".$t['id']);
        }
    }   
    return $html;  
 }
 
 
 
 //输出左侧栏目
 function outMenuNode($menu){
    $arr = array(); 
    foreach($menu as $m){
    	$icon = C('MENU_ICON.'.strtolower($m['name']));    
    	$arr[] = array(
       		'url' => U($m['name']."/index"),
    		'title' => $m['title'],
    		'icon' => empty($icon) ? C('MENU_ICON.default') : $icon,
    		'module' => $m['name']
       	);
    }   
    return $arr;  
 }
 
 //根据树形数组生成select控件
 function outNodeSelect($tree,$currentid){
     $html = '';  
    foreach($tree as $t){    
        if(empty($t['pid'])){
            if($currentid==$t['id']){
                $html.='<option value="'.$t['id'].'" selected="selected">';
            }else{
                $html.='<option value="'.$t['id'].'">';
            }
            
            for($i=1; $i<$t['level']; $i++) {
               $html.='....';
               if($i==$t['level']-1){
                   $html.='|- '; 
               }
            }
            $html.=$t['title'];
            $html.='</option>';
        }else{
            if($currentid==$t['id']){
                $html.='<option value="'.$t['id'].'" selected="selected">';
            }else{
                $html.='<option value="'.$t['id'].'">';
            }
            
            for($i=1; $i<$t['level']; $i++) {
               $html.='....'; 
               if($i==$t['level']-1){
                   $html.='|- '; 
               }
            }
            $html.=$t['title'];
            $html.='</option>';
            $html.=outNodeSelect($t['pid'],$currentid);
        }
    }   
    return $html;  
 }
 
 //权限组菜单输出
 function outMenu($tree,$nodeRoleList){  
    $html = '';  
    foreach($tree as $t){  
        if(in_array($t['id'],$nodeRoleList)){
            if(empty($t['pid'])){  
                $html .= '<li><label><input class="checkx" type="checkbox" name="menu_id[]" value="'.$t['id'].'" checked=true class="check" level="'.$t['level'].'">&nbsp;'.$t['title'].'</label></li>';
            }else{  
                $html .= '<li><label><input class="checkx" type="checkbox" name="menu_id[]" value="'.$t['id'].'" checked=true level="'.$t['level'].'"><span>&nbsp;'.$t['title'].'</span></label><ul style="padding-left:25px;">';   
                $html .=outMenu($t['pid'],$nodeRoleList);  
                $html  = $html.'</ul></li>';  
            }  
        }else{
            if(empty($t['pid'])){  
                $html .= '<li><label><input class="checkx" type="checkbox" name="menu_id[]" value="'.$t['id'].'" level="'.$t['level'].'">&nbsp;'.$t['title'].'</label></li>';
            }else{  
                $html .= '<li><label><input class="checkx" type="checkbox" name="menu_id[]" value="'.$t['id'].'" level="'.$t['level'].'"><span>&nbsp;'.$t['title'].'</span></label><ul style="padding-left:25px;">';   
                $html .=outMenu($t['pid'],$nodeRoleList);  
                $html  = $html.'</ul></li>';  
            } 
        }
    }   
    return $html;  
 }

//清空前台项目的缓存
function clearCache()
{
    import("@.ORG.Dir");
    if (is_dir(HOME_PATH."Runtime")){
        Dir::delDir(HOME_PATH."Runtime");
    }
    if (is_dir(APP_PATH."Runtime")){
        Dir::delDir(APP_PATH."Runtime");
    }
    if (is_dir("../".M_PATH."Runtime")){
        
        Dir::delDir("../".M_PATH."Runtime");
    }
}
//显示时间
function toDate($time, $format = 'Y-m-d H:i:s') {
	if (empty ( $time )) {
		return '';
	}
	$format = str_replace ( '#', ':', $format );
	return date ($format, $time );
}

// 缓存文件
function cmssavecache($name = '', $fields = '') {
	$Model = D ( $name );
	$list = $Model->select ();
	$data = array ();
	foreach ( $list as $key => $val ) {
		if (empty ( $fields )) {
			$data [$val [$Model->getPk ()]] = $val;
		} else {
			// 获取需要的字段
			if (is_string ( $fields )) {
				$fields = explode ( ',', $fields );
			}
			if (count ( $fields ) == 1) {
				$data [$val [$Model->getPk ()]] = $val [$fields [0]];
			} else {
				foreach ( $fields as $field ) {
					$data [$val [$Model->getPk ()]] [] = $val [$field];
				}
			}
		}
	}
	$savefile = cmsgetcache ( $name );
	// 所有参数统一为大写
	$content = "<?php\nreturn " . var_export ( array_change_key_case ( $data, CASE_UPPER ), true ) . ";\n?>";
	file_put_contents ( $savefile, $content );
}

function cmsgetcache($name = '') {
	return DATA_PATH . '~' . strtolower ( $name ) . '.php';
}

//根据ID获取角色名称
function getRole($id){
    if($id==0){
        return '超级管理员';
    }else if(empty ( $id )){
        return '未知角色';
    }
    
    $Role = D ( "Role" );
    $list = $Role->getField ( 'id,name' );
    $name = $list [$id];
    return $name;
}

//获取显示状态
function getShowStatus($status, $imageShow = false) {
    switch ($status) {
        case 0:
            $showText = '不显示';
            $showImg = '<img src="__PUBLIC__/Images/locked.gif" width="20" height="20" border="0" alt="不显示">';
            break;
        case - 1 :
            $showText = '删除';
            $showImg = '<img src="__PUBLIC__/Images/del.gif" width="20" height="20" border="0" alt="删除">';
            break;
        case 1 :
        default :
            $showText = '显示';
            $showImg = '<img src="__PUBLIC__/Images/ok.gif" width="20" height="20" border="0" alt="显示">';
    }
    return ($imageShow === true) ?  $showImg  : $showText;
}

//获取状态
function getStatus($status, $imageShow = false) {
	switch ($status) {
		case 0 :
			$showText = '禁用';
			$showImg = '<img src="__PUBLIC__/Images/locked.gif" width="20" height="20" border="0" alt="禁用">';
			break;
		case 2 :
			$showText = '待审';
			$showImg = '<img src="__PUBLIC__/Images/prected.gif" width="20" height="20" border="0" alt="待审">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<img src="__PUBLIC__/Images/del.gif" width="20" height="20" border="0" alt="删除">';
			break;
		case 1 :
		default :
			$showText = '正常';
			$showImg = '<img src="__PUBLIC__/Images/ok.gif" width="20" height="20" border="0" alt="正常">';

	}
	return ($imageShow === true) ?  $showImg  : $showText;

}
function getMessageStatus($status, $imageShow = false) {
	switch ($status) {
		case 0 :
			$showText = '未审核';
			$showImg = '<img src="__PUBLIC__/Images/locked.gif" width="20" height="20" border="0" alt="未审核">';
			break;
		case 2 :
			$showText = '未审核';
			$showImg = '<img src="__PUBLIC__/Images/prected.gif" width="20" height="20" border="0" alt="未审核">';
			break;
		case - 1 :
			$showText = '删除';
			$showImg = '<img src="__PUBLIC__/Images/del.gif" width="20" height="20" border="0" alt="删除">';
			break;
		case 1 :
		default :
			$showText = '已审核';
			$showImg = '<img src="__PUBLIC__/Images/ok.gif" width="20" height="20" border="0" alt="显示">';

	}
	return ($imageShow === true) ?  $showImg  : $showText;

}
function getOrderStatus($status, $imageShow = false) {
	switch ($status) {
                case - 1 :
			$showText = '删除';
			$showImg = '<img src="__PUBLIC__/Images/del.gif" width="20" height="20" border="0" alt="删除">';
			break;
		case 0 :
			$showText = '退货';
			$showImg = '<img src="__PUBLIC__/Images/locked.gif" width="20" height="20" border="0" alt="退货">';
			break;
		case 1 :
                        $showText = '<strong style="color:#487C09;">交易完成</strong>';
			$showImg = '<img src="__PUBLIC__/Images/ok.gif" width="20" height="20" border="0" alt="交易完成">';
                        break;
                case 2 :
			$showText = '<strong style="color:#0066CC;">未发货</strong>';
			$showImg = '<img src="__PUBLIC__/Images/prected.gif" width="20" height="20" border="0" alt="未发货">';
			break;
                case 3 :
			$showText = '<span style="color:#0066CC;">已发货</span>';
			$showImg = '<img src="__PUBLIC__/Images/prected.gif" width="20" height="20" border="0" alt="已发货">';
			break;
	
	}
	return ($imageShow === true) ?  $showImg  : $showText;

}

function getDefaultStyle($style) {
	if (empty ( $style )) {
		return 'blue';
	} else {
		return $style;
	}

}
function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array ();
	if (isset ( $_ip [$ip] )) {
		return $_ip [$ip];
	} else {
		import ( "ORG.Net.IpLocation" );
		$iplocation = new IpLocation ( $file );
		$location = $iplocation->getlocation ( $ip );
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function getNodeName($id) {
	if (Session::is_set ( 'nodeNameList' )) {
		$name = Session::get ( 'nodeNameList' );
		return $name [$id];
	}
	$Group = D ( "Node" );
	$list = $Group->getField ( 'id,name' );
	$name = $list [$id];
	Session::set ( 'nodeNameList', $list );
	return $name;
}

function getNodeGroupName($id) {
        if (empty ( $id )) {
		return '未分组';
	}
	if (isset ( $_SESSION ['nodeGroupList'] )) {
		return $_SESSION ['nodeGroupList'] [$id];
	}
	$Group = D ( "Group" );
	$list = $Group->getField ( 'id,title' );
	$_SESSION ['nodeGroupList'] = $list;
	$name = $list [$id];
	return $name;
}


/**
 +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
 +----------------------------------------------------------
 * @param string $fmode 文件名
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function build_verify($length = 4, $mode = 1) {
	return rand_string ( $length, $mode );
}

function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->select( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}


function sort_by($array, $keyname = null, $sortby = 'asc') {
	$myarray = $inarray = array ();
	# First store the keyvalues in a seperate array
	foreach ( $array as $i => $befree ) {
		$myarray [$i] = $array [$i] [$keyname];
	}
	# Sort the new array by
	switch ($sortby) {
		case 'asc' :
			# Sort an array and maintain index association...
			asort ( $myarray );
			break;
		case 'desc' :
		case 'arsort' :
			# Sort an array in reverse order and maintain index association
			arsort ( $myarray );
			break;
		case 'natcasesor' :
			# Sort an array using a case insensitive "natural order" algorithm
			natcasesort ( $myarray );
			break;
	}
	# Rebuild the old array
	foreach ( $myarray as $key => $befree ) {
		$inarray [] = $array [$key];
	}
	return $inarray;
}

function showStatus($status, $id) {
	switch ($status) {
            case 0 :
                //$info = '<a href="javascript:resume(' . $id . ')">恢复</a>';
                $url=U('User/resume',array('id'=>$id));
                $info ='<a style="margin-left: 20px;" href="'.$url.'">恢复</a>';
                break;
            case 2 :
                //$info = '<a href="javascript:pass(' . $id . ')">批准</a>';
                $url=U('User/pass',array('id'=>$id));
                $info ='<a style="margin-left: 20px;" href="'.$url.'">批准</a>';
                break;
            case - 1 :
                //$info = '<a href="javascript:recycle(' . $id . ')">还原</a>';
                $url=U('User/recycle',array('id'=>$id));
                $info ='<a style="margin-left: 20px;" href="'.$url.'">还原</a>';
                break;
            case 1 :
                //$info = '<a href="javascript:forbid(' . $id . ')">禁用</a>';
                $url=U('User/forbid',array('id'=>$id));
                $info ='<a style="margin-left: 20px;" href="'.$url.'">禁用</a>';
                break;
		
	}
	return $info;
}


/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) { //位数过长重复字符串一定次数
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		// 中文随机字
		for($i = 0; $i < $len; $i ++) {
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}
function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}
//随机产生数字和字母
function randstrletters($num){
    
    $strarray=array('a','b','c','d','e','f','g','h','i','g','k','l','m','n','o','p','q','i','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
    for($i=0;$i<$num;$i++){
        $strlettes.=$strarray[mt_rand(0,36)];
    }
    return $strlettes;
}

//生成二维码
function createQrCode($txt) {
    
    //二维码的保存地址
        $PNG_TEMP_DIR = '../Uploads';
        import('@.ORG.phpqrcode');
        
        if (!file_exists($PNG_TEMP_DIR))
            mkdir($PNG_TEMP_DIR);

        if(!empty($txt)&&trim($txt)!="")
        {
            $text= rawurldecode($txt);
            $filename = $PNG_TEMP_DIR.'/qrcode.png';
            QRcode::png($text, $filename, 'L', 6, 2);
            return true; 
            
            
        }
        return false;
}



// 参数解释
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