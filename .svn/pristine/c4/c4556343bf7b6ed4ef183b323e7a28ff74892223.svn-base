<?php
// 后台用户模块
class TopicAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
    function _before_index(){
    	$topicUMovie = D('TopicUMovie');
    	$list = $topicUMovie->field('count(1) as num,topic_id')->group('topic_id')->select();
    	foreach ($list as $item) {
    		$arr[$item['topic_id']] = $item['num'];
    	}
    	$this->assign('topicMovieNums', $arr);
    }
    
    function _before_insert(){
    	$graphers = I('grapher');
    	if (!empty($graphers)) {
    		$graphers = str_replace('，',',',trim($graphers));
    		$_POST['grapher'] = $graphers;
    		$tmp = explode(',', $graphers);
    		foreach ($tmp as $grapher) {
	    		$map = array('id'=>$grapher);
	    		$one = D('Member')->where($map)->getField('id');
	    		if (empty($one)) {
	    			$this->error('用户'.$grapher.'不存在啊');
	    		}
    		}
    	}
    }
    
    function _before_update(){
    	$graphers = I('grapher');
    	if (!empty($graphers)) {
    		$graphers = str_replace('，',',',trim($graphers));
    		$_POST['grapher'] = $graphers;
    		$tmp = explode(',', $graphers);
    		foreach ($tmp as $grapher) {
	    		$map = array('id'=>$grapher);
	    		$one = D('Member')->where($map)->getField('id');
	    		if (empty($one)) {
	    			$this->error('用户'.$grapher.'不存在啊');
	    		}
    		}
    	}
    }
    
	/**
     * 专题列表
     * @see CommonAction::index()
     */
    function index(){
    	$online = I('online');
    	if ($online > 0) { // 已经上线 或 未上线的
    		$online_topics = $this->_getOnlineIdList(); // 获取已经上线的专辑列表
    		$online_topic_ids = array_keys($online_topics); // 获取上线专辑ID集合
    		
    		$orderby = I('orderby');
    		$field = (empty($orderby) || $orderby == 'online_time') ? 'id' : $orderby;
    		$order = I('order');
    		$order = empty($order) ? 'desc' : $order;
    		
    		$map = $this->_search(); // 获取查询条件
    		$this->_filter($map);
    		$topic = D('Topic');
    		$all_topic = $topic->where($map)->getField('id,'.$field, true);
    		$all_topic_ids = array_keys($all_topic);
    		$topic_ids = array();
    		if ($online == 1) { // 已经上线
    			$topic_ids = array_intersect($online_topic_ids, $all_topic_ids); // 求交集，得到满足条件的IDs
    		} elseif ($online == 2) { // 未上线
    			$topic_ids = array_diff($all_topic_ids, $online_topic_ids); // 求差集，得到满足条件的IDs
    		}
    		if (!empty($topic_ids)) {
    			if (empty($orderby) || $orderby == 'id') {
    				$order == 'desc' ? rsort($topic_ids) : sort($topic_ids);
    			} elseif ($orderby == 'online_time') { // 根据上线表情况来判断
    				if ($online == 1) { // 只有在已经上线的情况，才有按照上线时间排序
    					// 由于数据库表中的ID已经是通过上线时间顺序降序排列的,如果是升序排列，变换顺序即可
    					$order == 'desc' ? array_values($topic_ids) : array_reverse($topic_ids);	
    				}
    				
    			} else {  // 根据topic表的字段情况来判断
    				// 由于从topic表中查出来的顺序是乱序的，所以这部分需要自己排序了
    				$sort_ids = array();
    				foreach ($topic_ids as $id) { 
    					$sort_ids[$id] = $all_topic[$id];
    				}
    				$order == 'desc' ? arsort($sort_ids) : asort($sort_ids);
    				$topic_ids = array_keys($sort_ids);
    			}
    			$this->_getTopicList($topic_ids, $map);
    		}
    		$this->display();
    	} else {
    		parent::index();
    	}
    }
    
    /**
     * 获取专辑播放情况
     * Enter description here ...
     */
    public function getTotalViews(){
    	$ids = I('ids');
    	$rst = array('rst'=>0, 'msg'=>'');
    	if (empty($ids)) {$rst['msg']='参数错误';$this->ajaxReturn($rst);}
    	$ids = explode(',', $ids);
    	foreach ($ids as $id) {
    		$map = array('topic_id'=>$id);
    		$movie_ids = D('TopicUMovie')->where($map)->getField('movie_id',TRUE);
    		if (empty($movie_ids)) {
    			$rst['data'][$id] = 0;
    		} else {
    			$map = array('id'=>array('in',$movie_ids));
    			$rst['data'][$id] = D('Movie')->where($map)->sum('played');
    		}
    	}
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    /**
     * 获取列表数据
     * Enter description here ...
     * @param unknown_type $ids
     */
    private function _getTopicList($ids, $map, $sort='desc', $order='id'){
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
            	$list = D('Topic')->where($where)->select(); // 这里查出来是乱序的，所以需要进行处理
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
     * 获取已经上线的专题ID列表
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
    	
    	$onlineMapTopic = D('OnlineMapTopic'); // 各个平台渠道上线模型
    	$list = $onlineMapTopic->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			$topic_ids = explode(',', $item['online_topic']);
    			$online_times = explode(',', $item['online_topic_time']);
    			foreach ($topic_ids as $key => $val) { // 直接用 key=value 免去求交集的烦恼。
    				$rst[$val] = $online_times[$key]; // 组成topic_id => 上线时间， 这样一个数组
    			}
    		}
    	}
    	return $rst;
    } 
    
    
    /**
     * 绑定电影操作
     * Enter description here ...
     */
    function uMovie(){
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    	$map = array('topic_id' => $id);
    	$own_movie_list = D('TopicUMovie')->where($map)->getField('id,movie_id,add_time');
    	
    	$movie_list = D('Movie')->getField('id,name,keyword');
    	foreach ($own_movie_list as $key=>$item) {
    		$own_movie_list[$key]['name'] = $movie_list[$item['movie_id']]['name'];
    		$own_movie_list[$key]['keyword'] = $movie_list[$item['movie_id']]['keyword'];
    		unset($movie_list[$item['movie_id']]);
    	}
    	
    	$this->assign('ownMovieList', $own_movie_list);
    	$this->assign('allMovieList', $movie_list);
    	$this->display();
    }
    
    function doUMovie(){
    	$movie_ids = I('movie_ids');
    	$topic_id = I('topic_id');
    	$topic_name = I('topic_name');
    	if (empty($movie_ids) || empty($topic_id) || empty($topic_name)) {$this->error('参数错误');}
    	$movie_ids = explode(',', $movie_ids);
    	$add_time = date('Y-m-d H:i:s', NOW_TIME);
    	$topicUMovie = D('TopicUMovie');
    	
    	$old_movie_ids = $topicUMovie->where(array('topic_id'=>$topic_id))->getField('movie_id', true);
    	
    	$add_data = array();
    	$del_data = array();
    	foreach ($movie_ids as $movie_id) {
    		if (!in_array($movie_id, $old_movie_ids)) {
    			$add_data[] = array('topic_id'=>$topic_id, 'movie_id'=>$movie_id, 'add_time'=>$add_time);
    		}
    	}
    	$del_data = array_diff($old_movie_ids, $movie_ids);
    	
    	$movie = D('Movie');
    	if (!empty($add_data)) {
	    	if (false === $topicUMovie->addAll($add_data)){
	    		$this->error('添加时失败');
	    	}
	    	
	    	foreach ($add_data as $add) {
				$map = array('id'=>$add['movie_id']);
				$data = $movie->where($map)->field('id,keyword')->find();
				if (!empty($data)) {
					if (empty($data['keyword'])) {
						$data['keyword'] = $topic_name;
					} else {
						$data['keyword'] = $data['keyword'] . ',' . $topic_name;
					}
					if (false === $movie->save($data)) {
						$this->error('更新电影表时出错'.$data['id']);
					}
				}
	    	}
	    	
	    	// 有新电影更新进来，则动态发送收藏该专辑的用户
	    	$this->_updateMemberNews($topic_id);
    	}
    	
    	if (!empty($del_data)) {
    		$map = array('movie_id'=>array('in',$del_data), 'topic_id'=>$topic_id);
    		if (false === $topicUMovie->where($map)->delete()) {
    			$this->error('删除时失败');
    		}
    		
    		foreach ($del_data as $del) {
				$map = array('id'=>$del);
				$data = $movie->where($map)->field('id,keyword')->find();
				if (!empty($data) && !empty($data['keyword'])) {
					$tmp = explode(',', $data['keyword']);
					foreach ($tmp as $key=>$val) {
						if ($val == $topic_name){
							unset($tmp[$key]); break;
						}
					}
					$data['keyword'] = implode(',', $tmp);
					if (false === $movie->save($data)) {
						$this->error('更新电影表时出错'.$data['id']);
					}
				}
	    	}
    	}
    	
    	if (empty($add_data) && empty($del_data)) {
    		$this->error('既然没有修改，为嘛还要提交？');
    	}
    	$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
	    $this->success('配置成功');
    }
    
	function online(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$topic = D('Topic')->getById($id);
    	if (empty($topic)){
    		$this->error('参数错误');
    	}
    	$_fiter = array(
    		'bpic' => '还木油上传封面',
    	);
    	foreach($_fiter as $key=>$val) {
    		if (empty($topic[$key])) {
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
		
		$onlineMapTopic = D('OnlineMapTopic');
		
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'online_topic' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\'')
		);
		$list = $onlineMapTopic->where($map)->field('pub_channel,pub_platform,online_topic,online_topic_time')->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		foreach ( $list as $val ) {
			$topic_ids = explode(',', $val['online_topic']);
			$topic_times = explode(',', $val['online_topic_time']);
			$online_time = '';
			foreach ($topic_ids as $key => $ids) {
				if ($ids == $id) {
					$online_time = $topic_times[$key];
				}
			}
			
			$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $online_time);
			$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true; 
			
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
     * 实实在在的上线操作
     * Enter description here ...
     */
    function doOnline(){
    	$online_list = I('online');
    	if (empty($online_list)) {$this->error('选择要上线的平台渠道啊');}
    	$topic_id = I('id');
    	if (empty($topic_id)) {$this->error('参数错误');}
    	
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
    	
    	$topic = D('Topic')->getById($topic_id);
    	if (empty($topic)) {$this->error('擦嘞，专题被删了');}
    	// 更新 topic_online_map
    	$this->_updateOnlineMap($topic, $online_options);
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
    	$topic_id = I('id');
    	if (empty($topic_id)) {$this->error('参数错误');}
    	
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1]
    		);
    	}
    	
    	$topic = D('Topic')->getById($topic_id);
    	if (empty($topic)) {$this->error('擦嘞，专题被删了');}
    	
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	$onlineMapTopic = D('OnlineMapTopic');
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'online_topic' => array('exp',' REGEXP \'^'.$topic['id'].'$|^'.$topic['id'].',|,'.$topic['id'].',|,'.$topic['id'].'$\'')
		);
		$list = $onlineMapTopic->where($map)->field('pub_channel,pub_platform')->select();
		
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
		
		// 下线更新topic_online_map
    	$this->_offOnlineMap($topic, $offline_options);
    	$this->success('下线成功');
    }
    
    
    /**
     * 更新平台渠道上线列表
     * Enter description here ...
     * @param Array $movie 一条电影记录 
     * @param Array $ppc
			ppc_list = array(
				0=>array(
					'pub_platform'=0,
					'pub_channel'=0,
					'position'=0,
				),
			)
     */
    private function _updateOnlineMap($topic, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$onlineMapTopic = D('OnlineMapTopic');
    			$list = $onlineMapTopic->where($map)->field('id,online_topic,await_topic,online_topic_time,await_topic_time')->select();
    			if (empty($list)) { // 不存在则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					'online_topic' => $topic['id'], 'await_topic' => '',
    					'online_topic_time' => date('Y-m-d H:i:s', NOW_TIME), 'await_topic_time'=> '',
    					'online_topic_count' => 1, 'await_topic_count' => 0
    				);
    				$onlineMapTopic->create($data);
    				if (false === $onlineMapTopic->add()) {
    					$this->error('插入渠道上线表出错');
    				}
    			} else { // 存在则跟新
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('topic_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_topic = empty($list[0]['online_topic']) ? array() : explode(',', $list[0]['online_topic']);
    				$online_topic_time = empty($list[0]['online_topic_time']) ? array() : explode(',', $list[0]['online_topic_time']);
    				$await_topic = empty($list[0]['await_topic']) ? array() : explode(',', $list[0]['await_topic']);
    				$await_topic_time = empty($list[0]['await_topic_time']) ? array() : explode(',', $list[0]['await_topic_time']);

    				if (!empty($online_topic)) {
	    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
	    				$tmp_value= array();
	    				foreach ($online_topic as $key=>$val) { // 修复数据
	    					if ($val == $topic['id'] || isset($tmp_value[$val])) {
	    						unset($online_topic[$key], $online_topic_time[$key]);
	    					} else {
	    						$tmp_value[$val] = true;
	    					}
	    				}
	    				$i = 0;
	    				$tmp = '';
	    				foreach ($online_topic as $key=>$val) {
	    					if ($i==$position) {
	    						$tmp = $val;
	    						$online_topic[$key] = $topic['id'];
	    						$i++;continue;
	    					}
	    					if ($tmp != '') { // 找到位置了 id 顺利插入
	    						$online_topic[$key] = $tmp;
	    						$tmp = $val;
	    					}
	    					$i++;
	    				}
	    				if ($tmp!='') {
	    					$online_topic[] = $tmp;
	    				}
	    				
	    				$i = 0;
	    				$tmp = '';
	    				foreach ($online_topic_time as $key=>$val) {
	    					if ($i==$position) {
	    						$tmp = $val;
	    						if ($i == 0) { // 如果是在第一位，则用当前时间
	    							$online_topic_time[$key] = date('Y-m-d H:i:s', NOW_TIME);
	    						} else {
	    							$online_topic_time[$key] = $val;
	    							$tmp = date('Y-m-d H:i:s',(strtotime($val)-1)); // 比这个小1秒
	    						}
	    						$i++;continue;
	    					}
	    					if ($tmp != '') { // 找到位置了 time 顺利插入
	    						$online_topic_time[$key] = $tmp;
	    						$tmp = $val;
	    					}
	    					$i++;
	    				}
	    				if ($tmp!='') {
	    					$online_topic_time[] = $tmp;
	    				}
    				} else {
    					$online_topic = array($topic['id']);
    					$online_topic_time = array(date('Y-m-d H:i:s', NOW_TIME));
    				}
    				
					$tmp_value = array();
    				foreach ($await_topic as $key=>$val) { // 删除等待区域的数据
    					if ($val == $topic['id'] || isset($tmp_value[$val])) {
    						unset($await_topic[$key], $await_topic_time[$key]); 
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_topic' => implode(',',$online_topic), 'await_topic' => implode(',',$await_topic),
    					'online_topic_time' => implode(',', $online_topic_time), 'await_topic_time'=> implode(',', $await_topic_time),
    					'online_topic_count' => count($online_topic), 'await_topic_count' => count($await_topic)
    				);
    				$onlineMapTopic->create($data);
    				if (false === $onlineMapTopic->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    			}
    		}
    	}
    }
    
    /**
     * 下发关注过该专题的动态通知
     * Enter description here ...
     * @param unknown_type $topic
     */
    private function _updateMemberNews($topic_id){
    	$map = array('id'=>$topic_id);
    	$topic_name = D('Topic')->where($map)->getField('name');
    	if (empty($topic_name)) return;
    	
    	// 这里查询出关注这部专题的人，就可以直接发了，因为专题是经过上线后，再进行收藏的，所以，没必要判断用户是否是该渠道的了
//    	$onlineMapTopic = D('OnlineMapTopic');
//    	$map = array ( // 四种情况  id | id, | ,id, | ,id
//			'id' => array ('neq',1),
//			'online_topic' => array('exp',' REGEXP \'^'.$topic_id.'$|^'.$topic_id.',|,'.$topic_id.',|,'.$topic_id.'$\'')
//		);
//		// 查询专辑上线的平台渠道
//		$channel_platform_list = $onlineMapTopic->where($map)->field('pub_channel,pub_platform')->select();
//		if (empty($channel_platform_list)) return;
//		$channel_platform = array();
//		foreach ($channel_platform_list as $tmp) {
//			$channel_platform[$tmp['pub_platform'].','.$tmp['pub_channel']] = true;
//		}
		
		$map = array('topic_id'=>$topic_id);
		$member_ids = D('MemberKeepTopic')->where($map)->getField('user_id',TRUE);
		
		if (empty($member_ids)) return;
		$member_new_list = array(); // 需要添加的用户动态列表
		$add_time = toDate(NOW_TIME);
		foreach ($member_ids as $member_id) {
			$member_new_list[] = array(
	    			'user_id' => 1,
	    			'to_user_id' => $member_id,
	    			'reply_comment_id' => 0,
	    			'comment_content' => '你收藏的专辑 《'.$topic_name.'》 有新作品上线啦，点击查看',
	    			'reply_from' => 'to-topic',
	    			'reply_from_data' => $topic_id,
	    			'readed' => 0,
	    			'pre_length' => 0,
	    			'secret_send' => 0,
	    			'add_time' => $add_time
	    	);
		}
		
    	if  (!empty($member_new_list) && false === D('MemberNew')->addAll($member_new_list)) {
    		$this->error('给关注用户发送动态错误');
    	}
    }
    
    /**
     * 针对渠道下线专题
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $ppc_list
     */
	private function _offOnlineMap($topic, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$onlineMapTopic = D('OnlineMapTopic');
    			$list = $onlineMapTopic->where($map)->field('id,online_topic,await_topic,online_topic_time,await_topic_time')->select();
    			if (!empty($list)) { // 存在则删除，否则不管
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('topic_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_topic = empty($list[0]['online_topic']) ? array() : explode(',', $list[0]['online_topic']);
    				$online_topic_time = empty($list[0]['online_topic_time']) ? array() : explode(',', $list[0]['online_topic_time']);
    				$await_topic = empty($list[0]['await_topic']) ? array() : explode(',', $list[0]['await_topic']);
    				$await_topic_time = empty($list[0]['await_topic_time']) ? array() : explode(',', $list[0]['await_topic_time']);
    				
    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
    				$tmp_value= array();
    				foreach ($online_topic as $key=>$val) { // 修复数据
    					if ($val == $topic['id'] || isset($tmp_value[$val])) {
    						unset($online_topic[$key], $online_topic_time[$key]);
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				$tmp_value = array();
    				foreach ($await_topic as $key=>$val) { // 删除等待区域的数据
    					if ($val == $topic['id'] || isset($tmp_value[$val])) {
    						unset($await_topic[$key], $await_topic_time[$key]); 
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_topic' => implode(',',$online_topic), 'await_topic' => implode(',',$await_topic),
    					'online_topic_time' => implode(',', $online_topic_time), 'await_topic_time'=> implode(',', $await_topic_time),
    					'online_topic_count' => count($online_topic), 'await_topic_count' => count($await_topic)
    				);
    				$onlineMapTopic->create($data);
    				if (false === $onlineMapTopic->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    			}
    		}
    	}
    }
    
    
    
     /**
     * 上传封面图片页面
     * Enter description here ...
     */
    function uploadImg(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	
    	$vo = D('Topic')->field('id,name,bpic,spic')->getById($id);
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
    	$model = D('Topic');
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
            $dir = '../topics/'.$id;
          
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
            
            if (!empty($_FILES['spic'])) {
            	$upload->saveRule = $saveName."s";
            	$fileInfo = $upload->uploadOne($_FILES['spic']);
            	if ($fileInfo) {
            		$_POST ['spic'] = getImgServerURL(0).'/topics/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'spic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
    		if (!empty($_FILES['bpic'])) {
            	$upload->saveRule = $saveName."b";
            	$fileInfo = $upload->uploadOne($_FILES['bpic']);
            	if ($fileInfo) {
            		$_POST ['bpic'] = getImgServerURL(0).'/topics/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'bpic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
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
            	$this->error('没选择文件，就不要提交了嘛~~~');
            } else {
            	$rst = sendFileToImgSevr('topics',$id,$files);
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