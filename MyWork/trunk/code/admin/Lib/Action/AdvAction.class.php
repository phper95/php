<?php
// 后台用户模块
class AdvAction extends CommonAction {
 	public function _filter(&$map)
    {
        $name = I('name');
        if (!empty($name)) {
        	$map['name'] = array('like',"%$name%");
        }
    }
	
    /**
     * 影片列表
     * @see CommonAction::index()
     */
    function index(){
//    	$rst = array();
//    	$rst['list'] = '列表';
//    	$rst['window'] = '窗口';
//    	$rst['fullscreen'] = '全屏';
//    	$rst['read'] = '阅读时';
//    	$rst['same'] = "相关推荐";
//    	$this->assign('adv_show_type',$rst);
    	
    	$online = I('online');
    	if ($online > 0) { // 已经上线 或 未上线的
    		$online_advs = $this->_getOnlineIdList(); // 获取已经上线的广告列表
    		$online_adv_ids = array_keys($online_advs); // 获取上线广告ID集合
    		
    		$orderby = I('orderby');
    		$field = (empty($orderby) || $orderby == 'online_time') ? 'id' : $orderby;
    		$order = I('order');
    		$order = empty($order) ? 'desc' : $order;
    		
    		$map = $this->_search(); // 获取查询条件
    		$this->_filter($map);
    		$adv = D('Adv');
    		$all_adv = $adv->where($map)->getField('id,'.$field, true);
    		$all_adv_ids = array_keys($all_adv);
    		$adv_ids = array();
    		if ($online == 1) { // 已经上线
    			$adv_ids = array_intersect($online_adv_ids, $all_adv_ids); // 求交集，得到满足条件的IDs
    		} elseif ($online == 2) { // 未上线
    			$adv_ids = array_diff($all_adv_ids, $online_adv_ids); // 求差集，得到满足条件的IDs
    		}
    		if (!empty($adv_ids)) {
    			if (empty($orderby) || $orderby == 'id') {
    				$order == 'desc' ? rsort($adv_ids) : sort($adv_ids);
    			} elseif ($orderby == 'online_time') { // 根据上线表情况来判断
    				if ($online == 1) { // 只有在已经上线的情况，才有按照上线时间排序
    					// 由于数据库表中的ID已经是通过上线时间顺序降序排列的,如果是升序排列，变换顺序即可
    					$order == 'desc' ? array_values($adv_ids) : array_reverse($adv_ids);	
    				}
    				
    			} else {  // 根据adv表的字段情况来判断
    				// 由于从adv表中查出来的顺序是乱序的，所以这部分需要自己排序了
    				$sort_ids = array();
    				foreach ($adv_ids as $id) { 
    					$sort_ids[$id] = $all_adv[$id];
    				}
    				$order == 'desc' ? arsort($sort_ids) : asort($sort_ids);
    				$adv_ids = array_keys($sort_ids);
    			}
    			$this->_getAdvList($adv_ids, $map);
    		}
    		$this->display();
    	} else {
    		parent::index();
    	}
    }
    
    /**
     * 获取列表数据
     * Enter description here ...
     * @param unknown_type $ids
     */
    private function _getAdvList($ids, $map, $sort='desc', $order='id'){
    	$count = count($ids);
    	$ids = array_values($ids);
    	
    	if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            
            $from = $p->firstRow; // 开始处， 
            $to = $from + $p->listRows; // 结束处
            
            $select_ids = array();
            for ($i=$from; $i < $to; $i++) {
            	if (isset($ids[$i])){
            		$select_ids[] = $ids[$i];
            	} else {break;}
            }
            $voList = array();
            if (!empty($select_ids)) {
            	$where = array('id'=>array('in', $select_ids));
            	$list = D('Adv')->where($where)->select(); // 这里查出来是乱序的，所以需要进行处理
            	if (!empty($list)) {
            		$tmpList = array();
            		foreach ($list as $item) {
            			$tmpList[$item['id']] = $item;
            		}
            		foreach ($select_ids as $val) {
            			$voList[] = $tmpList[$val];
            		}
            	}
            }

            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        Cookie::set('_currentUrl_', __SELF__);
        return;
    }
    
    /**
     * 获取已经上线的广告ID列表
     * Enter description here ...
     */
    private function _getOnlineIdList(){
    	$rst = array();
    	$map = array();
    	$platform = I('platform'); // 平台
    	$channel = I('channel'); // 渠道
    	if ($platform == 'all' && $channel != 'all') { // 
    		$map['pub_channel'] = $channel;
    	}else if ($channel == 'all') {
    		$map['pub_platform'] = $platform;
    	} else {
    		$map['pub_platform'] = $platform;
    		$map['pub_channel'] = $channel;
    	}
    	
    	$typeList = array('list', 'window', 'fullscreen', 'read', 'same'); // 顺序也比较重要 
    	
    	$advOnlineMap = D('AdvOnlineMap'); // 各个平台渠道上线模型
    	$list = $advOnlineMap->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			foreach ($typeList as $type) {
	    			$adv_ids = explode(',', $item[$type.'_online_adv']);
	    			$online_times = explode(',', $item[$type.'_online_time']);
	    			foreach ($adv_ids as $key => $val) { // 直接用 key=value 免去求交集的烦恼。
	    				$rst[$val] = $online_times[$key]; // 组成adv_id => 上线时间， 这样一个数组
	    			}
    			}
    		}
    	}
    	return $rst;
    } 
    
    /**
     * 插入数据前，做一个判断
     * Enter description here ...
     */
    function _before_insert(){
    	$arr = array('show_list','show_window','show_search','show_tag','show_qrcode','show_same','show_grapher','show_read','show_fullscreen','show_detail_fullscreen','show_detail_window','show_detail_list','show_detail_read','show_detail_same');
    	foreach ($arr as $val) {
    		$avg = I($val);
    		$_POST[$val] = !empty($avg) ? $avg : 0;
    	}
    }
    
    /**
     * 同理，更新也要
     * Enter description here ...
     */
    function _before_update(){
    	$arr = array('show_list','show_window','show_search','show_tag','show_qrcode','show_same','show_grapher','show_read','show_fullscreen','show_detail_fullscreen','show_detail_window','show_detail_list','show_detail_read','show_detail_same');
    	foreach ($arr as $val) {
    		$avg = I($val);
    		$_POST[$val] = !empty($avg) ? $avg : 0;
    	}
    }
    
	function update() {
        $model = D('Adv');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $data = $model->data();
        $map = array('id'=>$data['id']);
        $old_data = $model->where($map)->find();
        if (empty($old_data)) {
        	$this->error('参数错误');
        }
        
        $update = false; // 是否更新Map
        
        $typeList = array('show_list', 'show_window', 'show_fullscreen', 'show_read', 'show_same');
        $type2type = array( // adv 字段，对应  adv_online_map 字段
        	'show_list' => array('id'=>'list_online_adv', 'time'=>'list_online_time'), 
        	'show_window'=>array('id'=>'window_online_adv', 'time'=>'window_online_time'),
        	'show_fullscreen'=>array('id'=>'fullscreen_online_adv', 'time'=>'fullscreen_online_time'),
        	'show_read'=>array('id'=>'read_online_adv', 'time'=>'read_online_time'),
        	'show_same'=>array('id'=>'same_online_adv', 'time'=>'same_online_time')
        );  
        $online_type = array(); //需要上线的字段
        $offline_type = array(); //需要下线的字段
        
        foreach ($typeList as $type) {
        	if ($old_data[$type] != I($type)) {
        		$update = true;
        		if ($old_data[$type] == 0) { // 从0到1的装换，即需要上线
        			$online_type[] = $type2type[$type];
        		} else { // 从1到0的转换，即，需要下线
        			$offline_type[] = $type2type[$type];
        		}
        	}
        }
        
        if ($update) { // 判断是否在线，如果不在线，则不更新啦
        	$update = false;
        	$advOnlineMap = D('AdvOnlineMap');
        	$field = 'fullscreen_online_adv,window_online_adv,list_online_adv,read_online_adv,same_online_adv';
			$tmp = $advOnlineMap->where('id=1')->field($field)->find();
			$fields = explode(',', $field);
			if (!empty($tmp)) {
				foreach ($fields as $f) {
					$online_adv = empty($tmp[$f]) ? array() : explode(',', $tmp[$f]);
					if (in_array($data['id'], $online_adv)){
						$update = true; break; // 上线了，标记，跳出
					}
				}
			}
        }
        
        if ($update) { // 需要修改adv_online_map
        	$id = $data['id'];
        	// step1. 查出该广告所有已经上线的adv_online_map 的ID
        	$map = array ( // 四种情况  id | id, | ,id, | ,id
				'fullscreen_online_adv' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\''),
        		'window_online_adv' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\''),
	        	'list_online_adv' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\''),
	        	'read_online_adv' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\''),
	        	'same_online_adv' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\''),
        		'_logic' => 'OR'
			);
				
			$list = $advOnlineMap->where($map)->select();
			
			// step2. 找出之前排在第几位(这里找不出，之前第几位，与哪一列相同，不能确定，所以与第一列all相同)
			$options = array();
			foreach ($list as $tmp) { // 每条记录循环
				foreach ($fields as $f) { // 每个字段判断
					$find = false;
					$online_adv = empty($tmp[$f]) ? array() : explode(',', $tmp[$f]);
					if (!empty($online_adv)){
						foreach ($online_adv as $key=>$adv_id) {
							if ($adv_id == $id) {
								$options[] = array(
									'pub_platform' => $tmp['pub_platform'],
					    			'pub_channel' => $tmp['pub_channel'],
					    			'position' => ($key+1)
								);
								$find = true;
								break;
							}
						}
					}
					if ($find){break;}
				}
			}
			
			if (!empty($online_type))
        	foreach ($online_type as $type) { // 需要上线的字段渠道，平台
//        		echo $data['id'];print_r($type); print_r($options);
        		$this->_updateOnlineMap($data, $options, $type['id'], $type['time']);
        	}

        	if (!empty($offline_type))
        	foreach ($offline_type as $type) { // 需要下线的字段渠道，平台
        		$this->_offOnlineMap($data, $options, $type['id'], $type['time']);
        	}
        }
        
        // 更新数据
        $list = $model->save($data);
        if (false !== $list) {
//        	$this->display('index');
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
    
    
    function getOnlineTypesByAdv($adv){
    	$rst = array();
    	if ($adv['show_list'] == 1) {
    		$rst['list'] = '列表';
    	}
    	if ($adv['show_window'] == 1) {
    		$rst['window'] = '窗口';
    	}
    	if ($adv['show_fullscreen']) {
    		$rst['fullscreen'] = '全屏';
    	}
    	if ($adv['show_read']) {
    		$rst['read'] = '阅读时';
    	}
    	if ($adv['show_same']) {
    		$rst['same'] = "相关推荐";
    	}
    	return $rst;
    }
    
    // 影片上线页面
    function online(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$adv = D('Adv')->getById($id);
    	if (empty($adv)){
    		$this->error('参数错误');
    	}
    	$_fiter = array(
//    		'total_size'=>'广告大小为空，不允许该操作',
//    		'total_page' => '影片脚本为空，不允许上线',
    		'grapher' => '作者信息为空，请先绑定作者',
//    		'bpic' => '还木油上传大~封面',
//    		'spic' => '还木油上传大~封面'
    	);
    	foreach($_fiter as $key=>$val) {
    		if (empty($adv[$key])) {
    			$this->error($val);
    		}
    	}
    	
    	if ($adv['show_fullscreen'] == 1 && empty($adv['fullscreen_show_pic'])) {
   			$this->error('广告图，没上传啊。');
   		}
    	
    	$typeList = $this->getOnlineTypesByAdv($adv);
    	if (empty($typeList)) {
    		$this->error('该片不符合上线标准');
    	}
    	
    	$this->assign('typeList', $typeList);
    	
    	$platform = D('Platform');
		$map = array('open' => 1);
		$platformList = $platform->where($map)->getField('name,id');
		
		$channel = D('Channel');
		$channelList = $channel->where($map)->getField('name,id');
		
		$plVch = D('PlatformVChannel');
		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
		
		$xx = array();
		foreach ($typeList as $key=>$val) {
			$xx[$key] = array('on'=>$val.'OnList', 'off'=>$val.'OffList');
			$this->_assignOnlineList($id, $platformList, $channelList, $plVchList, $key);
		}
		$this->assign('xx',$xx);
    	$this->display();
    }
    
    private function _assignOnlineList($id, $platformList, $channelList, $plVchList, $ptype){
    	$advOnlineMap = D('AdvOnlineMap');
    	
    	$online_adv = $ptype.'_online_adv';
    	$online_times = $ptype.'_online_time';
    	
    	$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			$online_adv => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\'')
		);
		$list = $advOnlineMap->where($map)->field('pub_channel,pub_platform,'.$online_adv.','.$online_times)->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		foreach ( $list as $val ) {
			$adv_ids = explode(',', $val[$online_adv]);
			$adv_times = explode(',', $val[$online_times]);
			$online_time = '';
			foreach ($adv_ids as $key => $ids) {
				if ($ids == $id) {
					$online_time = $adv_times[$key];
				}
			}
			
			$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $online_time);
			$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true; 
			
		}
		$this->assign($ptype.'OnList',$onlineList);
		
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
		
		$this->assign($ptype.'OffList', $underlineList);
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
    	$type = I('type');
    	if (empty($type)) {$this->error('类型错误');}
    	
    	$online_options = array(array(
    		'pub_platform' => 'all',
    		'pub_channel' => 'all',
    		'position' => 0
    	));
    	foreach ($online_list as $val) {
    		$option = I($val,0);
    		$time = I($val.',time','');
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1],
    			'position' => is_numeric($option) ? abs($option) : 0,
    			'time' => $time
    		);
    	}
    	
    	$adv = D('Adv')->getById($adv_id);
    	if (empty($adv)) {
    		$this->error('擦嘞，广告被删了');
    	} else {
    		if ($type == 'fullscreen' && $adv['show_fullscreen'] == 1 && empty($adv['fullscreen_show_pic'])) {
    			$this->error('广告图，没上传啊。');
    		}
    	}
    	
    	$online_adv = $type.'_online_adv';
    	$online_times = $type.'_online_time';
    	
    	// 更新 adv_online_map
    	$this->_updateOnlineMap($adv, $online_options, $online_adv, $online_times);
    	// 更新用户相关信息 -- 暂时不需要这步操作
//    	$this->_updateAdvUser($adv);
    	$this->success('上线成功');
//    	$this->display('index');
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
    	$type = I('type');
    	if (empty($type)) {$this->error('类型错误');}
    	
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1]
    		);
    	}
    	
    	$adv = D('Adv')->getById($adv_id);
    	if (empty($adv)) {$this->error('擦嘞，广告被删了');}
    	
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	
    	$online_adv = $type.'_online_adv';
    	$online_times = $type.'_online_time';
    	
    	$advOnlineMap = D('AdvOnlineMap');
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			$online_adv => array('exp',' REGEXP \'^'.$adv['id'].'$|^'.$adv['id'].',|,'.$adv['id'].',|,'.$adv['id'].'$\'')
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
    	$this->_offOnlineMap($adv, $offline_options, $online_adv, $online_times);
    	
    	if ($is_all_offline) {
	    	// 下线更新用户信息
//	    	$this->_offAdvUser($adv);
    	}
    	$this->success('下线成功');
    }
    
    
    /**
     * 更新平台渠道上线列表
     * Enter description here ...
     * @param Array $adv 一条广告记录 
     * @param Array $ppc
			ppc_list = array(
				0=>array(
					'pub_platform'=0,
					'pub_channel'=0,
					'position'=0,
				),
			)
	 * @param $online_advs
	 * @param $online_times
     */
    private function _updateOnlineMap($adv, $ppc_list, $online_advs, $online_times){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		$now_time = date('Y-m-d H:i:s', NOW_TIME);
    		$dead_line = date('Y-m-d H:i:s', strtotime('-7 days'));
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$data_online_time = empty($ppc['time']) ? $now_time : ($now_time > $ppc['time'] ? $now_time : ($ppc['time'].':00:00'));
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$advOnlineMap = D('AdvOnlineMap');
    			$list = $advOnlineMap->where($map)->field("id,$online_advs,$online_times")->select();
    			if (empty($list)) { // 不存在则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					$online_advs => $adv['id'],
    					$online_times => $data_online_time
    				);
    				$advOnlineMap->create($data);
    				if (false === $advOnlineMap->add()) {
    					$this->error('插入渠道上线表出错');
    				}
    			} else { // 存在则跟新
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('adv_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_adv = empty($list[0][$online_advs]) ? array() : explode(',', $list[0][$online_advs]);
    				$online_adv_time = empty($list[0][$online_times]) ? array() : explode(',', $list[0][$online_times]);
    				
    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
    				$tmp_value= array();
    				$insert_key = -1;
    				foreach ($online_adv as $key=>$val) { // 修复数据
    					if ($val == $adv['id'] || isset($tmp_value[$val])) {
    						unset($online_adv[$key], $online_adv_time[$key]);
    					} else {
    						if ($online_advs == 'fullscreen_online_adv') { // 针对全屏广告去除7天前上线广告
    							if ($online_adv_time[$key] < $dead_line) {
    								unset($online_adv[$key], $online_adv_time[$key]);continue;
    							}
    						}
    						if ($online_adv_time[$key] > $data_online_time) {
    							$insert_key = $key;
    						}
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				if ($insert_key < 0){ // 插入最前头
    					array_unshift($online_adv, $adv['id']);
    					array_unshift($online_adv_time, $data_online_time);
    				} else {
    					$tmp_online_adv = array(); $tmp_online_adv_time = array();
    					foreach ($online_adv as $key=>$val) {
    						$tmp_online_adv[] = $online_adv[$key];
    						$tmp_online_adv_time[] = $online_adv_time[$key];
    						if ($key == $insert_key) { // 在这个Key后面插入
    							$tmp_online_adv[] = $adv['id'];
    							$tmp_online_adv_time[] = $data_online_time;
    						}
    					}
    					$online_adv = $tmp_online_adv; $online_adv_time = $tmp_online_adv_time;
    					unset($tmp_online_adv, $tmp_online_adv_time);
    				}
    				
    				$data = array(
    					'id' => $id,
    					$online_advs => implode(',',$online_adv),
    					$online_times => implode(',', $online_adv_time)
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
     * 针对渠道下线广告
     * Enter description here ...
     * @param unknown_type $adv
     * @param unknown_type $ppc_list
     * @param $online_advs
	 * @param $online_times
     */
    private function _offOnlineMap($adv, $ppc_list, $online_advs, $online_times){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$advOnlineMap = D('AdvOnlineMap');
    			$list = $advOnlineMap->where($map)->field("id,$online_advs,$online_times")->select();
    			if (!empty($list)) { // 存在则删除，否则不管
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('adv_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_adv = empty($list[0][$online_advs]) ? array() : explode(',', $list[0][$online_advs]);
    				$online_adv_time = empty($list[0][$online_times]) ? array() : explode(',', $list[0][$online_times]);
    				
    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
    				$tmp_value= array();
    				foreach ($online_adv as $key=>$val) { // 修复数据
    					if ($val == $adv['id'] || isset($tmp_value[$val])) {
    						unset($online_adv[$key], $online_adv_time[$key]);
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				$data = array(
    					'id' => $id,
    					$online_advs => implode(',',$online_adv), 
    					$online_times => implode(',', $online_adv_time)
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
     * 图片解说页面
     * Enter description here ...
     */
    function comment(){
    	$id = I('adv_id');
    	if (empty($id)) {
    		$this->error("参数错误");
    	}
    	$serv = I('serv');
    	$option = '';
    	if ($serv == '3' || $serv == '6'){ // 是本服务器的
    		$option = '1';
    	}
    	$this->assign('opt', $option);
    	
    	$map = array('adv_id' => $id);
    	$list = $this->_list(D('AdvComment'), $map, 'pindex', true);
    	$this->display();
    }
    
    function addComment(){
    	$adv_id = I('adv_id');
    	$map = array('id'=>$adv_id);
	    $imgserver = D('Adv')->where($map)->getField('imgserver_use');
	    if ($imgserver == 3 || $imgserver == 6) {
	    	$this->assign('opt',1);
	    }
    	
    	$this->display();
    }
    
    function doAddComment(){
    	$adv_id = I('adv_id');
    	if (empty($adv_id)) {$this->error('参数错误');}
    	$this->_uploadImg2($adv_id);
    	$pindex = I('pindex');
    	if (!is_numeric($pindex)) {
    		$this->error('页数错误');
    	}
    	$image = I('image');
    	if (empty($image)) {$this->error('图片必须');}
    	
    	$update = true;
    	$model = D('AdvComment');
    	if ($pindex < 0) {
    		$map = array('adv_id'=>$adv_id);
    		$pindex = $model->where($map)->max('pindex');
    		$_POST['pindex'] = $pindex === NULL ? 0 : ($pindex+1);
    		$update = false;
    	}
     	if (false === $model->create()) {
            $this->error($model->getError());
        }
        $rst = $model->add();
        if (false !== $rst) {
        	if ($update) {
	        	$map = array('adv_id'=>$adv_id, 'pindex'=> array('EGT',$pindex), 'id' => array('LT',$rst));
	        	$model->where($map)->setInc('pindex');
        	}
        	
        	$map = array('id'=>$adv_id);
        	D('Adv')->where($map)->setInc('total_page');
        	$this->success('添加成功');
        } else {
        	$this->error('添加失败');
        }
    }
    
    /**
     * 获取一页的解说信息
     * Enter description here ...
     */
    function getOneComment(){
    	$adv_id = I('adv_id');
    	$pindex = I('pindex');
    	
    	$num = I('num');
    	if ($pindex < 0){
    		$map = array('adv_id'=>$adv_id);
    		$pindex = D('AdvComment')->where($map)->max('pindex');
    		$pindex ++;
    	}
    	if (!empty($num)) {
    		$pindex = $pindex + ($num);
    	}
    	
    	if ($pindex<0) {
    		$vo = array();
    	} else {
	    	$map = array('id'=>$adv_id);
	    	$imgserver = D('Adv')->where($map)->getField('imgserver_use');
	    	
	    	$map = array('adv_id'=>$adv_id, 'pindex'=>$pindex);
	    	$vo = D('AdvComment')->where($map)->find();
	    	if (!empty($vo)) {
	    		$vo['img'] = getAdvCommentImgUrl($vo['image'], $adv_id, $imgserver);
	    	}
    	}
//    	$this->display('index');
    	$this->ajaxReturn($vo);
    }
    
    /**
     * 更新解说
     * Enter description here ...
     */
    function updateComment(){
    	$id = I('id');
    	$model = D('AdvComment');
    	$map = array('id' => $id);
    	$adv_id = $model->where($map)->getField('adv_id');
    	if (empty($adv_id)) {$this->error('参数错误');}
    	$this->_uploadImg2($adv_id, true);
        
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            $this->success('更新成功');
        } else {
        	$this->error('更新失败');
        }
    }
    
	/**
     * 删除影片解说
     * Enter description here ...
     */
    function foreverdelComment(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$model = D('AdvComment');
    	$map = array('id' => $id);
    	
    	$one = $model->where($map)->field('adv_id, pindex')->find();
    	if (empty($one)) {$this->error('参数错误');}
    	
    	$rst = array('rst' => 1); 
    	if (false !== $model->where($map)->delete()) {
    		$map = array('id' => $one['adv_id']);
    		if (!$adv = D('Adv')->where($map)->setDec('total_page')) { // 影片页数-1
    			$this->error('更新广告信息失败，还未跟新章节数'); 
    		} 
    		$map = array('id' => $one['adv_id'],'pindex' => array('GT',$one['pindex']));
    		$model->where($map)->setDec('pindex');
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败');
    	}
    }
    
//    /**
//     * 删除影片解说
//     * Enter description here ...
//     */
//    function foreverdelComment(){
//    	$id = I('id');
//    	if (empty($id)) {
//    		$this->error('参数错误');
//    	}
//    	$model = D('AdvComment');
//    	$map = array('id' => $id);
//    	
//    	$one = $model->where($map)->field('adv_id, pindex')->find();
//    	if (empty($one)) {$this->error('参数错误');}
//    	
//    	$rst = array('rst' => 1); 
//    	if (false !== $model->where($map)->delete()) {
//    		$map = array('id' => $one['adv_id']);
//    		if (!$adv = D('Adv')->where($map)->setDec('total_page')) { // 影片页数-1
//    			$rst['msg'] = '更新广告信息失败，还未跟新章节数'; 
//    			$this->ajaxReturn($rst);
//    		} 
//    		$map = array('pindex' => array('GT',$one['pindex']));
//    		$model->where($map)->setDec('pindex');
//    		$rst['msg'] = '删除成功!';
//    		$rst['rst'] = 0;  
//    		$this->ajaxReturn($rst);
//    	} else {
//    		$rst['msg'] = '删除成功!';
//    		$this->ajaxReturn($rst);
//    	}
//    }
    
    /**
     * 上传封面图片页面
     * Enter description here ...
     */
    function uploadImg(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	
    	$vo = D('Adv')->field('id,name,icon,bpic,spic,fullscreen_show_pic')->getById($id);
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
    	$model = D('Adv');
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
            $dir = '../adv/'.$id;
          
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
    		if (!empty($_FILES['icon']) && $_FILES['icon']['error'] != 4) { 
            	$upload->saveRule = $saveName."i";
            	$fileInfo = $upload->uploadOne($_FILES['icon']);
            	if ($fileInfo) {
            		$_POST ['icon'] = $server_pre.'adv/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'icon','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            	$nofile = false;
            }
            
            if (!empty($_FILES['bpic']) && $_FILES['bpic']['error'] != 4) { 
            	$upload->saveRule = $saveName."b";
            	$fileInfo = $upload->uploadOne($_FILES['bpic']);
            	if ($fileInfo) {
            		$_POST ['bpic'] = $server_pre.'adv/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'bpic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            	$nofile = false;
            }
            
            if (!empty($_FILES['spic']) && $_FILES['spic']['error'] != 4) {
            	$upload->saveRule = $saveName."s";
            	$fileInfo = $upload->uploadOne($_FILES['spic']);
            	if ($fileInfo) {
            		$_POST ['spic'] = $server_pre.'adv/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'spic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            	$nofile = false;
            }
            
    		if (!empty($_FILES['fullscreen_show_pic']) && $_FILES['fullscreen_show_pic']['error'] != 4) {
            	$upload->saveRule = $saveName."f";
            	$fileInfo = $upload->uploadOne($_FILES['fullscreen_show_pic']);
            	if ($fileInfo) {
            		$_POST ['fullscreen_show_pic'] = $server_pre.'adv/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'fullscreen_show_pic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            	$nofile = false;
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (empty($files)) {
            	$this->error('没选择文件，就不要提交了嘛~~~');
            } else {
            	$rst = sendFileToImgSevr('adv',$id,$files);
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
    
 	/**
     * 上传解说图片
     * Enter description here ...
     * @param unknown_type $id
     */
	private function _uploadImg2($id = 0, $update=FALSE){
    	if (!empty($_FILES)) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../adv/'.$id;
          
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
            
            $server_pre = getImgServerURL(0)."/";
            
            $files = array();
    		if (!empty($_FILES['image']) && $_FILES['image']['error'] != 4) {
            	$upload->saveRule = "i".date('mdHis');
            	$fileInfo = $upload->uploadOne($_FILES['image']);
            	if ($fileInfo) {
            		$_POST ['image'] = $server_pre.'adv/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'image','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (empty($files)) {
            	if (!$update) {
            		$this->error('没选择文件，就不要提交了嘛~~~');
            	}
            } else {
            	$rst = sendFileToImgSevr('adv',$id,$files);
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
?>