<?php
// 影片模块
class MovieAction extends CommonAction {
 	public function _filter(&$map)
    {
        $tags = I('tags');
        if (!empty($tags)) {
        	$map['tags'] = array('like',"%$tags%");
        }
        $name = I('name');
        if (!empty($name)) {
        	$map['name'] = array('like',"%$name%");
        }
        $grapher = I('grapher',NULL);
        if (!empty($grapher)) {
        	$map['_string'] = " instr(CONCAT(',',grapher,','), ',$grapher,') > 0 ";
        }
    }
    
    function _before_index(){
    	$grapher_id = I('grapher_id',NULL);
    	if (isset($grapher_id)) {
    		$_GET['grapher'] = $grapher_id;
    	}
    }
	
    /**
     * 影片列表
     * @see CommonAction::index()
     */
    function index(){
    	$online = I('online');
    	if ($online > 0) { // 已经上线 或 未上线的
    		$rst = $this->_getOnlineIdList();
    		$online_movies = $rst['online']; // 获取已经上线的电影列表
    		$online_movie_ids = array_keys($online_movies); // 获取上线电影ID集合
    		$await_movies = $rst['await']; // 获取等待上线的电影列表
    		$await_movie_ids = array_keys($await_movies); // 获取等待上线电影ID集合
    		
    		$orderby = I('orderby');
    		$field = (empty($orderby) || $orderby == 'online_time') ? 'id' : $orderby;
    		$order = I('order');
    		$order = empty($order) ? 'desc' : $order;
    		
    		$map = $this->_search(); // 获取查询条件
    		$this->_filter($map);
    		$movie = D('Movie');
    		$all_movie = $movie->where($map)->getField('id,'.$field, true);
    		$all_movie_ids = array_keys($all_movie);
    		$movie_ids = array();
    		if ($online == 1) { // 已经上线
    			$movie_ids = array_intersect($online_movie_ids, $all_movie_ids); // 求交集，得到满足条件的IDs
    		} elseif ($online == 2) { // 未上线
    			$movie_ids = array_diff($all_movie_ids, $online_movie_ids, $await_movie_ids); // 求差集，得到满足条件的IDs
    		} elseif ($online == 3) { // 即将上线
    			$movie_ids = array_intersect($await_movie_ids, $all_movie_ids); // 求交集，得到即将上线电影的IDs
    		}
    		if (!empty($movie_ids)) {
    			if (empty($orderby) || $orderby == 'id') {
    				$order == 'desc' ? rsort($movie_ids) : sort($movie_ids);
    			} elseif ($orderby == 'online_time') { // 根据上线表情况来判断
    				if ($online == 1 || $online == 3) { // 只有在已经上线的情况，才有按照上线时间排序
    					// 由于数据库表中的ID已经是通过上线时间顺序降序排列的,如果是升序排列，变换顺序即可
    					$movie_ids = $order == 'desc' ? array_values($movie_ids) : array_reverse($movie_ids);	
    				}
    				
    			} else {  // 根据movie表的字段情况来判断
    				// 由于从movie表中查出来的顺序是乱序的，所以这部分需要自己排序了
    				$sort_ids = array();
    				foreach ($movie_ids as $id) { 
    					$sort_ids[$id] = $all_movie[$id];
    				}
    				$order == 'desc' ? arsort($sort_ids) : asort($sort_ids);
    				$movie_ids = array_keys($sort_ids);
    			}
    			$this->_getMovieList($movie_ids, $map);
    		}
    		
    		
    	} else {
    		//列表过滤器，生成查询Map对象
	        $map = $this->_search();
	        if (method_exists($this, '_filter')) {
	            $this->_filter($map);
	        }
	        $name = $this->getActionName();
	        $model = D($name);
	        if (!empty($model)) {
	            $this->_list($model, $map);
	        }
    	}
    	$this->_displayList();
    	$this->assign('ji',array(0=>'未',8=>'S',7=>'A',6=>'B',5=>'C'));
    	$this->display();
    }
    
    /**
     * 绑定豆瓣或者时光的影片链接
     */
    public function doBindLink(){
    	$movie_id = I('movie_id');
    	$douban_url = I('douban_url');
    	$shiguang_url = I('shiguang_url');
    	$douban_id = $this->_getIdByUrl($douban_url, 'douban');
    	$shiguang_id = $this->_getIdByUrl($shiguang_url, 'shiguang');
    	if (!empty($douban_url) && empty($douban_id)) {
    		$this->error('豆瓣链接解析错误');
    	}
    	if (!empty($shiguang_url) && empty($shiguang_id)) {
    		$this->error('时光链接解析错误');
    	}
    	$flag = array('douban'=>true,'shiguang'=>true);
    	if (empty($movie_id)) {$this->error('参数错误');}
    	$map = array('movie_id'=>$movie_id);
    	$MdbMovieLink = D('MdbMovieLink');
    	$list = $MdbMovieLink->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			$data = array();
    			if ($item['link_type'] == 1) { // 豆瓣链接
    				$data = array('id'=>$item['id'],'link_url'=>$douban_url);
    				$flag['douban'] = false;
    			} else if ($item['link_type'] == 2) { // 时光链接
    				$data = array('id'=>$item['id'],'link_url'=>$shiguang_url);
    				$flag['shiguang'] = false;
    			}
    			if (empty($data)) {
    				$this->error('链接类型判断错误哟');
    			}
    			if (empty($data['link_url'])) {
    				if (false === $MdbMovieLink->where(array('id'=>$item['id']))->delete()) {
    					$this->error('修改数据库失败');
    				}
    			} else {
	    			if (false === $MdbMovieLink->save($data)) {
	    				$this->error('修改数据库失败');
	    			}
    			}
    		}
    	}
    	
    	foreach ($flag as $key=>$insert) {
    		if ($insert) {
    			$data = array('movie_id'=>$movie_id);
    			if ($key=='douban') {
    				$data['link_type'] = 1;
    				$data['link_url'] = $douban_url;
    			} else if ($key == 'shiguang') {
    				$data['link_type'] = 2;
    				$data['link_url'] = $shiguang_url;
    			}
    			if (!empty($data['link_url'])){ // 只有链接不为空的时候才插入
	    			if (false === $MdbMovieLink->create($data)) {
	    				$this->error($MdbMovieLink->getError());
	    			}
	    			if (false === $MdbMovieLink->add()) {
	    				$this->error('插入数据库失败');
	    			}
    			}
    		}
    	}
    	
    	// step3 更新mdb_film_v_movie
    	$MdbFilmMovie = D('MdbFilmMovie');
    	$map = array('movie_id'=>$movie_id,'open'=>1);
    	if (false === $MdbFilmMovie->where($map)->delete()){
    		$this->error('删除电影数据库与图解的关系失败');
    	}
    	$Film = D('Film');
    	$film_ids = array();
    	if (!empty($douban_id)) {
    		$map = array('douban_key'=>$douban_id);
    		$film_ids = $Film->where($map)->getField('id,name');
    	}
    	if (!empty($shiguang_id)) {
    		$map = array('mtime_key'=>$shiguang_id);
    		$tmp_ids = $Film->where($map)->getField('id,name');
    		if (!empty($tmp_ids)) {
    			foreach ($tmp_ids as $key=>$item) {
    				$film_ids[$key] = $item;
    			}
    		}
    	}
    	if (!empty($film_ids)) {
    		$dataList = array();
    		$time = toDate(NOW_TIME);
    		foreach ($film_ids as $film_id=>$film_name) {
    			$dataList[] = array('movie_id'=>$movie_id,'film_id'=>$film_id,'open'=>1,'add_time'=>$time);
    		}
    		if (false === $MdbFilmMovie->addAll($dataList)) {
    			$this->error('最后一步，插入图解电影关系表错误');
    		}
    	}
    	$this->success('更新成功');
    }
    
    private function _getIdByUrl($url,$type){
    	if ($type == 'douban') {
    		$tmp = explode('/subject/',$url);
    	} else if ($type=='shiguang') {
    		$tmp = explode('mtime.com/', $url);
    	}
    	if (empty($tmp) || count($tmp)<2) { return 0;}
    	$tmp = explode('/', $tmp[1]);
    	if (!is_numeric($tmp[0])) {return 0;}
    	return intval($tmp[0]);
    }
    
    private function _displayList(){
    	$list = $this->get('list');
    	if (!empty($list)) {
    		// 获取图解作者数据
    		$Member = D('Member');
    		$mem_id = array();
    		foreach ($list as $item) {
    			if (!empty($item['grapher'])) {
    				$tmp = explode(',', $item['grapher']);
    				foreach ($tmp as $id) {
    					$mem_id[$id] = true;
    				}
    			}
    		}
    		$map = array('id' => array('in',array_keys($mem_id)));
    		$mem_tmp = $Member->where($map)->getField('id,name');
    		foreach ($list as $key=>$item) {
    			$list[$key]['grapher_str'] = array();
    			if (!empty($item['grapher'])) {
    				$tmp = explode(',', $item['grapher']);
    				foreach ($tmp as $id) {
    					$list[$key]['grapher_str'][] = array('id'=>$id,'name'=>$mem_tmp[$id]);
    				}
    			}
    		}
    		
    		// 获取上线时间数据
    		$onlineMapPC = D('OnlineMapPC');
    		$map = array('pub_platform'=>'android','pub_channel'=>'xiaomi');
    		$one = $onlineMapPC->where($map)->field('online_movie,online_movie_time,await_movie,await_movie_time')->find();
    		if (!empty($one)) {
    			$tmp_online_movie = explode(',', $one['online_movie']);
    			$tmp_online_time = explode(',', $one['online_movie_time']);
    			$tmp_await_movie = explode(',', $one['await_movie']);
    			$tmp_await_time = explode(',', $one['await_movie_time']);
    			foreach ($tmp_online_movie as $key=>$m_id) {
    				$online_movie[$m_id] = $tmp_online_time[$key];
    			}
    			foreach ($tmp_await_movie as $key=>$m_id) {
    				$online_movie[$m_id] = $tmp_await_time[$key];
    			}
    			
    			foreach ($list as $key=>$item) {
    				$list[$key]['online_time'] = isset($online_movie[$item['id']]) ? $online_movie[$item['id']] : '<span class="red">-未上线-</span>'; 
    			}
    		}
    		
    		// 获取绑定豆瓣，时光链接数据
    		$MdbMovieLink = D('MdbMovieLink');
    		foreach ($list as $key=>$item) {
    			$map = array('movie_id'=>$item['id']);
    			$list[$key]['link'] = $MdbMovieLink->where($map)->getField('link_type,link_url');
    		}
    		
    		// 获取关联剧集数据
    		$MovieSeason = D('MovieSeason');
    		foreach ($list as $key=>$item) {
    			$list[$key]['season'] = ''; 
    			if ($item['season_id']>0){
    				$map = array('id'=>$item['season_id']);
    				$list[$key]['season'] = $MovieSeason->where($map)->getField('name');
    			}
    		}
    		
    		// 2016年1月23日16:13:39 V4.8 版本获取是否推荐至首页标签
    		$Tuijian = D('HomeTuijian');
    		foreach ($list as $key=>$item) {
    			if ($item['vol_count']<=2) {
    				$map = array('t_id'=>$item['id'], 't_type'=>$item['vol_count']);
    				$tmp = $Tuijian->where($map)->getField('jian_time');
    				if (!empty($tmp)) {
    					$list[$key]['tuijian_time'] = $tmp;
    				}
    			}
    		}
    		
    		// 重新赋值list
    		$this->assign('list',$list);
    	}
    }
    
    /**
     * 获取列表数据
     * Enter description here ...
     * @param unknown_type $ids
     */
    private function _getMovieList($ids, $map, $sort='desc', $order='id'){
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
            	$list = D('Movie')->where($where)->select(); // 这里查出来是乱序的，所以需要进行处理
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
     * 获取已经上线的电影ID列表
     * Enter description here ...
     */
    private function _getOnlineIdList(){
    	$online_list = array();
    	$await_list = array();
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
    	
    	$onlineMapPC = D('OnlineMapPC'); // 各个平台渠道上线模型
    	$list = $onlineMapPC->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			if (!empty($item['online_movie'])) {
	    			$movie_ids = explode(',', $item['online_movie']);
	    			$online_times = explode(',', $item['online_movie_time']);
	    			foreach ($movie_ids as $key => $val) { // 直接用 key=value 免去求交集的烦恼。
	    				$online_list[$val] = $online_times[$key]; // 组成movie_id => 上线时间， 这样一个数组
	    			}
    			}
    			if (!empty($item['await_movie'])) {
    				$movie_ids = explode(',', $item['await_movie']);
    				$online_times = explode(',', $item['await_movie_time']);
    				foreach ($movie_ids as $key => $val) { // 直接用 key=value 免去求交集的烦恼。
	    				$await_list[$val] = $online_times[$key]; // 组成movie_id => 上线时间， 这样一个数组
	    			}
    			}
    		}
    	}
    	$rst = array(
    		'online' => $online_list,
    		'await' => $await_list
    	);
    	return $rst;
    } 
    
    
    /**
     * 插入数据前，做一个判断
     * Enter description here ...
     */
    function _before_insert(){
    	$arr = I('tagsArr');
    	if (empty($arr)) {
    		$this->error('类型必填');
    	}
    	$_POST['tags'] = implode('|', $arr);
    	$mark = I('mark');
    	if (!empty($mark)) {
    		$type = D('MovieTag');
    		$map = array('level' => 2, 'open' => 1);
    		$list = $type->where($map)->getField('name',true);
    		$marks = explode('|', $mark);
    		$dataList = array();
    		$time = toDate(NOW_TIME);
    		foreach ($marks as $item) {
    			if (!in_array($item, $list)) {
    				$dataList[] = array('name'=>$item,'level'=>2,'add_time'=>$time);
    			}
    		}
    		if (!empty($dataList)) {
    			$type->addAll($dataList);
    		}
    	}
    }
    
    /**
     * 同理，更新也要
     * Enter description here ...
     */
    function _before_update(){
    	$arr = I('tagsArr');
    	if (empty($arr)) {
    		$this->error('类型必填');
    	}
    	$_POST['tags'] = implode('|', $arr);
    	
    	$arr = array('limit_listshow','limit_search','limit_tag','limit_qrcode','limit_same','limit_grapher','limit_topic');
    	foreach ($arr as $val) {
    		$avg = I($val);
    		$_POST[$val] = !empty($avg) ? $avg : 0;
    	}
    	
    	$mark = I('mark');
    	if (!empty($mark)) {
    		$type = D('MovieTag');
    		$map = array('level' => 2, 'open' => 1);
    		$list = $type->where($map)->getField('name',true);
    		$marks = explode('|', $mark);
    		$dataList = array();
    		$time = toDate(NOW_TIME);
    		foreach ($marks as $item) {
    			if (!in_array($item, $list)) {
    				$dataList[] = array('name'=>$item,'level'=>2,'add_time'=>$time);
    			}
    		}
    		if (!empty($dataList)) {
    			$type->addAll($dataList);
    		}
    	}
    }
    
	function update() {
        $model = D('Movie');
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
        // 比较是否有修改 标签，地区，时间，推荐与否 (去掉了 jian)
        $rst = isEqueTags($data['tags'],$old_data['tags']);
        if (!$rst['rst'] || $data['zone'] != $old_data['zone'] || $data['zone'] != $old_data['zone'] || $data['showtime'] != $old_data['showtime']) { //不相等
        	$update = true;
        }
        
        if ($update) { // 判断是否在线，如果不在线，则不更新啦
        	$update = isOnlineByMovieId($data['id']);
        }
        
        if ($update) {
	        $nameIds = $this->_getTagsZoneTimeIds($old_data);
	    	if (!$nameIds['rst']) {
	    		$this->error($nameIds['msg']);
	    	}
	    	$tags1 = array_keys($nameIds['tags']);
	    	$zones1 = array_keys($nameIds['zones']);
	    	$showtimes1 = array_keys($nameIds['showtimes']);
	    	
	    	// 补全新data里没有的数据，用于上线，下线操作
	    	foreach ($old_data as $key=>$val) {
	    		if (!isset($data[$key])) { 
	    			$data[$key] = $val;
	    		}
	    	}
	    	
	    	$nameIds = $this->_getTagsZoneTimeIds($data);
	    	if (!$nameIds['rst']) {
	    		$this->error($nameIds['msg']);
	    	}
	    	$tags2 = array_keys($nameIds['tags']);
	    	$zones2 = array_keys($nameIds['zones']);
	    	$showtimes2 = array_keys($nameIds['showtimes']);
	    	
	    	// 先下线，再上线
	    	$this->_offTagsMap($old_data, $tags1, $zones1, $showtimes1);
	    	$this->_updateTagsMap($data, $tags2, $zones2, $showtimes2);
        }
        
        // 2015年5月26日16:04:51 修改影片二级标签
        $this->_updateMark($data);
        
        // 更新数据
        $list = $model->save($data);
        if (false !== $list) {
//        	$this->display('doOnline');
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
    
    // 影片上线页面
    function online(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$movie = D('Movie')->getById($id);
    	if (empty($movie)){
    		$this->error('参数错误');
    	}
    	$_fiter = array(
    		'total_size'=>'影片大小为空，不允许该操作',
    		'total_page' => '影片脚本为空，不允许上线',
    		'grapher' => '作者信息为空，请先绑定作者',
    		'bpic' => '还木油上传大~封面',
    		'spic' => '还木油上传大~封面'
    	);
    	foreach($_fiter as $key=>$val) {
    		if (empty($movie[$key])) {
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
		
		$onlineMapPC = D('OnlineMapPC');
		
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'online_movie' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\'')
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform,online_movie,online_movie_time')->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		foreach ( $list as $val ) {
			$movie_ids = explode(',', $val['online_movie']);
			$movie_times = explode(',', $val['online_movie_time']);
			$online_time = '';
			foreach ($movie_ids as $key => $ids) {
				if ($ids == $id) {
					$online_time = $movie_times[$key];
				}
			}
			
			$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $online_time);
			$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true; 
			
		}
		
		// 即将上线
    	$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'await_movie' => array('exp',' REGEXP \'^'.$id.'$|^'.$id.',|,'.$id.',|,'.$id.'$\'')
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform,await_movie,await_movie_time')->select();
		
		foreach ( $list as $val ) {
			$movie_ids = explode(',', $val['await_movie']);
			$movie_times = explode(',', $val['await_movie_time']);
			$online_time = '';
			foreach ($movie_ids as $key => $ids) {
				if ($ids == $id) {
					$online_time = $movie_times[$key];
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
    	$movie_id = I('id');
    	if (empty($movie_id)) {$this->error('参数错误');}
    	
    	$online_options = array();
    	$wait_online_list = array();
    	$rss_content = ''; // rss 生成结果
    	foreach ($online_list as $val) {
    		$option = I($val);
    		$tmp_time = I($val.',time');
    		$option_time = empty($tmp_time)?0 :strtotime($tmp_time.":00");
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		
    		if ($option_time - NOW_TIME > 0) { // 等待上线的电影
    			$wait_online_list[] = array(
    				'pub_platform' => $tmp[0],
	    			'pub_channel' => $tmp[1],
	    			'position' => date('Y-m-d H:i:s',$option_time)
    			);
    		} else {
    			$online_options[] = array(
	    			'pub_platform' => $tmp[0],
	    			'pub_channel' => $tmp[1],
	    			'position' => is_numeric($option) ? abs($option) : 0
	    		);
    		}
    	}
    	
    	$movie = D('Movie')->getById($movie_id);
    	if (empty($movie)) {$this->error('擦嘞，电影被删了');}
    	
    	if (!empty($online_options)) { // 当有电影现在上线时
    		// 添加全部渠道，全部平台上线
    		$online_options [] = array ('pub_platform' => 'all', 'pub_channel' => 'all', 'position' => 0 );
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
    		
    		// 调用接口更新RSS源
    		load('@.rss');
    		$rss = new  My_RSS();
    		$rss_content = $rss->create($online_options);
    		
    		// 对关注作者的用户，下发动态
    		// 2014年9月19日18:07:03，应为app不支持，所以这里先注释掉
    		//$this->_updateMemberNews($movie, $online_options);
    		
    		// 2014年12月29日14:57:44 下发关注过这部电影的百度Push
    		load('@.pushNotice3');
    		$pushNotice = new Push_Notice3();
	    	$message['title'] = $movie['name'];
			$message['description'] = $movie['sub_title'];
			$message['notification_basic_style'] = 7;
			$message['open_type'] = 3;
			$message['url'] = '';
			$message['user_confirm'] = 0;
			$message['notification_basic_style'] = 0x07;
			$message['custom_content']['gm_opt'] = 2;
			$message['custom_content']['gm_id'] = $movie_id."";
			$rst = $pushNotice->pushMessage_android($message, "MORMe_$movie_id",'Tag');
    	}
    	
    	if (!empty($wait_online_list)) {
    		// 添加全部渠道未上线列表
    		$wait_online_list [] = array ('pub_platform' => 'all', 'pub_channel' => 'all', 'position' => date('Y-m-d H:i:s',NOW_TIME));
    		$this->_awaitOnlineMap($movie,$wait_online_list);
    	}
    	
    	$this->success('上线成功,'.$rss_content);
//    	$this->display();
//    	exit();
    }
    
    /**
     * 实实在在的下线操作
     * Enter description here ...
     */
    public function doOffline(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$movie_id = I('id');
    	if (empty($movie_id)) {$this->error('参数错误');}
    	
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1]
    		);
    	}
    	
    	$movie = D('Movie')->getById($movie_id);
    	if (empty($movie)) {$this->error('擦嘞，电影被删了');}
    	
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	$onlineMapPC = D('OnlineMapPC');
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'online_movie' => array('exp',' REGEXP \'^'.$movie['id'].'$|^'.$movie['id'].',|,'.$movie['id'].',|,'.$movie['id'].'$\'')
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform')->select();
		
		$online_flag = false;
		$await_flag = false; // 是否在等待列表里
		$update_other_flag = false; // 修改其他信息（地区，时间，用户信息）等，开关变量
		$all_ppc_flag = false; // 是否在 all 平台，all 渠道，下线，开关变量
		$all_await_flag = false;
		if (!empty($list)) { // 上线平台为空，此时需要到等待列表里查找
			$online_flag = true;
			foreach ($list as $key=>$val) { // 只有针对已经上线的list，如果list 为空的话，就代表是全部渠道下线
				if (in_array($val['pub_platform'].",".$val['pub_channel'], $offline_list)){
					unset($list[$key]);
				}
			}
			$update_other_flag = empty($list);
		}
		
		// step3 找出在等待列表中的记录。
		$map = array ( // 四种情况  id | id, | ,id, | ,id
			'id' => array ('neq',1),
			'await_movie' => array('exp',' REGEXP \'^'.$movie['id'].'$|^'.$movie['id'].',|,'.$movie['id'].',|,'.$movie['id'].'$\'')
		);
		$list = $onlineMapPC->where($map)->field('pub_channel,pub_platform')->select();
		if (!empty($list)){
			$await_flag = true;
			foreach ($list as $key=>$val) { // 只有针对已经上线的list，如果list 为空的话，就代表是全部渠道下线
				if (in_array($val['pub_platform'].",".$val['pub_channel'], $offline_list)){
					unset($list[$key]);
				}
			}
			$all_await_flag = empty($list);
		}
		
		$all_ppc_flag = $update_other_flag || $all_await_flag;
		
		if (!$online_flag && !$await_flag) { // 既没有在已经上线的列表，又没有在等待列表里
			$this->error('还木油任何平台上线，就不能下线');
		}
		if ($all_ppc_flag) { // 如果是全部下线，则添加all平台，all渠道下线
			$offline_options[] = array(
	    		'pub_platform' => 'all',
	    		'pub_channel' => 'all',
				'await_flag' => $all_await_flag,
				'online_flag' => $update_other_flag 
	    	);
		}
		
		// 下线更新movie_online_map
    	$this->_offOnlineMap($movie, $offline_options);
    	
    	if ($update_other_flag) { // 只有全部下线，并且已经在上线列表里了，才进行操作
	    	$nameIds = $this->_getTagsZoneTimeIds($movie);
	    	if (!$nameIds['rst']) {
	    		$this->error($nameIds['msg']);
	    	}
	    	$tags = array_keys($nameIds['tags']);
	    	$zones = array_keys($nameIds['zones']);
	    	$showtimes = array_keys($nameIds['showtimes']);
	    	
	    	// 下线更新movie_tags_map
	    	$this->_offTagsMap($movie, $tags, $zones, $showtimes);
	    	
	    	// 下线更新用户信息
	    	$this->_offMovieUser($movie);
	    	
	    	// 下线跟新Tag
    		$this->_offMovieTag($movie, $tags);
    	}
    	
    	$rss_content = '';
    	// 调用接口更新RSS源
//     	load('@.rss');
//     	$rss = new  My_RSS();
//     	$rss_content = $rss->create($offline_options);
    	
    	$this->success('下线成功'.$rss_content);
//    	$this->display('doOnline');
    }
    
    
    /**
     * 绑定作者页面
     * Enter description here ...
     */
    function grapher(){
    	$list = D('MemberWorkMap')->field('user_id,online_work_count')->order('online_work_count desc')->limit(500)->select();
    	$member = array();
    	foreach ($list as $tmp) {
    		$member[$tmp['user_id']] = array('id'=>$tmp['user_id'],'works'=>$tmp['online_work_count']);
    	}
    	if (!empty($member)) {
	    	$map = array('id' => array('in', array_keys($member)));
	    	$list = D('Member')->where($map)->getField('id,name,email');
	    	foreach ($list as $key=>$val) {
	    		$member[$key]['name'] = $val['name'];
	    		$member[$key]['email'] = $val['email'];
	    	}
    	}
    	$this->assign('list', $member);
    	$this->display();
    }
    
    function updateGrapher(){
    	$id = I('id');
    	$grapher = trim(I('grapher'));
    	if (empty($id) || empty($grapher)) {
    		$this->error('参数错误');
    	}
    	$map = array('id' => $id);
    	$model = D('Movie');
    	$movie = $model->where($map)->find();
    	if (empty($movie)) {
    		$this->error('参数错误');
    	}
    	
    	// 比较是否有修改 作者，这里不能用$grapher != $movie['grapher'] 因为顺序是可以调换的
        $rst = isEqueTags($grapher,$movie['grapher'],',');
    	
    	if (!$rst['rst']) { // 不匹配
	    	// 验证用户ID是否非法
	    	$xxids = array();
	    	$grapher_ids = explode(',', $grapher);
	    	foreach ($grapher_ids as $to_id) {
	    		if (isset($xxids[$to_id])) {$this->error($to_id.' 作者ID重复了，亲~');}
	    		if (!is_numeric($to_id)) {$this->error($to_id." 附近的作者ID错误。");}
	    		$xxids[$to_id] = true;
	    	}
	    	$map = array('id'=> array('in', $grapher_ids));
	    	$mids = D('Member')->where($map)->getField('id',true);
	    	
	    	$mids = empty($mids) ? array() : $mids;
	    	$xxids = array_diff($grapher_ids, $mids);
	    	if (!empty($xxids)) {
	    		$this->error(implode(',', $xxids)." 这几个作者ID数据里没有啊。");
	    	}
    		
	       	$update = isOnlineByMovieId($movie['id']);
	       	if ($update) { //是否上线
	       		$add_grapher = $rst['diff'][0];
	       		$del_grapher = $rst['diff'][1];
	    		if (!empty($del_grapher)) { // 下线不在新作者里的老作者
	    			$movie['grapher'] = implode(',', $del_grapher);
	    			$this->_offMovieUser($movie);
	    		}
	    		if (!empty($add_grapher)) { // 上线不在老作者里的新作者
		    		$movie['grapher'] = implode(',', $add_grapher);
	    			$this->_updateMovieUser($movie);
	    		}
	       	}
	       	$movie['grapher'] = $grapher;
	       	$model->create($movie);
	       	if (false === $model->save($movie)){
	       		$this->error('更新Movie信息出错');
	       	}
    	}
    	// 2014年12月26日03:44:35 在编辑作者的时候，添加进打赏表 因为要按照打赏记录排序
    	$data = array('data_type'=>1,'data_id'=>$id);
    	$model = D('RewardMap');
    	$one = $model->where($data)->find();
    	if (empty($one)) { // 不存在则添加
	    	if (false === $model->create($data)){
	    		$this->error($model->getError());
	    	} else {
	    		if (false === $model->add()) {
	    			$this->error('作者更新成功，但是插入电影打赏表失败');
	    		}
    		}
    	}
    	
//    	$this->display('doOnline');
    	$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    	$this->success('更新完毕');
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
    	
    	$map = array('name'=> array('in',$tags),'level'=>1);
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
     * 修改Tag_Map 老的，已废弃;
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     * @param unknown_type $zones
     * @param unknown_type $showtimes
     */
    /*
    private function _updateTagsMap($movie, $tags, $zones, $showtimes){
    	$tags[] = 0; $zones[] = 0; $showtimes[] = 0;  // 先给三个加一个初始值，0 代表全部
    	foreach ($tags as $tag) {
    		foreach ($zones as $zone) {
    			foreach ($showtimes as $showtime) {
    				$onlineMapTags = D('OnlineMapTags');
    				$map = array( 'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime );
    				$tmp = $onlineMapTags->where($map)->field('id,tag_id,zone_id,showtime_id,jian,match_movie')->select();
    				$update = true; // 更新记录的开关 
    				if (empty($tmp)) { 
    					$update = false; //关闭更新
    					$data = array (
    						'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime, 'jian' => 0,
    						'movie_count' => 1, 'match_movie' => $movie['id'], 'last_movie_name' => $movie['name'],
    						'last_movie_pic' => $movie['bpic'], 'last_movie_id' => $movie['id']
    					);
    					$onlineMapTags->create($data);
    					if(!$onlineMapTags->add()) { // 先插入jian=0 （代表全部）
    						$this->error('插入movie_tags_map出错jian0');
    					} 
    					if ($movie['jian'] == 1) { // 插入两条记录 jian=0, jian=1
    						$data['jian'] = 1;
    						$onlineMapTags->create($data);
    						if(!$onlineMapTags->add()) {
    							$this->error('插入movie_tags_map出错jian1');
    						}
    					}
    				} elseif (count($tmp) == 1) { // 数据库里只有一条 (姑且认为这一条是$tmp[jian]=0 的)
    					// 当movie jian 1 时，正常情况下，数据库中只有一条记录的时候，jian 肯定为0，所以无论如何，都需要插入一条数据
    					if ($movie['jian'] == 1) { 
    						$data = array ('tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime, 'movie_count' => 1, 'match_movie' => $movie ['id'], 'last_movie_name' => $movie ['name'], 'last_movie_pic' => $movie ['bpic'], 'last_movie_id' => $movie ['id'] );
    						 
    						if ($tmp[0]['jian'] == 0) { // movie 的jian 为1 ，而数据库中的记录是0，则插入一条jian1 的数据
    							$data['jian'] = 1; 
    						}else{ // movie 的jian 为1 ，而数据库中的记录是1(应该不存在，在这里修复罢了)，则插入一条jian0 的数据
    							$data['jian'] = 0;
    						}
    						$onlineMapTags->create($data);
    						if (false === $onlineMapTags->add()){
    							$this->error('插入movie_tags_map出错，DB1，movie_jian1');
    						}
    					} else { // jian为0了
    						if ($tmp[0]['jian'] == 1) { // 这种情况一般也不存在，这里修复罢了
    							// 插入一条jian 0 的记录就好
    							$data = array ('tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime, 'movie_count' => 1, 'match_movie' => $movie ['id'], 'last_movie_name' => $movie ['name'], 'last_movie_pic' => $movie ['bpic'], 'last_movie_id' => $movie ['id'] );
    							$data['jian'] = 0;
    							$onlineMapTags->create($data);
    							if (false === $onlineMapTags->add()) {
    								$this->error('插入movie_tags_map出错,DB1,movie_jian0');
    							}
    							$update = false; //关闭更新
    						}
    					}
    				} elseif (count($tmp) > 2){ 
    					$update = false; //关闭更新
    					 // 这个时候大于两条记录，那就应该报错了
    					$str = array();
    					foreach ($tmp as $t) {
    						$str [] = $t['id'];
    					}
    					$this->error('Tags_map 表数据混乱'. implode(',', $str));
    				} // end if count($tmp)
    				
    				if ($update) {
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
    				} // end if update
    				
    			}// end foreach shwotime
    			
    		} // end foreach zone
    		
    	}// end foreach tag
    }
    */
    
    
    /**
     * 下线类型，区域，时间，tags_map
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     * @param unknown_type $zones
     * @param unknown_type $showtimes
     */
    private function _offTagsMap($movie, $tags, $zones, $showtimes){
    	$tags[] = 0; $zones[] = 0; $showtimes[] = 0;  // 先给三个加一个初始值，0 代表全部
    	foreach ($tags as $tag) {
    		foreach ($zones as $zone) {
    			foreach ($showtimes as $showtime) {
    				$onlineMapTags = D('OnlineMapTags');
    				$map = array( 'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime );
    				$tmp = $onlineMapTags->where($map)->field('id,tag_id,zone_id,showtime_id,jian,match_movie')->select();
    				if (empty($tmp)) { // 如果不存在，继续就好了
    					continue;
    				}elseif (count($tmp)>2) {
    					$update = false; //关闭更新
    					 // 这个时候大于两条记录，那就应该报错了
    					$str = array();
    					foreach ($tmp as $t) {
    						$str [] = $t['id'];
    					}
    					$this->error('Tags_map 表数据混乱'. implode(',', $str));
    				}else { // 正常一条或者两条,不管几条，直接更新即可
    					foreach ($tmp as $t) { 
    						$data = array ('id' => $t['id'] );
							$match_movie = empty($t['match_movie']) ? array() : explode ( ',', $t['match_movie'] );

							$tmp_value = array();
							foreach ( $match_movie as $key => $id ) { // 修复数据库中重复的数据
								if ($id == $movie ['id'] || isset($tmp_value[$id])) { 
									unset ( $match_movie [$key] );
								} else {
									$tmp_value[$id] = true;
								}
							}
							if (empty($match_movie)) { // 如果木有电影了
								$last_movie_name = '无更新';
								$last_movie_pic = '';
								$last_movie_id = '0';
							} else {// 还有电影在里面
								$tmp_id = reset($match_movie); // 得到第一个元素
								$map = array('id'=>$tmp_id);
								
								// 到movie表中查询最后一条movie的信息
								$tmp_movie = D('Movie')->where($map)->field('id,name,bpic')->find();
								if (!empty($tmp_movie)) { 
									$last_movie_id = $tmp_movie['id'];
									$last_movie_name = $tmp_movie['name'];
									$last_movie_pic = $tmp_movie['bpic'];
								}
							}
							$data ['last_movie_name'] = $last_movie_name;
							$data ['last_movie_pic'] = $last_movie_pic;
							$data ['last_movie_id'] = $last_movie_id;
							$data ['movie_count'] = count($match_movie);
							$data ['match_movie'] = implode ( ',', $match_movie );
							$onlineMapTags->create($data);
    						if (false === $onlineMapTags->save()){ // 更新
								$this->error('更新movie_tags_map出错 id:'.$t['id'] );
							}
    					}
    				}
    			} // ---end foreach showtime
    		} // ----end foreach zone
    	} //  ----end foreach tags
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
     * 下线标签电影关系
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     */
    private function _offMovieTag($movie, $tags){
    	$map = array('id' => array('in', $tags));
    	if (false === D('MovieTag')->where($map)->setDec('tag_times')) {
    		$this->error('数据库错误');
    	}
    	
    	foreach ($tags as $tag) {
    		$movieUTag = D('MovieUTag');
    		$map = array('tag_id'=> $tag, 'movie_id'=>$movie['id']);
    		$id = $movieUTag->where($map)->getField('id');
    		if (isset ($id)) {
    			$data = array('id'=>$id,'tag_times' => array('exp', 'tag_times-1'));
    			$movieUTag->create($data);
    			if (false === $movieUTag->save()){
    				$this->error('更新movie_v_tag出错');
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
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$onlineMapPC = D('OnlineMapPC');
    			$list = $onlineMapPC->where($map)->field('id,online_movie,await_movie,online_movie_time,await_movie_time')->select();
    			if (empty($list)) { // 不存在则插入
    				$data = array(
    					'pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform'],
    					'online_movie' => $movie['id'], 'await_movie' => '',
    					'online_movie_time' => date('Y-m-d H:i:s', NOW_TIME), 'await_movie_time'=> '',
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
    				
    				$position = $ppc['position']>0 ? ($ppc['position'] - 1) : 0;
    				$tmp_value= array();
    				foreach ($online_movie as $key=>$val) { // 修复数据
    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
    						unset($online_movie[$key], $online_movie_time[$key]);
    					} else {
    						$tmp_value[$val] = true;
    					}
    				}
    				$i = 0;
    				$tmp = '';
    				foreach ($online_movie as $key=>$val) {
    					if ($i==$position) {
    						$tmp = $val;
    						$online_movie[$key] = $movie['id'];
    						$i++;continue;
    					}
    					if ($tmp != '') { // 找到位置了 id 顺利插入
    						$online_movie[$key] = $tmp;
    						$tmp = $val;
    					}
    					$i++;
    				}
    				if ($tmp!='') {
    					$online_movie[] = $tmp;
    				}
    				
    				$i = 0;
    				$tmp = '';
    				foreach ($online_movie_time as $key=>$val) {
    					if ($i==$position) {
    						$tmp = $val;
    						if ($i == 0) { // 如果是在第一位，则用当前时间
    							$online_movie_time[$key] = date('Y-m-d H:i:s', NOW_TIME);
    						} else {
    							$online_movie_time[$key] = $val;
    							$tmp = date('Y-m-d H:i:s',(strtotime($val)-1)); // 比这个小1秒
    						}
    						$i++;continue;
    					}
    					if ($tmp != '') { // 找到位置了 time 顺利插入
    						$online_movie_time[$key] = $tmp;
    						$tmp = $val;
    					}
    					$i++;
    				}
    				if ($tmp!='') {
    					$online_movie_time[] = $tmp;
    				}
    				
//    				$m1 = array($movie['id']);
//    				$t1 = array(date('Y-m-d H:i:s', NOW_TIME));
//    				array_splice($m1,$position,0,$online_movie); // 在第几位插入数据
//    				array_splice($t1,$position,0,$online_movie_time);
					if ($ppc['pub_channel'] != 'all'){ // 针对all渠道，不做处理
						$tmp_value = array();
	    				foreach ($await_movie as $key=>$val) { // 删除等待区域的数据
	    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
	    						unset($await_movie[$key], $await_movie_time[$key]); 
	    					} else {
	    						$tmp_value[$val] = true;
	    					}
	    				}
					}
    				
    				if (empty($online_movie)) { // 如果为空的话。 则添加进去
    					$online_movie = array($movie['id']);
    					$online_movie_time = array(date('Y-m-d H:i:s', NOW_TIME));
    				} else if ($i<=$position) { // 如果$position 大于最后一个位置 则添加到最后
    					$online_movie[] = $movie['id'];
    					$time = end($online_movie_time);
    					$online_movie_time[] = date('Y-m-d H:i:s',(strtotime($time)-1)); // 比这个小1秒
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
     * 针对渠道下线电影
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $ppc_list
     */
    private function _offOnlineMap($movie, $ppc_list){
    	if (!empty($ppc_list) && is_array($ppc_list)) {
    		foreach ($ppc_list as $ppc) {
    			if (!isset($ppc['pub_channel']) || !isset($ppc['pub_platform'])) continue;
    			$map = array('pub_channel'=>$ppc['pub_channel'], 'pub_platform' => $ppc['pub_platform']);
    			$onlineMapPC = D('OnlineMapPC');
    			$list = $onlineMapPC->where($map)->field('id,online_movie,await_movie,online_movie_time,await_movie_time')->select();
    			if (!empty($list)) { // 存在则删除，否则不管
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
    				
    				if (!isset($ppc['online_flag']) || $ppc['online_flag']){
	    				$tmp_value= array();
	    				foreach ($online_movie as $key=>$val) { // 修复数据
	    					if ($val == $movie['id'] || isset($tmp_value[$val])) {
	    						unset($online_movie[$key], $online_movie_time[$key]);
	    					} else {
	    						$tmp_value[$val] = true;
	    					}
	    				}
    				}
    				
    				if (!isset($ppc['await_flag']) || $ppc['await_flag']) {
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
			
			// ------ movie_v_ding_day 开始
			$memberUDingDay = D('MemberUDingDay');
			$memberUMovieDing = D('MemberUMovieDing');
			$map = array('movie_id'=>$movie['id']);
			$tmp_list = $memberUMovieDing->where($map)->getField('add_time',true);
			if (!empty($tmp_list)) {
				$num = array();
				foreach ($tmp_list as $add_time) {
					$tmp_time = date('Y-m-d 00:00:00', strtotime($add_time));
					if (!isset($num [$tmp_time])) {$num [$tmp_time] = 0;}
					$num[$tmp_time] ++;
				}
				foreach ($num as $day=>$val) {
					$map = array('user_id'=>$member_id, 'day_time'=>$day);
					$tmp_one = $memberUDingDay -> where($map) ->find();
					if (!empty($tmp_one)) {
						if (false === $memberUDingDay -> where($map) -> setInc('ding',intval($val))) {
							$this->error('修改 user_v_ding_day 出错');
						}
					} else {
						$data = array('user_id' => $member_id, 'ding'=>$val, 'day_time'=>$day);
						if (false === $memberUDingDay->add($data)) {
							$this->error('添加 user_v_ding_day 出错');
						}
					}
				}
			}
			// ------ movie_v_ding_day 结束
			
			// ------ movie_v_played_day 开始
			$memberUPlayedDay = D('MemberUPlayedDay');
			$memberUMoviePlayed = D('MemberUMoviePlayed');
			$map = array('movie_id'=>$movie['id']);
			$tmp_list = $memberUMoviePlayed->where($map)->getField('add_time',true);
			if (!empty($tmp_list)) {
				$num = array();
				foreach ($tmp_list as $add_time) {
					$tmp_time = date('Y-m-d 00:00:00', strtotime($add_time));
					if (!isset($num [$tmp_time])) {$num [$tmp_time] = 0;}
					$num[$tmp_time] ++;
				}
				foreach ($num as $day=>$val) {
					$map = array('user_id'=>$member_id, 'day_time'=>$day);
					$tmp_one = $memberUPlayedDay -> where($map) ->find();
					if (!empty($tmp_one)) {
						if (false === $memberUPlayedDay -> where($map) -> setInc('played',intval($val))) {
							$this->error('修改 user_v_played_day 出错');
						}
					} else {
						$data = array('user_id' => $member_id, 'played'=>$val, 'day_time'=>$day);
						if (false === $memberUPlayedDay->add($data)) {
							$this->error('添加 user_v_played_day 出错');
						}
					}
				}
			}
			// ------ movie_v_played_day 结束
			
			
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
						'reply_from' => 'notice', 'reply_from_data' => '1',
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
     * 下线更新用户信息
     * Enter description here ...
     * @param unknown_type $movie
     */
    private function _offMovieUser($movie){
    	$grapher = empty($movie['grapher']) ? array() : explode(',', $movie['grapher']);
    	foreach ($grapher as $member_id) { 
    		// step1 更新user_work_map 
    		$memberWorkMap = D('MemberWorkMap');
			$map = array('user_id' => $member_id);
			$tmp = $memberWorkMap->where($map)->field('id,online_work')->select();
			$member_work_count = 0; // 用户影片数量
			$member_work_movie_ids = array(); // 用户影片ID，用于跟新操作
			$is_in_worklist = false;
			if (empty($tmp)) { // 冇记录 不管
				
			} elseif (count($tmp)>1) { // 数据库数据异常
				$str = array();
				foreach ($tmp as $l) {
					$str [] = $l['id'];
				}
				$this->error('user_work_map 表数据损坏'.implode(',', $str));
			} else {
				$online_work = empty($tmp[0]['online_work']) ? array() : explode(',', $tmp[0]['online_work']);
				$tmp_value = array();
				foreach ($online_work as $key=>$val) { // 修复数据
					if ($val == $movie['id']) {
						$is_in_worklist = true; // 在发表列表里
    					unset($online_work[$key]);
    				} elseif (isset($tmp_value[$val])) {
    					unset($online_work[$key]);
    				} else {
    					$tmp_value[$val] = true;
    				}
				}
				
				$data = array ('id' => $tmp [0] ['id'], 'online_work' => implode ( ',', $online_work ), 'online_work_count' => count ( $online_work ) );
				
				$memberWorkMap->create($data);
				if(false === $memberWorkMap->save()){
					$this->error('更新用户作品表出错');
				}
				$member_work_count = count($online_work);
				$member_work_movie_ids = $online_work;
			}
			
	    	// 	step2 更新 movie_v_user 
			$movieUMember = D('MovieUMember');
			$map = array('user_id' => $member_id, 'movie_id' => $movie['id']);
			if (false === $movieUMember->where($map)->delete()) {
				$this->error('删除movie_v_user出错');
			}
			
			// step2.5------ 更新movie_v_ding_day 开始
			$memberUDingDay = D('MemberUDingDay');
			$memberUMovieDing = D('MemberUMovieDing');
			$map = array('movie_id'=>$movie['id']);
			$tmp_list = $memberUMovieDing->where($map)->getField('add_time',true);
			if (!empty($tmp_list)) {
				$num = array();
				foreach ($tmp_list as $add_time) {
					$tmp_time = date('Y-m-d 00:00:00', strtotime($add_time));
					if (!isset($num [$tmp_time])) {$num [$tmp_time] = 0;}
					$num[$tmp_time] ++;
				}
				foreach ($num as $day=>$val) {
					$map = array('user_id'=>$member_id, 'day_time'=>$day);
					$tmp_one = $memberUDingDay -> where($map) ->find();
					if (!empty($tmp_one)) {
						if (false === $memberUDingDay -> where($map) -> setDec('ding',intval($val))) {
							$this->error('修改 user_v_ding_day 出错');
						}
					}
				}
			}
			// ------ movie_v_ding_day 结束
			
			// step2.6------ 更新movie_v_played_day 开始
			$memberUPlayedDay = D('MemberUPlayedDay');
			$memberUMoviePlayed = D('MemberUMoviePlayed');
			$map = array('movie_id'=>$movie['id']);
			$tmp_list = $memberUMoviePlayed->where($map)->getField('add_time',true);
			if (!empty($tmp_list)) {
				$num = array();
				foreach ($tmp_list as $add_time) {
					$tmp_time = date('Y-m-d 00:00:00', strtotime($add_time));
					if (!isset($num [$tmp_time])) {$num [$tmp_time] = 0;}
					$num[$tmp_time] ++;
				}
				foreach ($num as $day=>$val) {
					$map = array('user_id'=>$member_id, 'day_time'=>$day);
					$tmp_one = $memberUPlayedDay -> where($map) ->find();
					if (!empty($tmp_one)) {
						if (false === $memberUPlayedDay -> where($map) -> setDec('played',intval($val))) {
							$this->error('修改 user_v_played_day 出错');
						}
					}
				}
			}
			// ------ movie_v_played_day 结束
			
    		// step3 更新用户的作品数目以及通知操作
			$member = D('Member');
			$map = array('id'=>$member_id);
			$id = $member->where($map)->getField('id');
			if (!empty($id)) {
				$new_flag = false; // 是否插入消息列表的标志
				if ($is_in_worklist) { 
					// 为用户添加动态新闻 //用户1号为图解电影官方
					$new_msg =  '(┳Д┳)您的作品《'.$movie['name'].'》 被下线了...';
					$memberNew = D('MemberNew');
					$data = array(
						'user_id'=>1, 'to_user_id'=>$member_id, 
						'comment_content'=>$new_msg, 'reply_comment_id' => 0, 'readed' => 0,
						'reply_from' => 'notice', 'reply_from_data' => '1',
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
				if ($new_flag) { // 未读信息+1
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
    	$all_online_movie_wmean = array(); // 记录所有影片和该影片相似度的电影排名
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
			$all_online_movie_wmean[$movie_id] = $wmean;
    	}
    	
    	// 降序进行排列
    	arsort($all_online_movie_wmean);
    	// step6
    	$movieRelateWmean = D('MovieRelateWmean');
    	$i=1;
    	foreach ($all_online_movie_wmean as $movie_id=>$wmean) {
    		if ($i>20) {break;} // 如果超过20条记录，则只记录20条
    		$i++;
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
    	} // step6 over
    }
    
    /**
     * 针对不同渠道，对应关注过这部影片作者的所有用户发送动态
     * Enter description here ...
     * @param MovieModel $movie
     * @param array $online_options
     */
    private function _updateMemberNews($movie,$online_options) {
    	$graphers = explode(',', $movie['grapher']);
    	$grapher_names = array();
    	$Member = D('Member');
    	$MemberFollowMember = D('MemberFollowMember');
    	$follow_member_ids = array(); // 关注作者的用户ID数组
    	foreach($graphers as $grapher_id) {
    		$map = array('follow_user_id'=>$grapher_id, 'open'=>1);
    		$tmp_member_ids = $MemberFollowMember->where($map)->getField('user_id',TRUE);
    		if (!empty($tmp_member_ids)) {
    			$follow_member_ids[$grapher_id] = $tmp_member_ids;
    		}
    	}
    	if (!empty($follow_member_ids)) { // 如果关注作者的用户ID不为空
    		$follow_members = array(); // 
    		$tmp_member_ids = array();
    		foreach ($follow_member_ids as $grapher_id => $member_ids) {
    			foreach ($member_ids as $member_id) {
    				if (!isset($tmp_member_ids[$member_id])) {
		    			$map = array('id'=>$member_id);
		    			$tmp_member_ids[$member_id] = $Member->where($map)->field('id,pub_channel,pub_platform')->find();
	    				if (!empty($tmp_member_ids[$member_id])) {
		    				$follow_members[$grapher_id][] = $tmp_member_ids[$member_id];
		    			}
    				}
    			}
    		}
    		unset($tmp_member_ids);
    		// 改变onlie_options 结构
    		$tmp_options = array();
    		foreach ($online_options as $options) {
    			$tmp_options[$options['pub_platform'].','.$options['pub_channel']] = true;
    		}
    		
    		$member_new_list = array(); // 需要添加的用户动态列表
    		$add_time = toDate(NOW_TIME);
    		foreach ($follow_members as $grapher_id => $member_objs) { // 找到在这次上线渠道列表里的用户
    			foreach ($member_objs as $member_obj) {
	    			if (isset($tmp_options[$member_obj['pub_platform'].','.$member_obj['pub_channel']])) { 
	    				$read_str = '你关注的 @{msgboarduserid:'.$grapher_id.'} 有新作品 《'.$movie['name'].'》上线啦，点击查看';
	    				$member_new_list[] = array(
	    					'user_id' => 1,
	    					'to_user_id' => $member_obj['id'],
	    					'reply_comment_id' => 0,
	    					'comment_content' => $read_str,
	    					'reply_from' => 'to-movie',
	    					'reply_from_data' => $movie['id'],
	    					'readed' => 0,
	    					'pre_length' => mb_strlen($read_str,'utf8'),
	    					'secret_send' => 0,
	    					'add_time' => $add_time
	    				);
	    			}
    			}
    		}
    		
    		if  (!empty($member_new_list) && false === D('MemberNew')->addAll($member_new_list)){
    			$this->error('给关注用户发送动态错误');
    		}
    	}
    }
    
    
    function uploadScript(){
    	$movie_id = I('movie_id','');
    	if (empty($movie_id) || !is_numeric($movie_id)) {
    		$this->error('参数错误');
    	}
    	if (empty($_FILES['script'])) {
    		$this->error('先选择脚本文件呐');
    	}
    	$file = $_FILES['script'];
    	if ($file['error'] > 0) {
    		$this->error($this->file_error($file['error']));
    	}
    	if ($file['size']>256000){
    		$this->error('文件过大--不能大于150K');
    	}
    	$content = trim(file_get_contents($file['tmp_name']));
    	$content = trim($content,"\xEF\xBB\xBF"); // 去除文件的BOM头
    
    	$json = json_decode($content, true);
    	if (!is_array($json) || empty($json['story'])) {
    		$this->error('获取文件内容错误，请检查脚本文件后再提交');
    	}
    	$script = $json['story'];
    	
    	$map = array('movie_id' => $movie_id);
    	$movie_comment = D('MComment');
    	$one = $movie_comment->where($map)->find();
    	if (!empty($one)) {
    		$this->error('在你之前，不知道谁，已经添加了至少一条的解说哦。');
    	}
    	$map = array('id'=>$movie_id);
    	$movie = D('Movie');
    	$img_serv = $movie->where($map)->getField('imgserver_use');
    	if ($img_serv === null) {
    		$this->error('没找到电影啊。');
    	}
    	
    	$rst = getScriptImgInfoUrl($movie_id,$img_serv);
    	if (is_string($rst)) {
    		$this->error($rst);
    	}
    	if (empty($rst)) {
    		$this->error('电影图片服务器选择错误'.$img_serv);
    	}
    	if (is_array($rst)) {
    		//$size = $rst['size'];
    		$size = 0;
    		$pages = count($script);
    		/*
    		 * 2014年9月15日17:02:26 去除这个判断，因为脚本有可能使用两张一样的图片。
    		 */
//    		if ($rst['num'] < $pages) {
//    			$this->error('图片服务器里的图片数量比脚本里的要少哦，请检查。');
//    		} else {
//    			$cha_page = $rst['num'] - $pages;
//    			if ($cha_page > 0) { // 减去少了的页数所占的空间
//    				$size = $size - (($rst['size']/$rst['num'])*$cha_page);
//    			}
    			foreach ($script as $i=>$item) {
    				if (!preg_match("/^[a-zA-Z0-9_.]+$/",$item['name'])) {
    					$this->error("第".($i+1)."张 图片地址 “".$item['name']."” 中含有特殊字符，请过滤");
    				}
    				if (!isset($rst['files'][$item['name']])) {
    					$this->error("第".($i+1)."张 图片 ".$item['name'].' 这个文件在图片服务器中不存在');
    				}
    				if ($item['intro'] !== null && !is_string($item['intro'])) { 
    					$this->error("第".($i+1)."张 图片 ".$item['name'].'解说 '.print_r($item,true)." 不符合要求");
    				}
    				if (!isset($rst['infos'][$item['name']])) {
    					$this->error("第".($i+1)."张 图片 ".$item['name'].' 在图片服务器获取信息出错');
    				}
    				$size += $rst['files'][$item['name']];
    			}
//    		}
    		$size = floor($size*100/1048576) / 100;
    		$data = array('id'=>$movie_id,'total_size'=>$size,'total_page'=>$pages);
    		$movie->save($data);
    		
    		$dataList = array();
    		$data_index = 0;
    		$img_url = otherURL_2_Server_2_URL('o.jpg',$movie_id,$img_serv);
    		$img_url = substr($img_url, 0, strlen($img_url) - 5);
    		foreach ($script as $i=>$item) {
    			$item['intro'] = isset($item['intro']) ? (str_replace(array("\r\n", "\r", "\n"), "", $item['intro'])) : '';
    			$dataList[] = array(
    				'movie_id'=>$movie_id,
    				'vol_id' => 1,
    				'image' => $img_url.$item['name'],
    				'intro' => $item['intro'],
    				'script' => '',
    				'pindex' => $i,
    				'poptxt_count' => 0,
    				'img_info' => json_encode($rst['infos'][$item['name']])
    			);
    			if (($data_index ++) >= 1000) {
    				$data_index = 0;
    				$movie_comment->addAll($dataList);
    				$dataList = array();
    			}
    		}
    		if ($data_index>0) {
    			$movie_comment->addAll($dataList);
    		}
    		$this->success('导入成功');
    	} else {
    		$this->error('未知错误'.$rst);
    	}
    	
    }
    
	protected function file_error($errorNo) {
         switch($errorNo) {
            case 1:
                return '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
            case 2:
                return '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
            case 3:
                return '文件只有部分被上传';
            case 4:
                return '没有文件被上传';
            case 6:
                return '找不到临时文件夹';
            case 7:
                return '文件写入失败';
            default:
                return '未知上传错误！';
        }
        return '';
    }
    
    
    /**
     * 图片解说页面
     * Enter description here ...
     */
    function comment(){
    	$id = I('movie_id');
    	if (empty($id)) {
    		$this->error("参数错误");
    	}
    	$map = array('movie_id' => $id);
    	$list = $this->_list(D('MComment'), $map, 'pindex', true);
    	$this->display();
    }
    
    /**
     * 添加解说
     * Enter description here ...
     */
    function addComment(){
    	$movie_id = I('movie_id');
    	$map = array('id'=>$movie_id);
	    $imgserver = D('Movie')->where($map)->getField('imgserver_use');
	    if ($imgserver == 3) {
	    	$this->error('该影片服务器不支持编辑。');
	    	$this->assign('opt',1);
	    }
    	$this->display();
    }
    
    /**
     * 清空电影解说脚本
     * Enter description here ...
     */
    function doCleanComment(){
    	$movie_id = I('movie_id');
    	if (empty($movie_id) || !is_numeric($movie_id)) {
    		$this->error('参数错误');
    	}
//    	$data = array('id'=>$movie_id,'total_size'=>0,'total_page'=>0);
//		D('Movie')->save($data);
    	
    	$map = array('id'=>$movie_id);
    	$poptxt_count = D('Movie')->where($map)->getField('poptxt_count');
    	$login_id = $_SESSION[C('USER_AUTH_KEY')];
    	if ($poptxt_count>10 && $login_id != 1) {
    		$this->error('已经有弹幕了，不允许删除');
    	}
    	
    	$map = array('movie_id'=>$movie_id);
    	$rst = D('MComment')->where($map)->delete();
    	if ($rst === false) {
    		$this->error('删除失败');
    	} else {
    		$this->success("删除成功,共删除".$rst.' 条记录');
    	}
    }
    
    /**
     * 实实在在的添加解说
     * Enter description here ...
     */
	function doAddComment(){
    	$movie_id = I('movie_id');
    	if (empty($movie_id)) {$this->error('参数错误');}
//    	$this->_uploadImg2($movie_id);
    	$pindex = I('pindex');
    	if (!is_numeric($pindex)) {
    		$this->error('页数错误');
    	}
    	$image = I('image');
    	if (empty($image)) {$this->error('图片必须');}
    	
    	$update = true;
    	$model = D('MComment');
    	if ($pindex < 0) {
    		$map = array('movie_id'=>$movie_id);
    		$pindex = $model->where($map)->max('pindex');
    		$_POST['pindex'] = $pindex === NULL ? 0 : ($pindex+1);
    		$update = false;
    	}
    	
		if ($update) {
	       	$map = array('movie_id'=>$movie_id, 'pindex'=> array('EGT',$pindex));
	       	$model->where($map)->setInc('pindex');
        }
    	
     	if (false === $model->create()) {
            $this->error($model->getError());
        }
        $rst = $model->add();
        if (false !== $rst) {
        	$map = array('id'=>$movie_id);
        	D('Movie')->where($map)->setInc('total_page');
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
    	$movie_id = I('movie_id');
    	$pindex = I('pindex');
    	
    	$num = I('num');
    	if ($pindex < 0){
    		$map = array('movie_id'=>$movie_id);
    		$pindex = D('MComment')->where($map)->max('pindex');
    		$pindex ++;
    	}
    	if (!empty($num)) {
    		$pindex = $pindex + ($num);
    	}
    	
    	if ($pindex<0) {
    		$vo = array();
    	} else {
	    	$map = array('id'=>$movie_id);
	    	$imgserver = D('Movie')->where($map)->getField('imgserver_use');
	    	
	    	$map = array('movie_id'=>$movie_id, 'pindex'=>$pindex);
	    	$vo = D('MComment')->where($map)->find();
	    	if (!empty($vo)) {
	    		$vo['img'] = otherURL2ServerUrl($vo['image'], $movie_id, $imgserver);
	    	}
    	}
    	$this->ajaxReturn($vo);
    }
    
    /**
     * 更新解说
     * Enter description here ...
     */
    function updateComment(){
        $model = D('MComment');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            $rst['msg'] = '更新成功';
        } else {
        	$rst['msg'] = '更新失败';
        }
        $this->ajaxReturn($rst);
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
    	$model = D('MComment');
    	$map = array('id' => $id);
    	
    	$one = $model->where($map)->field('movie_id, pindex')->find();
    	if (empty($one)) {$this->error('参数错误');}
    	
    	if (false !== $model->where($map)->delete()) {
    		$map = array('id' => $one['movie_id']);
    		if (!$movie = D('Movie')->where($map)->setDec('total_page')) { // 影片页数-1
    			$this->error('更新影片信息失败，还未跟新章节数');
    		} 
    		$map = array('movie_id'=>$one['movie_id'],'pindex' => array('GT',$one['pindex']));
    		$model->where($map)->setDec('pindex');
    		//成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('删除成功!');
    	} else {
    		$this->error('删除失败');
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
    	
    	$vo = D('Movie')->field('id,name,bpic,spic')->getById($id);
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
    	$model = D('Movie');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
        	// 2015年5月26日15:40:14 由于每个图解都有编辑封面操作，故在这里对图解的二级标签进行处理( 上线操作流程太多，太慢)
        	$this->_updateMark($id);
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
            $dir = '../movies/'.$id;
          
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
            if (!empty($_FILES['bpic']) && $_FILES['bpic']['error'] != 4) { 
            	$upload->saveRule = $saveName."b";
            	$fileInfo = $upload->uploadOne($_FILES['bpic']);
            	if ($fileInfo) {
            		$_POST ['bpic'] = 'movies/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'bpic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            }
            
            if (!empty($_FILES['spic']) && $_FILES['spic']['error'] != 4) {
            	$upload->saveRule = $saveName."s";
            	$fileInfo = $upload->uploadOne($_FILES['spic']);
            	if ($fileInfo) {
            		$_POST ['spic'] = 'movies/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'spic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
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
            	$rst = sendFileToImgSevr('movies',$id,$files);
            	if (is_array($rst)) {
            		foreach ($files as $file) {
            			if (isset($rst['succ'][$file['key']])) {
            				//$_POST[$file['key']] = $rst['succ'][$file['key']]['url'];
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
     * 阅读行为统计
     */
    public function readBehavior(){
    	$id = I('id');
    	$file = APP_PATH.'Catch/MovieReadBehavior/'.$id.'.catch';
    	$url_go = true;
    	if (file_exists($file)) {
    		$file_mtime = filemtime($file);
    		if (NOW_TIME - $file_mtime < 86400) {
    			$url_go = false;
    			$content = file_get_contents($file);
    		}
    		$this->assign('time',toDate($file_mtime));
    	}
    	if ($url_go){
    		$url = C('GET_API.movie_read_behavior');
    		$data = array('id'=>$id);
    		import("@.ORG.Net.HttpClient");
    		$arr_url = parse_url($url);
    		$port = isset($arr_url['port']) ? $arr_url['port'] : 80;
    		$request = new HttpClient($arr_url['host'], $port);
    		$request->setTimeout(60);
    		$result = $request->get($arr_url['path'],$data);
    		if (!empty($result)) {
    			$content = $request->getContent();
    			$xx = json_decode($content, true);
    			if (is_array($xx)) {
    				file_put_contents($file, $content);
    			} else {
    				$this->assign('err',$content);
    			}
    		}
    		$this->assign('time',toDate(NOW_TIME));
    	}
    	$this->assign('rst',$content);
    	$this->display();
    }
    
    /**
     * 图解编辑打分
     */
    public function weScore(){
    	$rst = array('rst' => 0);
    	$model = D('Movie');
    	if (false === $model->create() || false === $model->save()) {
    		$rst['msg'] = ($model->getError());
    		$this->ajaxReturn($rst);
    	}
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    
    /**
     * 更新影片二级标签表
     * @param unknown $id
     * @return string|boolean
     */
    private function _updateMark($id){
    	if (is_numeric($id)) {
    		$movie = D('movie')->where(array('id'=>$id))->field('id,mark')->find();
    	} else {
    		$movie = $id;
    	}
    	$MovieUTag = D('MovieUTag');
    	if (!empty($movie['mark'])) {
    		$type = D('MovieTag');
    		$map = array('level' => 2, 'open' => 1);
    		$tags = $type->where($map)->getField('name,id');
    		$marks = explode('|', $movie['mark']);
    		$dataList = array();
    		$time = toDate(NOW_TIME);
    		$list = $MovieUTag->where(array('movie_id'=>$movie['id'],'level'=>2))->getField('tag_id',true);
    		$mark_ids = array();
    		foreach ($marks as $mark) {
    			$mark_ids[] = $tags[$mark];
    			if (!in_array($tags[$mark], $list)){
    				$dataList[] = array('movie_id'=>$movie['id'], 'tag_id'=>$tags[$mark], 'tag_times'=>1, 'add_time'=>$time, 'online_time'=>$time, 'level'=>2, 'open'=>1);
    			}
    			
    		}
    		$diff_arr = array_diff($list,$mark_ids);
    		if (!empty($diff_arr)) {
	    		$map = array('movie_id'=>$movie['id'],'level'=>2, 'tag_id'=>array('in',$diff_arr));
	    		$MovieUTag->where($map)->delete();
    		}
    		
    		if (!empty($dataList)) {
    			if ($MovieUTag->addAll($dataList) === false) {
    				return $MovieUTag->getError();
    			}
    		}
    	} else {
    		$map = array('movie_id'=>$movie['id'],'level'=>2);
    		$MovieUTag->where($map)->delete();
    	}
    	return true;
    }
}
?>