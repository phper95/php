<?php
// 本类由系统自动生成，仅供测试用途
class Adv2Action extends CommonAction {
	public function _filter(&$map)
    {
        $name = I('name');
        if (!empty($name)) {
        	$map['name'] = array('like',"%$name%");
        }
    }
    
	public function index(){
		$type_list = D('Adv2Area')->getField('area,intro');
		$this->assign('typeList',$type_list);	
		parent::index();
	}
	
	public function _before_add(){
		$type_list = D('Adv2Area')->where(array('open'=>1))->getField('area,intro');
		$this->assign('typeList',$type_list);	
    }
    
	public function _before_edit(){
		$type_list = D('Adv2Area')->where(array('open'=>1))->getField('area,intro');
		$this->assign('typeList',$type_list);
		$id = I('id');
		if (empty($id) || !is_numeric($id)) {$this->error('参数错误');}
		$onlineMapPC = D('Adv2OnlineMap');
		$map = array ( 
			'id' => array ('neq',1),
			'online_map' => array('like',"%,$id,%")
		);
		$tmp_id = $onlineMapPC->where($map)->getField('id');
		if (!empty($tmp_id)) {
			$this->assign('online',1);
		}
    }
	
	public function add(){
		$this->display();
	}
	
	public function online(){
		$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$adv = D('Adv2')->getById($id);
    	if (empty($adv)){
    		$this->error('参数错误');
    	}
    	
    	if ($adv['area'] == 'A4') { // 针对4.0版本Banner走单独流程
    		$this->_online4($adv);
    		return;
    	}
    	
    	if ($adv['area'] == 'A5') { // 针对4.0版本Banner走单独流程
    		$this->_onlineX($adv);
    		return;
    	}
    	
    	if ($adv['area'] != 'T1' && empty($adv['pic'])) {
    		$this->error('图片还没上传呢，就别上线了吧');
    	}
    	
    	$platform = D('Platform');
		$map = array('open' => 1);
		$platformList = $platform->where($map)->getField('name,id');
		
		$channel = D('Channel');
		$channelList = $channel->where($map)->getField('name,id');
		
		$plVch = D('PlatformVChannel');
		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
		
		
		$onlineMapPC = D('Adv2OnlineMap');
		$map = array ( 
			'id' => array ('neq',1),
			'online_map' => array('like',"%,$id,%")
//			'_string' => "instr(CONCAT(';',`online_map`),';".$id.",')>0"
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform,online_map')->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		if (!empty($list)) {
			foreach ( $list as $val ) {
				$item = explode(';', $val['online_map']);
				foreach ($item as $item2) {
					$tmp = explode(',', $item2);
					if ($tmp[1] == $id) {
						$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $tmp[2]);
						$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
						break;
					} 
				}
			}
		}
		
		$this->assign('onlineList',$onlineList);
		$platformList = array_flip($platformList);
		$channelList = array_flip($channelList);
		$underlineList = array();
		foreach ($plVchList as $key=>$val) { // 去除已经上线的平台-渠道
			if (isset ($onlineArr[$val['platform_id'].'-'.$val['channel_id']])) {
				unset($plVchList[$key]);
			} else {
				$underlineList[ $platformList[ $val['platform_id'] ] ][] = $channelList[ $val['channel_id'] ];
			}
		}
		
		$this->assign('underlineList', $underlineList);
		
    	$this->display();
	}
	
	/**
	 * 4.0版本上线页面
	 */
	private function _online4($adv){
		
		$platform = D('Platform');
		$map = array('open' => 1);
		$platformList = $platform->where($map)->getField('name,id');
		
		$channel = D('Channel');
		$channelList = $channel->where($map)->getField('name,id');
		
		$plVch = D('PlatformVChannel');
		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
		
		
		$onlineMapPC = D('Adv2OnlineMap4');
		$map = array (
			'online_id' => $adv['id']
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform,start_time,end_time,tag,tag_color')->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		if (!empty($list)) {
			foreach ( $list as $val ) {
				$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'start_time' => $val['start_time'],'end_time'=>$val['end_time'],'tag'=>$val['tag'],'tag_color'=>$val['tag_color']);
				$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
			}
		}
		$this->assign('onlineList',$onlineList);
		$platformList = array_flip($platformList);
		$channelList = array_flip($channelList);
		$underlineList = array();
		foreach ($plVchList as $key=>$val) { // 去除已经上线的平台-渠道
			if (isset ($onlineArr[$val['platform_id'].'-'.$val['channel_id']])) {
				unset($plVchList[$key]);
			} else {
				$underlineList[ $platformList[ $val['platform_id'] ] ][] = $channelList[ $val['channel_id'] ];
			}
		}
		
		$this->assign('underlineList', $underlineList);
		
		$this->display('online4');
	}
	
	/**
	 * 4.0版本上线页面
	 */
	private function _onlineX($adv){
	
		$platform = D('Platform');
		$map = array('open' => 1);
		$platformList = $platform->where($map)->getField('name,id');
	
		$channel = D('Channel');
		$channelList = $channel->where($map)->getField('name,id');
	
		$plVch = D('PlatformVChannel');
		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
	
	
		$onlineMapPC = D('Adv2OnlineMapX');
		$map = array (
				'online_id' => $adv['id']
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform,start_time,end_time,icon,title,after_id')->select();
	
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		if (!empty($list)) {
			foreach ( $list as $val ) {
				$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'start_time' => $val['start_time'],'end_time'=>$val['end_time'],'after_id'=>$val['after_id'],'title'=>$val['title'],'icon'=>$val['icon']);
				$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
			}
		}
		$this->assign('onlineList',$onlineList);
		$platformList = array_flip($platformList);
		$channelList = array_flip($channelList);
		$underlineList = array();
		foreach ($plVchList as $key=>$val) { // 去除已经上线的平台-渠道
			if (isset ($onlineArr[$val['platform_id'].'-'.$val['channel_id']])) {
				unset($plVchList[$key]);
			} else {
				$underlineList[ $platformList[ $val['platform_id'] ] ][] = $channelList[ $val['channel_id'] ];
			}
		}
	
		$this->assign('underlineList', $underlineList);
		$this->assign('after',array('1'=>'精选图解','3'=>'片场'));
	
		$this->display('onlineX');
	}
	
	/**
     * 实实在在的上线操作
     * Enter description here ...
     */
    function doOnline(){
    	$online_list = I('online');
    	if (empty($online_list)) {$this->error('选择要上线的平台渠道啊');}
    	$adv_id = I('id');
    	if (empty($adv_id)) {$this->error('参数错误');}
    	
    	$adv = D('Adv2')->getById($adv_id);
    	if (empty($adv)) {
    		$this->error('擦嘞，广告被删了');
    	}
    	
    	if ($adv['area'] == 'A4') { // 4.0 专属上线
    		$this->_doOnline4($adv);
    		return;
    	}
    	
    	if ($adv['area'] == 'A5') { // 首页穿插专属上线
    		$this->_doOnlineX($adv);
    		return;
    	}
    	
    	$online_options = array(array(
    		'pub_platform' => 'all',
    		'pub_channel' => 'all',
    		'time' => toDate(NOW_TIME)
    	));
    	foreach ($online_list as $val) {
    		$time = I($val.',time','');
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1],
    			'time' => $time
    		);
    	}
    	
    	// 更新 adv2_online_map
    	$this->_updateOnlineMap($adv, $online_options);
    	$this->success('上线成功');
    }
    
    /**
     * 实实在在的上线操作
     * Enter description here ...
     */
    function _doOnline4($adv){
    	$online_list = I('online');
    	$online_options = array();
    	$time = toDate(NOW_TIME);
    	$etime = toDate(NOW_TIME + 86400);
    	
    	$adv2OnlineMap4 = D('Adv2OnlineMap4');
    	$list = $adv2OnlineMap4->where(array('online_id'=>$adv['id']))->field('pub_platform,pub_channel')->select();
    	$exit_platform = array();
    	if (!empty($list)) {
    		foreach ($list as $item){
    			$exit_platform[$item['pub_platform'].'-'.$item['pub_channel']] = true;
    		}
    	}
    	$exit_channel = array();
    	
    	foreach ($online_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		if (isset($exit_platform[$tmp[0].'-'.$tmp[1]])) {$exit_channel[] = $tmp[0].'-'.$tmp[1]; continue;}
    		$stime = I($val.',stime',$time);
    		$etime = I($val.',etime',$etime);
    		$online_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1],
    				'start_time' => empty($stime) ? $time : $stime,
    				'end_time' => empty($etime) ? $etime : $etime,
    				'online_id' => $adv['id'],
    				'tag' => I('tag',' '),
    				'tag_color' => I('tag_color','#00b9ff'),
    				'add_time' => $time
    		);
    	}
    	if (!empty($online_options)) {
    		if (false === $adv2OnlineMap4->addAll($online_options)) {
    			$this->error($adv2OnlineMap4->getError());
    		} else {
    			$this->success((empty($exit_channel) ? '上线成功' : (implode('; ', $exit_channel).' 这些个渠道已经上线了<br />其他渠道上线成功！')));
    		}
    	} else {
    		$this->success('没有选择渠道，或者选择的渠道已经上线');
    	}
    }
    
    /**
     * 实实在在的上线操作
     * Enter description here ...
     */
    function _doOnlineX($adv){
    	$online_list = I('online');
    	$online_options = array();
    	$time = toDate(NOW_TIME);
    	$etime = toDate(NOW_TIME + 86400);
    	 
    	$adv2OnlineMap4 = D('Adv2OnlineMapX');
    	$list = $adv2OnlineMap4->where(array('online_id'=>$adv['id']))->field('pub_platform,pub_channel')->select();
    	$exit_platform = array();
    	if (!empty($list)) {
    		foreach ($list as $item){
    			$exit_platform[$item['pub_platform'].'-'.$item['pub_channel']] = true;
    		}
    	}
    	$exit_channel = array();
    	 
    	foreach ($online_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		if (isset($exit_platform[$tmp[0].'-'.$tmp[1]])) {$exit_channel[] = $tmp[0].'-'.$tmp[1]; continue;}
    		$stime = I($val.',stime',$time);
    		$etime = I($val.',etime',$etime);
    		$online_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1],
    				'start_time' => empty($stime) ? $time : $stime,
    				'end_time' => empty($etime) ? $etime : $etime,
    				'online_id' => $adv['id'],
    				'icon' => I('icon',''),
    				'title' => I('title','推广'),
    				'after_id' => I('after_id',1),
    				'add_time' => $time
    		);
    	}
    	if (!empty($online_options)) {
    		if (false === $adv2OnlineMap4->addAll($online_options)) {
    			$this->error($adv2OnlineMap4->getError());
    		} else {
    			$this->success((empty($exit_channel) ? '上线成功' : (implode('; ', $exit_channel).' 这些个渠道已经上线了<br />其他渠道上线成功！')));
    		}
    	} else {
    		$this->success('没有选择渠道，或者选择的渠道已经上线');
    	}
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $adv
     * @param unknown_type $online_options
     */
    private function _updateOnlineMap($adv, $ppc_list) {
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$data_online_time = empty($ppc['time']) ? toDate(NOW_TIME) : (NOW_TIME > strtotime($ppc['time']) ? toDate(NOW_TIME) : ($ppc['time'].':00'));
    			
    			// 组合单条Map字段
    			$data_insert = $adv['area'].','.$adv['id'].','.$data_online_time;
    			
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$adv2OnlineMap = D('Adv2OnlineMap');
    			$list = $adv2OnlineMap->where($map)->field("id,online_map")->select();
    			if (empty($list)) { // 不存在则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					'online_map' => $data_insert
    				);
    				$adv2OnlineMap->create($data);
    				if (false === $adv2OnlineMap->add()) {
    					$this->error('插入渠道上线表出错');
    				}
    			} else { // 存在则跟新
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('adv2_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_adv = empty($list[0]['online_map']) ? array() : explode(';', $list[0]['online_map']);
    				
    				$tmp_value= array();
    				$insert_key = -1;
    				foreach ($online_adv as $key=>$item1) { // 修复数据
    					$tmp = explode(',', $item1);
    					// 如果存在ID相等，区域相等 的，就修复
    					if (isset($tmp_value[$tmp[0]]) || $tmp[1] == $adv['id'] || isset($tmp_value[$tmp[1]])) {
    						unset($online_adv[$key]);
    					} else {
    						if ($tmp[2] > $data_online_time) {
    							$insert_key = $key;
    						}
    						$tmp_value[$tmp[1]] = true;
    						$tmp_value[$tmp[0]] = true; // 区域广告上线为true
    					}
    				}
    				
    				if ($insert_key < 0){ // 插入最前头
    					array_unshift($online_adv, $data_insert);
    				} else {
    					$tmp_online_adv = array(); $tmp_online_adv_time = array();
    					foreach ($online_adv as $key=>$val) {
    						$tmp_online_adv[] = $online_adv[$key];
    						if ($key == $insert_key) { // 在这个Key后面插入
    							$tmp_online_adv[] = $data_insert;
    						}
    					}
    					$online_adv = $tmp_online_adv; $online_adv_time = $tmp_online_adv_time;
    					unset($tmp_online_adv, $tmp_online_adv_time);
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_map' => implode(';',$online_adv)
    				);
    				$adv2OnlineMap->create($data);
    				if (false === $adv2OnlineMap->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    			}
    		}
    	}
    }
    
 	/**
     * 实实在在的下线操作
     * Enter description here ...
     */
    public function doOffline(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$adv_id = I('id');
    	if (empty($adv_id)) {$this->error('参数错误');}
    	
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1]
    		);
    	}
    	
    	$adv = D('Adv2')->getById($adv_id);
    	if (empty($adv)) {$this->error('擦嘞，广告被删了');}
    	if ($adv['area'] == 'A4') {
    		$this->_doOffline4($adv,$offline_options);return;
    	}
    	
    	if ($adv['area'] == 'A5') {
    		$this->_doOfflineX($adv,$offline_options);return;
    	}
    	
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	
    	
    	$advOnlineMap = D('Adv2OnlineMap');
		$map = array (
			'id' => array ('neq',1),
			'online_map' => array('like',"%,$adv_id,%")
		);
		$list = $advOnlineMap->where($map)->field('pub_channel,pub_platform')->select();
		
		if (empty($list)) { 
			$this->error('还木油任何平台上线，就不能下线');
		} else {
			foreach ($list as $key=>$val) { // 如果list 为空的话，就代表是全部渠道下线
				if (in_array($val['pub_platform'].",".$val['pub_channel'], $offline_list)){
					unset($list[$key]);
				}
			}
		}
		
		$is_all_offline = empty($list);
		
		if ($is_all_offline) { // 如果是全部下线，则添加all平台，all渠道下线
			$offline_options[] = array(
	    		'pub_platform' => 'all',
	    		'pub_channel' => 'all'
	    	);
		}
		
		// 下线更新adv_online_map
    	$this->_offOnlineMap($adv, $offline_options);
    	
    	if ($is_all_offline) {
	    	// 下线更新用户信息
//	    	$this->_offAdvUser($adv);
    	}
    	$this->success('下线成功');
    }
    
    /**
     * 4.0实实在在的下线操作
     */
    private function _doOffline4($adv,$offline_options){
    	$advOnlineMap4 = D('Adv2OnlineMap4');
    	foreach ($offline_options as $item) {
    		$map = $item;
    		$map['online_id']=$adv['id'];
    		$advOnlineMap4->where($map)->delete();
    	}
    	$this->success('下线成功');
    }
    
    /**
     * 4.0实实在在的下线操作
     */
    private function _doOfflineX($adv,$offline_options){
    	$advOnlineMapX = D('Adv2OnlineMapX');
    	foreach ($offline_options as $item) {
    		$map = $item;
    		$map['online_id']=$adv['id'];
    		$advOnlineMapX->where($map)->delete();
    	}
    	$this->success('下线成功');
    }
    
	/**
     * 针对渠道下线广告
     * Enter description here ...
     * @param unknown_type $adv
     * @param unknown_type $ppc_list
     */
    private function _offOnlineMap($adv, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$advOnlineMap = D('Adv2OnlineMap');
    			$list = $advOnlineMap->where($map)->field("id,online_map")->select();
    			if (!empty($list)) { // 存在则删除，否则不管
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('adv_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_adv = empty($list[0]['online_map']) ? array() : explode(';', $list[0]['online_map']);
    				
    				$tmp_value= array();
    				foreach ($online_adv as $key=>$val) { // 修复数据
    					$tmp = explode(',', $val);
    					if (isset($tmp_value[$tmp[0]]) || $tmp[1] == $adv['id'] || isset($tmp_value[$tmp[1]])) {
    						unset($online_adv[$key]);
    					} else {
    						$tmp_value[$tmp[1]] = true;
    						$tmp_value[$tmp[0]] = true; // 区域广告上线为true
    					}
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_map' => implode(';',$online_adv)
    				);
    				$advOnlineMap->create($data);
    				if (false === $advOnlineMap->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    			}
    		}
    	}
    }
    
    /**
     * 更新广告脚本
     * Enter description here ...
     */
    public function updateScript (){
    	$id = I('id');
    	$rst = array('rst'=>0,'msg'=>'');
    	if (empty($id) || !is_numeric($id)) {
    		$rst['msg'] = '参数错误';
    		$this->ajaxReturn($rst);
    	}
    	$adv = D('Adv2');
    	if ($adv->create() === FALSE) {
    		$rst['msg'] = '修改失败1';
    		$this->ajaxReturn($rst);
    	}
    	if ($adv->save() === FALSE) {
    		$rst['msg'] = '修改失败2';
    		$this->ajaxReturn($rst);
    	} else {
    		$rst['rst'] = 1;
    		$this->ajaxReturn($rst);
    	}
    }
    
    
	
	public function uploadImg (){
		$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	
    	$vo = D('Adv2')->field('id,name,pic')->getById($id);
    	if (empty($vo)) {
    		$this->error('参数错误');
    	}
    	$this->assign('vo', $vo);
    	$this->display();
	}
	
	/**
     * 编辑封面图片
     * Enter description here ...
     */
    function doUploadImg(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$this->_uploadImg($id);
    	$model = D('Adv2');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功');
        } else {
            //错误提示
            $this->error('编辑失败！');
        }
    }
    
	/**
     * 上传封面图片
     * Enter description here ...
     * @param unknown_type $id
     */
	private function _uploadImg($id = 0){
    	if (!empty($_FILES)) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../adv2/'.$id;
          
    		if (!is_dir($dir)) { 
                mkdir($dir, 0777);
            }
            
            $dir .= '/';
            $upload->savePath = $dir;
            
            // 设置引用图片类库包路径
            $upload->imageClassPath = '@.ORG.Util.Image';
            
            $saveName = $id.'_'.date('YmdHis').'_';
            //删除原图
            $upload->thumbRemoveOrigin = true;
            
            $files = array();
            $server_pre = getImgServerURL(0)."/";
    		if (!empty($_FILES['pic']) && $_FILES['pic']['error'] != 4) { 
            	$upload->saveRule = $saveName;
            	$fileInfo = $upload->uploadOne($_FILES['pic']);
            	if ($fileInfo) {
            		$_POST ['pic'] = $server_pre.'adv2/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'pic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            	$nofile = false;
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (empty($files)) {
            	$this->error('没选择文件，就不要提交了嘛~~~');
            } else {
            	$rst = sendFileToImgSevr('adv2',$id,$files);
            	if (is_array($rst)) {
            		foreach ($files as $file) {
            			if (isset($rst['succ'][$file['key']])) {
            				$_POST[$file['key']] = $rst['succ'][$file['key']]['url'];
            			} else {
            				$this->error('上传图片服务器失败');
            			}
            		}
            	}else {
            		if (empty($rst)) {
            			$this->error('上传图片服务器错误');
            		} else {
            			$this->error($rst);
            		}
            	}
            }
    	}
    }
}