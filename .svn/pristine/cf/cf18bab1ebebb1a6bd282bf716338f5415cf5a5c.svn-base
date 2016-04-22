<?php
// 自动执行调用模块
class AutoExecAction extends Action {
	
    /**
     * 
     * @see CommonAction::index()
     */
    function index(){
    	
    }
    
    function _initialize(){
    	$this->_startTime = microtime(TRUE);
    }
    
    /**
     * 上线电影
     * Enter description here ...
     */
    function doOnline(){
    	//step1: 找到所有数据库中上线列表不为空的记录
    	$onlineMapPC = D('OnlineMapPC');
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'await_movie' => array('neq','')
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform,await_movie,await_movie_time')->select();
		
		$movie_ids = array(); // 记录未上线的电影ID
		if (!empty($list)) {
			foreach($list as $item) {
				$await_movie = explode(',', $item['await_movie']);
				$await_movie_time = explode(',', $item['await_movie_time']);
				foreach ($await_movie_time as $key=>$val) {
					if (NOW_TIME - strtotime($val) > 0) { // 如果现在的时间，大于上线时间，则表示要上线了
						$online_list[$await_movie[$key]][] = array(
							'pub_platform' => $item['pub_platform'],
							'pub_channel' => $item['pub_channel'],
							'position' => $val
						);
					} else {
						$movie_ids[$await_movie[$key]] = true; 
					}
				} 
			}
		}
		if (!empty($online_list)) { // 上线列表不为空则需要上线
			foreach ($online_list as $movie_id => $online_options) {
				$movie = D('Movie')->getById($movie_id);
				if (empty($movie)) {continue;}
				
				// 添加全部渠道，全部平台上线
	    		$online_options [] = array ('pub_platform' => 'all', 'pub_channel' => 'all', 
	    			'position' => isset($movie_ids[$movie_id]) ? 0 : -1 // 如果存在还没有上线的渠道，则为-1 
	    		);
		    	$nameIds = $this->_getTagsZoneTimeIds($movie);
		    	if (!$nameIds['rst']) {
		    		$this->error($nameIds['msg']);
		    	}
		    	
		    	$tags = array_keys($nameIds['tags']);
		    	$zones = array_keys($nameIds['zones']);
		    	$showtimes = array_keys($nameIds['showtimes']);
		    	// 更新Tags_map
		    	$this->_updateTagsMap($movie, $tags, $zones, $showtimes);
		    	
		    	// 更新m_tag movie_v_tag
		    	$this->_updateMovieTag($movie, $tags);
		    	// 更新movie_v_zong
		    	$this->_updateMovieZone($movie, $zones);
		    	// 更新movie_v_showtime
		    	$this->_updateMovieShowtime($movie, $showtimes);
		    	// 更新 movie_online_map
		    	$this->_updateOnlineMap($movie, $online_options);
		    	// 更新用户相关信息
		    	$this->_updateMovieUser($movie);
		    	// 更新电影相似度
	    		$this->_updateMovieRelateWmean($movie, $tags, $zones, $showtimes);
			}
			$this->success('',$online_list);
		} else {
			$this->success('ok');
		}
    }
    
   
     /**
     * 获取标签，区域，上映时间的ID
     * @param Array $movie 一条电影记录
     * Enter description here ...
     */
    private function _getTagsZoneTimeIds($movie){
    	$rst = array('rst'=>false);
    	if (empty($movie['tags']) || empty($movie['zone']) || empty($movie['showtime'])){
    		$rst['msg'] = '额。。貌似电影参数不全';
    		return $rst;
    	}
    	
    	$tags = explode('|',$movie['tags']);
    	if (empty($tags)) {
    		$rst['msg'] = '木油Tag';
    		return $rst;
    	}
    	
    	$map = array('name'=> array('in',$tags));
    	$ids = D('MovieTag')->where($map)->getField('id,name');
    	$rst['tags'] = $ids;
    	if (count($tags) != count($ids)) {
    		$rst['msg'] = 'Tag数据对不上号';
    		return $rst;
    	}
    	
    	$map = array('name'=> $movie['zone']);
    	$ids = D('MZone')->where($map)->getField('id,name');
    	$rst['zones'] = $ids;
    	if (empty($ids)) {
    		$rst['msg'] = '地区数据查不到';
    		return $rst;
    	}
    	
    	$map = array('name'=> $movie['showtime']);
    	$ids = D('MShowtime')->where($map)->getField('id,name');
    	$rst['showtimes'] = $ids;
    	if (empty($ids)) {
    		$rst['msg'] = '上映时间查不到';
    		return $rst;
    	}
    	$rst['rst'] = true;
    	return $rst;
    }
    
	/**
     * 修改Tag_Map; 2014年4月2日12:35:21 上线只需要管jian0 的记录，jian1的在影片评级时，自动插入
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     * @param unknown_type $zones
     * @param unknown_type $showtimes
     */
    private function _updateTagsMap($movie, $tags, $zones, $showtimes){
    	$tags[] = 0; $zones[] = 0; $showtimes[] = 0;  // 先给三个加一个初始值，0 代表全部
    	foreach ($tags as $tag) {
    		foreach ($zones as $zone) {
    			foreach ($showtimes as $showtime) {
    				$onlineMapTags = D('OnlineMapTags');
    				$map = array( 'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime , 'jian'=>0);
    				$tmp = $onlineMapTags->where($map)->field('id,tag_id,zone_id,showtime_id,jian,match_movie')->select();
    				if (empty($tmp)) { 
    					$data = array (
    						'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime, 'jian' => 0,
    						'movie_count' => 1, 'match_movie' => $movie['id'], 'last_movie_name' => $movie['name'],
    						'last_movie_pic' => $movie['bpic'], 'last_movie_id' => $movie['id']
    					);
    					$onlineMapTags->create($data);
    					if(!$onlineMapTags->add()) { // 先插入jian=0 （代表全部）
    						$this->error('插入movie_tags_map出错jian0');
    					} 
    				} elseif (count($tmp) == 1) { // 数据库里有一条
    					foreach ($tmp as $t) { 
    						$data = array ('id' => $t['id'], 'last_movie_name' => $movie ['name'], 'last_movie_pic' => $movie ['bpic'], 'last_movie_id' => $movie ['id'] );
							$match_movie = empty($t['match_movie']) ? array() : explode ( ',', $t['match_movie'] );

							$tmp_value = array();
							foreach ( $match_movie as $key => $id ) { // 修复数据库中重复的数据
								if ($id == $movie ['id'] || isset($tmp_value[$id])) { 
									unset ( $match_movie [$key] );
								} else {
									$tmp_value[$id] = true;
								}
							}
							
							$var = array_unshift ($match_movie, $movie ['id']); // 压入第一个元素
							$data ['movie_count'] = count($match_movie);
							$data ['match_movie'] = implode ( ',', $match_movie );
							$onlineMapTags->create($data);
							if (false === $onlineMapTags->save()){
								$this->error('更新movie_tags_map出错 id:'.$t['id'] );
							}
    					}
    				} else{  // if (count($tmp) > 1)
    					$update = false; //关闭更新
    					 // 这个时候大于两条记录，那就应该报错了
    					$str = array();
    					foreach ($tmp as $t) {
    						$str [] = $t['id'];
    					}
    					$this->error('Tags_map 表数据混乱'. implode(',', $str));
    				} // end if count($tmp)
    				
    			}// end foreach shwotime
    			
    		} // end foreach zone
    		
    	}// end foreach tag
    }
    
 
    /**
     * 更新 m_tags movie_v_tag
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     */
    private function _updateMovieTag($movie,$tags){
    	$map = array('id' => array('in', $tags));
    	if (!D('MovieTag')->where($map)->setInc('tag_times')) {
    		$this->error('数据库错误');
    	}
    	
    	foreach ($tags as $tag) {
    		$movieUTag = D('MovieUTag');
    		$map = array('tag_id'=> $tag, 'movie_id'=>$movie['id']);
    		$id = $movieUTag->where($map)->getField('id');
    		if (isset ($id)) {
    			$data = array('id'=>$id,'tag_times' => array('exp', 'tag_times+1'));
    			$movieUTag->create($data);
    			if (false === $movieUTag->save()){
    				$this->error('更新movie_v_tag出错');
    			}
    		} else {
    			$data = array('movie_id' => $movie['id'], 'tag_id' => $tag, 'tag_times' => 1, 'level'=>1);
    			$movieUTag->create($data);
    			if (false === $movieUTag->add()){
    				$this->error('插入movie_v_tag出错');
    			}
    		}
    	}
    }
    
    /**
     * 更新 movie_v_zone
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $zones
     */
    private function _updateMovieZone($movie, $zones) {
    	foreach ($zones as $zone) {
    		$movieUZone = D('MovieUZone');
    		$map = array('zone_id'=> $zone, 'movie_id'=>$movie['id']);
    		$id = $movieUZone->where($map)->getField('id');
    		if (isset ($id)) {
    			$data = array('id'=>$id);
    			$movieUZone->create($data);
    			if (false === $movieUZone->save()) {
    				$this->error('更新movie_v_zone出错');
    			}
    		} else {
    			$data = array('movie_id' => $movie['id'], 'zone_id' => $zone);
    			$movieUZone->create($data);
    			if (false === $movieUZone->add()) {
    				$this->error('插入movie_v_zone出错');
    			}
    		}
    	}
    }
    
    /**
     * 更新 movie_v_showtime
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $showtimes
     */
	private function _updateMovieShowtime($movie, $showtimes) {
    	foreach ($showtimes as $showtime) {
    		$movieUShowtime = D('MovieUShowtime');
    		$map = array('showtime_id'=> $showtime, 'movie_id'=>$movie['id']);
    		$id = $movieUShowtime->where($map)->getField('id');
    		if (isset ($id)) {
    			$data = array('id'=>$id);
    			$movieUShowtime->create($data);
    			if (false === $movieUShowtime->save()){
    				$this->error('更新movie_v_showtime出错');
    			}
    		} else {
    			$data = array('movie_id' => $movie['id'], 'showtime_id' => $showtime);
    			$movieUShowtime->create($data);
    			if(!$movieUShowtime->add()){
    				$this->error('插入movie_v_showtime出错');
    			}
    		}
    	}
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
    private function _updateOnlineMap($movie, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			$online_time = is_string($ppc['position']) ? $ppc['position'] : date('Y-m-d H:i:s', NOW_TIME);
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$onlineMapPC = D('OnlineMapPC');
    			$list = $onlineMapPC->where($map)->field('id,online_movie,await_movie,online_movie_time,await_movie_time')->select();
    			if (empty($list)) { // 不存在则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					'online_movie' => $movie['id'], 'await_movie' => '',
    					'online_movie_time' => $online_time, 'await_movie_time'=> '',
    					'online_movie_count' => 1, 'await_movie_count' => 0
    				);
    				$onlineMapPC->create($data);
    				if (false === $onlineMapPC->add()) {
    					$this->error('插入渠道上线表出错');
    				}
    			} else { // 存在则跟新
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('movie_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				$id = $list[0]['id'];
    				$online_movie = empty($list[0]['online_movie']) ? array() : explode(',', $list[0]['online_movie']);
    				$online_movie_time = empty($list[0]['online_movie_time']) ? array() : explode(',', $list[0]['online_movie_time']);
    				$await_movie = empty($list[0]['await_movie']) ? array() : explode(',', $list[0]['await_movie']);
    				$await_movie_time = empty($list[0]['await_movie_time']) ? array() : explode(',', $list[0]['await_movie_time']);
    				
    				$tmp_value= array();
    				$insert_key = -1;
    				foreach ($online_movie as $key=>$val) { // 修复数据
    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
    						unset($online_movie[$key], $online_movie_time[$key]);
    					} else {
    						if ($online_movie_time[$key] > $online_time) {
    							$insert_key = $key;
    						}
    						$tmp_value[$val] = true;
    					}
    				}
    				if ($insert_key < 0) { // 在最前头插入
    					array_unshift($online_movie, $movie['id']);
    					array_unshift($online_movie_time, $online_time);
    				} else {
    					$tmp_online_movie = array(); $tmp_online_movie_time = array();
    					foreach ($online_movie as $key=>$val) {
    						$tmp_online_movie[] = $online_movie[$key];
    						$tmp_online_movie_time[] = $online_movie_time[$key];
    						if ($key == $insert_key) { // 在这个Key后面插入
    							$tmp_online_movie[] = $movie['id'];
    							$tmp_online_movie_time[] = $online_time;
    						}
    					}
    					$online_movie = $tmp_online_movie; $online_movie_time = $tmp_online_movie_time;
    					unset($tmp_online_movie, $tmp_online_movie_time);
    				}
    				
					if ($ppc['pub_channel'] != 'all' || $ppc['position']<0){ // 针对all渠道，这里有修改，并且position>=0，不做处理 
						$tmp_value = array();
	    				foreach ($await_movie as $key=>$val) { // 删除等待区域的数据
	    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
	    						unset($await_movie[$key], $await_movie_time[$key]); 
	    					} else {
	    						$tmp_value[$val] = true;
	    					}
	    				}
					}
    				
    				$data = array(
    					'id' => $id,
    					'online_movie' => implode(',',$online_movie), 'await_movie' => implode(',',$await_movie),
    					'online_movie_time' => implode(',', $online_movie_time), 'await_movie_time'=> implode(',', $await_movie_time),
    					'online_movie_count' => count($online_movie), 'await_movie_count' => count($await_movie)
    				);
    				$onlineMapPC->create($data);
    				if (false === $onlineMapPC->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    			}
    		}
    	}
    }
    
    /**
     * 进入等待上线的列表 
     * 修正上线列表里的数据，新增等待区域列表数据
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $ppc_list
     */
    private function _awaitOnlineMap($movie, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$onlineMapPC = D('OnlineMapPC');
    			$list = $onlineMapPC->where($map)->field('id,online_movie,await_movie,online_movie_time,await_movie_time')->select();
    			if (!empty($list)) { // 如果存在，则修改
    				if (count($list) > 1) {
    					$str = array();
    					foreach ($list as $item) {
    						$str [] = $item['id'];
    					}
    					$this->error('movie_online_map 有大于1条同样的记录'.implode(',', $str));
    				}
    				
    				$id = $list[0]['id'];
    				$online_movie = empty($list[0]['online_movie']) ? array() : explode(',', $list[0]['online_movie']);
    				$online_movie_time = empty($list[0]['online_movie_time']) ? array() : explode(',', $list[0]['online_movie_time']);
    				$await_movie = empty($list[0]['await_movie']) ? array() : explode(',', $list[0]['await_movie']);
    				$await_movie_time = empty($list[0]['await_movie_time']) ? array() : explode(',', $list[0]['await_movie_time']);
    				
    				if ($ppc['pub_channel'] != 'all'){ // all渠道不需要
    					$tmp_value= array();
	    				foreach ($online_movie as $key=>$val) { // 修复数据
	    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
	    						unset($online_movie[$key], $online_movie_time[$key]);
	    					} else {
	    						$tmp_value[$val] = true;
	    					}
	    				}
    				}
    				
					$tmp_value = array();
					$insert_key = -1;
    				foreach ($await_movie as $key=>$val) { // 删除等待区域的数据
    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
    						unset($await_movie[$key], $await_movie_time[$key]); 
    					} else {
    						if ($await_movie_time[$key] < $ppc['position']) {
    							$insert_key = $key;
    						}
    						$tmp_value[$val] = true;
    					}
    				}
    				
    				if ($insert_key < 0) { // 在最前头插入
    					array_unshift($await_movie, $movie['id']);
    					array_unshift($await_movie_time, $ppc['position']);
    				} else {
    					$tmp_await_movie = array(); $tmp_await_movie_time = array();
    					foreach ($await_movie as $key=>$val) {
    						$tmp_await_movie[] = $await_movie[$key];
    						$tmp_await_movie_time[] = $await_movie_time[$key];
    						if ($key == $insert_key) { // 在这个Key后面插入
    							$tmp_await_movie[] = $movie['id'];
    							$tmp_await_movie_time[] = $ppc['position'];
    						}
    					}
    					$await_movie = $tmp_await_movie; $await_movie_time = $tmp_await_movie_time;
    					unset($tmp_await_movie, $tmp_await_movie_time);
    				}
    				
    				$data = array(
    					'id' => $id,
    					'online_movie' => implode(',',$online_movie), 'await_movie' => implode(',',$await_movie),
    					'online_movie_time' => implode(',', $online_movie_time), 'await_movie_time'=> implode(',', $await_movie_time),
    					'online_movie_count' => count($online_movie), 'await_movie_count' => count($await_movie)
    				);
    				$onlineMapPC->create($data);
    				if (false === $onlineMapPC->save()) {
    					$this->error('更新渠道上线表出错');
    				}
    				
    			} else { // 不存在，则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					'online_movie' => '', 'await_movie' => $movie['id'],
    					'online_movie_time' => '', 'await_movie_time'=> date('Y-m-d H:i:s', $ppc['position']),
    					'online_movie_count' => 0, 'await_movie_count' => 1
    				);
    				$onlineMapPC->create($data);
    				if (false === $onlineMapPC->add()) {
    					$this->error('插入渠道上线表出错');
    				}
    			}
    			
    		}
    	}
    }
    
    /**
     * 更新用户电影关系表格 (user_work_map,movie_v_user,movie_user 更新)
     * Enter description here ...
     * @param unknown_type $movie
     */
    private function _updateMovieUser($movie){
		$grapher = empty($movie['grapher']) ? array() : explode(',', $movie['grapher']);
		foreach ($grapher as $member_id) {
			$memberWorkMap = D('MemberWorkMap');
			$map = array('user_id' => $member_id);
			$tmp = $memberWorkMap->where($map)->field('id,online_work')->select();
			$member_work_count = 0; // 用户影片数量
			$member_work_movie_ids = array(); // 用户影片ID，用于跟新操作
			$is_in_worklist = false;
			if (empty($tmp)) { // 木油，插入
				$data = array('user_id'=>$member_id, 'online_work'=>$movie['id'], 'online_work_count'=>1);
				$memberWorkMap->create($data);
				if (false === $memberWorkMap->add()){
					$this->error('插入用户电影表出错');
				}
				$member_work_count = 1;
				$member_work_movie_ids[] = $movie['id'];
			} elseif (count($tmp)>1) { // 如果大于1
				$str = array();
				foreach ($tmp as $l) {
					$str [] = $l['id'];
				}
				$this->error('user_work_map 表数据损坏'.implode(',', $str));
			} else { // 正常情况
				$online_work = empty($tmp[0]['online_work']) ? array() : explode(',', $tmp[0]['online_work']);
				$tmp_value = array();
				foreach ($online_work as $key=>$val) { // 修复数据
					if ($val == $movie['id']) {
						$is_in_worklist = true; // 以前发表过了。
    					unset($online_work[$key]);
    				} elseif (isset($tmp_value[$val])) {
    					unset($online_work[$key]);
    				} else {
    					$tmp_value[$val] = true;
    				}
				}
				$var = array_unshift ($online_work, $movie ['id']); // 压入第一个元素
				$data = array(
					'id' => $tmp[0]['id'],
					'online_work' => implode(',', $online_work),
					'online_work_count' => count($online_work)
				);
				
				$memberWorkMap->create($data);
				if(false === $memberWorkMap->save()){
					$this->error('更新用户作品表出错');
				}
				$member_work_count = count($online_work);
				$member_work_movie_ids = $online_work;
			}
			// -------- user_work_map 更新完毕
			
			// ------ movie_v_user 开始
			$movieUMember = D('MovieUMember');
			$map = array('user_id' => $member_id, 'movie_id' => $movie['id']);
			$id = $movieUMember->where($map)->getField('id');
			if (empty($id)) { // 冇记录，插入
				$data = array('user_id' => $member_id, 'movie_id' => $movie['id']);
				$movieUMember->create($data);
				if (false === $movieUMember->add()) {
					$this->error('插入movie_v_user出错');
				}
			}
			// ------ movie_v_user 结束
			
			// 更新用户的作品数目以及通知操作
			$member = D('Member');
			$map = array('id'=>$member_id);
			$id = $member->where($map)->getField('id');
			if (!empty($id)) {
				$new_flag = false; // 是否插入消息列表的标志
				if (!$is_in_worklist) { 
					// 为用户添加动态新闻 //用户1号为图解电影官方
					$new_msg =  'ヽ(･∀･)ﾉ您的作品《'.$movie['name'].'》'.($movie['jian'] == 1 ? '[精品推荐]' : '').' 审核成功上线！';
					$memberNew = D('MemberNew');
					$data = array(
						'user_id'=>1, 'to_user_id'=>$member_id, 
						'comment_content'=>$new_msg, 'reply_comment_id' => 0, 'readed' => 0,
						'reply_from' => 'user', 'reply_from_data' => '1',
						'pre_length' => 0, 'secret_send' => 0
					);
					$memberNew->create($data);
					$new_flag = $memberNew->add();
				}
				
				$movieModel = D('Movie');
				$map = array('id'=> array('in',$member_work_movie_ids));
				$field = 'sum(ding) AS ding, sum(keep) AS keep, sum(cai) AS cai, sum(played) AS played, sum(share) as share';
				$list = $movieModel->where($map)->field($field)->find();
				
				$data = array(
					'id'=>$member_id, 
					'stat_work' => $member_work_count, 
					'stat_belike'=>$list['ding'],
					'stat_becai'=>$list['cai'],
					'stat_beshare'=>$list['share'],
					'stat_bekeep'=>$list['keep'],
					'stat_beplayed'=>$list['played']
				);
				if ($new_flag) {  // 未读信息+1
					$data['stat_user_new_unread'] = array('exp', ' stat_user_new_unread+1 ');
				}
				$member->create($data);
				if (false === $member->save()){  // 更新用户信息
					$this->error('更新用户信息出错');
				}
				
			}else{ //未找到用户，不管
				
			}
		}
    }
    
    /**
     * 更新影片相似度
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     * @param unknown_type $zones
     * @param unknown_type $showtimes
     */
    private function _updateMovieRelateWmean($movie, $tags, $zones, $showtimes){
    	// step1 找出全部上线影片
    	$onlineMapPC = D('OnlineMapPC');
    	$map = array('pub_channel'=>'all', 'pub_platform' => 'all');
    	$tmp = $onlineMapPC->where($map)->field('online_movie')->find();
    	if (empty($tmp)) {
    		$this->error('movie_online_map表中木油all记录');
    	}
    	$online_movie = $tmp['online_movie']; // 所有上线影片记录
    	
    	// step2 找到所有同类型的上线电影
    	$movie_tags = array();
    	$map = array('movie_id' => array('in', $online_movie), 'tag_id' => array('in', $tags));
    	$list = D('MovieUTag')->where($map)->field('movie_id,tag_id')->select();
    	if (!empty($list)) {
    		foreach ($list as $val) {
    			$movie_tags[$val['movie_id']][] = $val['tag_id'];
    		}
    	}
    	
    	
    	// step2 找到所有同区域的上线电影
    	$movie_zones = array();
    	$map = array('movie_id' => array('in', $online_movie), 'zone_id' => array('in',$zones));
    	$list = D('MovieUZone')->where($map)->field('movie_id,zone_id')->select();
    	if (!empty($list)) {
    		foreach ($list as $val) {
    			$movie_zones[$val['movie_id']][] = $val['zone_id'];
    		}
    	}
    	
    	// step3 找到所有同上映时间(+-1)的上线电影
    	$movie_showtimes = array();
    	// 扩大区间，每个区间+-1 查询相邻俩区间值
    	foreach ($showtimes as $key=>$val) { 
    		$times[] = $val;
    		$pre = $val-1;
    		$next = $val + 1;
    		if ( $pre> 0 && !in_array($pre,$times)) {
    			$times[] = $pre;
    		}
    		if (!in_array($next, $times)) {
    			$times[] = $next;
    		}
    	}
    	$map = array('movie_id' => array('in', $online_movie), 'showtime_id' => array('in',$times));
    	$list = D('MovieUShowtime')->where($map)->field('movie_id,showtime_id')->select();
    	if (!empty($list)) {
    		foreach ($list as $val) {
    			$movie_showtimes[$val['movie_id']][] = $val['showtime_id'];
    		}
    	} 
    	
    	// step4 求交集
    	$all_online_movie_ids = array_merge(array_keys($movie_tags), array_keys($movie_zones), array_keys($movie_showtimes));
    	
    	
    	// step5 遍历与该影片相关的所有影片，并且插入数据库中。
    	foreach ($all_online_movie_ids as $movie_id) {
    		if ($movie_id == $movie['id']) { // 如果相等，则修复之前的数据，然后再继续
    			D('MovieRelateWmean')->where(array('movie_id'=>$movie['id'], 'to_movie_id'=>array('eq',$movie_id,'or')))->delete();
    			continue;
    		}
    		
    		$st = isset($movie_tags[$movie_id]) ? count($movie_tags[$movie_id]) : 0;
    		$sz = isset($movie_zones[$movie_id]) ? count($movie_zones[$movie_id]) : 0;
    		$ss = 0;
    		if (isset($movie_showtimes[$movie_id])) {
    			foreach ($movie_showtimes[$movie_id] as $val) {
    				if (in_array($val,$showtimes)) {
    					$ss++;
    				} else {
    					$ss += 0.5; // 同邻年代+0.5
    				}
    			}
    		}
    		//计算公式-加权平均值,
			/*
				movie ralate wmean =  (St*0.65 + Ss*0.15 + Sz*0.2)
			*/
    		$wmean =$st*0.65 + $ss*0.15 + $sz*0.2;
			$wmean = sprintf("%.5f",$wmean);
			$movieRelateWmean = D('MovieRelateWmean');
			$map = array('movie_id'=>$movie['id'], 'to_movie_id'=>$movie_id);
			$id = $movieRelateWmean->where($map)->getField('id');
			if (empty($id)) { // 添加记录
				$data = array('movie_id'=>$movie['id'], 'to_movie_id'=>$movie_id, 'relate_wmean'=>$wmean);
				$movieRelateWmean->create($data);
				if (false === $movieRelateWmean->add()) {
					$this->error('插入movie_relate_wmean出错 No1');
				}
			} else { // 跟新记录
				$data = array('id'=>$id, 'relate_wmean'=>$wmean);
				$movieRelateWmean->create($data);
				if (false === $movieRelateWmean->save()) {
					$this->error('更新movie_relate_wmean出错 No1');
				}
			}
			
			// 反过来再更新一条
    		$map = array('to_movie_id'=>$movie['id'], 'movie_id'=>$movie_id);
			$id = $movieRelateWmean->where($map)->getField('id');
			if (empty($id)) { // 添加记录
				$data = array('to_movie_id'=>$movie['id'], 'movie_id'=>$movie_id, 'relate_wmean'=>$wmean);
				$movieRelateWmean->create($data);
				if (false === $movieRelateWmean->add()) {
					$this->error('插入movie_relate_wmean出错 No2');
				}
			} else { // 跟新记录
				$data = array('id'=>$id, 'relate_wmean'=>$wmean);
				$movieRelateWmean->create($data);
				if (false === $movieRelateWmean->save()){
					$this->error('更新movie_relate_wmean出错 No2');
				}
			}
			
    	}// step5 over
    }
    
	/**
     * 重写error方法
     * Enter description here ...
     */
    protected function error($msg, $code = 0){
    	$use_time = round(microtime(TRUE) - $this->_startTime , 5);
    	$rst = array('code'=>$code, 'msg'=>$msg, 'time'=>$use_time);
    	$str = json_encode($rst);
    	echo $str;
    	import('@.ORG.LoggerWritter');
    	$log = new LoggerWriter(APP_PATH.'Logs/auto/');
    	$log->add(date('Y-m-d H:i:s').' '.$str.' '.get_client_ip().PHP_EOL);
    	exit();
    }
    
    /**
     * 重写success方法
     * @see Action::success()
     */
    protected function success($msg='', $data = array()) {
    	$use_time = round(microtime(TRUE) - $this->_startTime , 5);
    	$rst = array('code'=>1, 'msg'=>$msg, 'data'=>$data, 'time'=>$use_time);
    	$str = json_encode($rst);
    	echo $str;
    	import('@.ORG.LoggerWritter'); 
    	$log = new LoggerWriter(APP_PATH.'Logs/auto/');
    	$log->add(date('Y-m-d H:i:s').' '.$str.' '.get_client_ip().PHP_EOL);
    }
}
?>