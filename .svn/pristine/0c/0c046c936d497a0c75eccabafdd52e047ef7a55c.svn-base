<?php
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


function otherURL2ServerUrl($other_url,$movieid,$imgserver_id){
	switch($imgserver_id){
			case 0:
				return $other_url;
				break;
			case 1:
				return otherURL_2_Server_1_URL($other_url,$movieid);
				break;
			case 2:
				return otherURL_2_Server_2_URL($other_url,$movieid);
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
	function otherURL_2_Server_2_URL($other_url,$movieid){
		
		if(strlen($other_url)==0 || $movieid<=0){
			return $other_url;
		}	
		$img_name = getUrlFileName($other_url);
		return 'http://113.107.72.248:8099/movies/'.$movieid.'/'.$img_name;
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
		default :
			return '';
	}
}

function getAdvCommentImgUrl($other_url,$adv_id,$imgserver_id){
	if ($imgserver_id < 2) {
		return $other_url;
	} elseif($imgserver_id > 3) {
		return '';
	}
	return getImgServerURL2($imgserver_id) . '/adv/'. $adv_id.'/'.getUrlFileName($other_url);
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
