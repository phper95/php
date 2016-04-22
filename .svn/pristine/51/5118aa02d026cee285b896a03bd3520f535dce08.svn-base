<?php
// 后台用户模块
class AdvhtAction extends CommonAction {
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
    	$online = I('online');
    	if ($online > 0) { // 已经上线 或 未上线的
    		$online_advhts = $this->_getOnlineIdList(); // 获取已经上线的广告列表
    		$online_advht_ids = array_keys($online_advhts); // 获取上线广告ID集合
    		
    		$orderby = I('orderby');
    		$field = (empty($orderby) || $orderby == 'online_time') ? 'id' : $orderby;
    		$order = I('order');
    		$order = empty($order) ? 'desc' : $order;
    		
    		$map = $this->_search(); // 获取查询条件
    		$this->_filter($map);
    		$advht = D('Advht');
    		$all_advht = $advht->where($map)->getField('id,'.$field, true);
    		$all_advht_ids = array_keys($all_advht);
    		$advht_ids = array();
    		if ($online == 1) { // 已经上线
    			$advht_ids = array_intersect($online_advht_ids, $all_advht_ids); // 求交集，得到满足条件的IDs
    		} elseif ($online == 2) { // 未上线
    			$advht_ids = array_diff($all_advht_ids, $online_advht_ids); // 求差集，得到满足条件的IDs
    		}
    		if (!empty($advht_ids)) {
    			if (empty($orderby) || $orderby == 'id') {
    				$order == 'desc' ? rsort($advht_ids) : sort($advht_ids);
    			} elseif ($orderby == 'online_time') { // 根据上线表情况来判断
    				if ($online == 1) { // 只有在已经上线的情况，才有按照上线时间排序
    					// 由于数据库表中的ID已经是通过上线时间顺序降序排列的,如果是升序排列，变换顺序即可
    					$order == 'desc' ? array_values($advht_ids) : array_reverse($advht_ids);	
    				}
    			} else {  // 根据adv表的字段情况来判断
    				// 由于从adv表中查出来的顺序是乱序的，所以这部分需要自己排序了
    				$sort_ids = array();
    				foreach ($advht_ids as $id) { 
    					$sort_ids[$id] = $all_advht[$id];
    				}
    				$order == 'desc' ? arsort($sort_ids) : asort($sort_ids);
    				$advht_ids = array_keys($sort_ids);
    			}
    			$this->_getAdvhtList($advht_ids, $map);
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
    private function _getAdvhtList($ids, $map, $sort='desc', $order='id'){
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
            	$list = D('Advht')->where($where)->select(); // 这里查出来是乱序的，所以需要进行处理
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
    	
    	$advhtOnlineMap = D('AdvhtOnlineMap'); // 各个平台渠道上线模型
    	$list = $advhtOnlineMap->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
	    		$advht_ids = explode(',', $item['online_ht']);
    			foreach ($advht_ids as $key => $val) { // 直接用 key=value 免去求交集的烦恼。
    				$rst[$val] = $val; // 组成advht_id => id， 这样一个数组
    			}
    		}
    	}
    	return $rst;
    } 
    
    /**
     * 更新有个文件上传，需要处理一下
     * Enter description here ...
     */
    function _before_update(){
    	$id = I('id');
    	if (!empty($id) && is_numeric($id)) {
    		$this->_uploadImg2($id);
    	}
    }
    
	function update() {
        $model = D('Advht');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        $data = $model->data();
        $map = array('id'=>$data['id']);
        $old_data = $model->where($map)->find();
        if (empty($old_data)) {
        	$this->error('参数错误');
        }
        
        // 更新数据
        $list = $model->save($data);
        if (false !== $list) {
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
    
    
    // 上线页面
    function online(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$advht = D('Advht')->getById($id);
    	if (empty($advht)){
    		$this->error('参数错误');
    	}
    	$_fiter = array(
    		'img'=>'没有图片，不允许上线',
    		'intro' => '没有解说，不允许上线，哼~~'
    	);
    	foreach($_fiter as $key=>$val) {
    		if (empty($advht[$key])) {
    			$this->error($val);
    		}
    	}
    	
    	$platform = D('Platform');
		$map = array('open' => 1);
		$platformList = $platform->where($map)->getField('name,id');
		
		$channel = D('Channel');
		$channelList = $channel->where($map)->getField('name,id');
		
		$plVch = D('PlatformVChannel');
		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
		
		$advhtOnlineMap = D('AdvhtOnlineMap');
		$map = array( // 四种情况 id | id, | ,id, | ,id
			'id' => array('neq',1),
			'online_ht' => array ('exp' ,' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\'')
		);
		$list = $advhtOnlineMap->where($map)->field('pub_channel,pub_platform,online_ht')->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		foreach($list as $val) {
			$advht_ids = explode(',', $val['online_ht']);
			$onlineList[$val['pub_platform']][] = array('channel'=>$val['pub_channel']);
			$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true; 
		}
		
		$this->assign('onList',$onlineList);
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
		
		$this->assign('offList', $underlineList);
		
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
    	$advht_id = I('id');
    	if (empty($advht_id)) {$this->error('参数错误');}
    	
    	$online_options = array(array(
    		'pub_platform' => 'all',
    		'pub_channel' => 'all',
    		'position' => 0
    	));
    	foreach ($online_list as $val) {
    		$option = I($val);
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1],
    			'position' => is_numeric($option) ? abs($option) : 0
    		);
    	}
    	
    	$adv = D('Adv')->getById($advht_id);
    	if (empty($adv)) {$this->error('擦嘞，广告被删了');}
    	
    	// 更新 adv_online_map
    	$this->_updateOnlineMap($adv, $online_options);
    	$this->success('上线成功');
    }
    
    /**
     * 实实在在的下线操作
     * Enter description here ...
     */
    public function doOffline(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$advht_id = I('id');
    	if (empty($advht_id)) {$this->error('参数错误');}
    	
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1]
    		);
    	}
    	
    	$advht = D('Advht')->getById($advht_id);
    	if (empty($advht)) {$this->error('擦嘞，广告被删了');}
    	
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	$advhtOnlineMap = D('AdvhtOnlineMap');
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'online_ht' => array('exp',' REGEXP \'^'.$advht['id'].'$|^'.$advht['id'].',|,'.$advht['id'].',|,'.$advht['id'].'$\'')
		);
		$list = $advhtOnlineMap->where($map)->field('pub_channel,pub_platform')->select();
		
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
    	$this->_offOnlineMap($advht, $offline_options);
    	$this->success('下线成功');
    }
    
    
    /**
     * 更新平台渠道上线列表
     * Enter description here ...
     * @param Array $advht 一条广告记录 
     * @param Array $ppc
			ppc_list = array(
				0=>array(
					'pub_platform'=0,
					'pub_channel'=0,
					'position'=0,
				),
			)
	 * @param $online_advhts
     */
    private function _updateOnlineMap($advht, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$advhtOnlineMap = D('AdvhtOnlineMap');
    			$list = $advhtOnlineMap->where($map)->field("id,online_ht")->select();
    			if (empty($list)) { // 不存在则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					'online_ht' => $advht['id']
    				);
    				$advhtOnlineMap->create($data);
    				if (false === $advhtOnlineMap->add()) {
    					$this->error('插入渠道上线表出错');
    				}
    			} else { // 存在则跟新
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('adv_ht_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_advhts = empty($list[0]['online_ht']) ? array() : explode(',', $list[0]['online_ht']);
    				
    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
    				$tmp_value= array();
    				foreach ($online_advhts as $key=>$val) { // 修复数据
    					if ($val == $advht['id'] || isset($tmp_value[$val])) {
    						unset($online_advhts[$key]);
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				$i = 0;
    				$tmp = '';
    				foreach ($online_advhts as $key=>$val) {
    					if ($i==$position) {
    						$tmp = $val;
    						$online_advhts[$key] = $advht['id'];
    						$i++;continue;
    					}
    					if ($tmp != '') { // 找到位置了 id 顺利插入
    						$online_advhts[$key] = $tmp;
    						$tmp = $val;
    					}
    					$i++;
    				}
    				if ($tmp!='') {
    					$online_advhts[] = $tmp;
    				}
    				
    				if (empty($online_advhts)) { // 如果为空的话。 则添加进去
    					$online_advhts = array($advht['id']);
    				} else if ($i<=$position) { // 如果$position 大于最后一个位置 则添加到最后
    					$online_advhts[] = $advht['id'];
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_ht' => implode(',',$online_advhts)
    				);
    				$advhtOnlineMap->create($data);
    				if (false === $advhtOnlineMap->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    			}
    		}
    	}
    }
    
    /**
     * 针对渠道下线广告
     * Enter description here ...
     * @param unknown_type $advht
     * @param unknown_type $ppc_list
     */
    private function _offOnlineMap($advht, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$advhtOnlineMap = D('AdvhtOnlineMap');
    			$list = $advhtOnlineMap->where($map)->field("id,online_ht")->select();
    			if (!empty($list)) { // 存在则删除，否则不管
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('adv_ht_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_advht = empty($list[0]['online_ht']) ? array() : explode(',', $list[0]['online_ht']);
    				
    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
    				$tmp_value= array();
    				foreach ($online_advht as $key=>$val) { // 修复数据
    					if ($val == $advht['id'] || isset($tmp_value[$val])) {
    						unset($online_advht[$key]);
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_ht' => implode(',',$online_advht), 
    				);
    				$advhtOnlineMap->create($data);
    				if (false === $advhtOnlineMap->save()) {
    					$this->error('更新渠道上线表出错');
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
	private function _uploadImg2($id = 0){
    	if (!empty($_FILES)) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../advht/'.$id;
          
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
    		if (!empty($_FILES['img']) && $_FILES['img']['error'] != 4) {
            	$upload->saveRule = "i".date('mdHis');
            	$fileInfo = $upload->uploadOne($_FILES['img']);
            	if ($fileInfo) {
            		$_POST ['img'] = $server_pre.'advht/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'img','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (!empty($files)) {
            	$rst = sendFileToImgSevr('advht',$id,$files);
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