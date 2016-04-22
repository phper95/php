<?php
// 影片评级类
class MovieRankAction extends CommonAction {
    public function index(){
		$this->display();
    }
    
    public function history(){
    	$model = D('MovieRankRecord');
    	$map = array();
    	$s_day = I('s_day');
    	$e_day = I('e_day');
    	$s_day = empty($s_day) ? 0 : ($s_day.' 00:00:00');
    	$e_day = empty($e_day) ? toDate(NOW_TIME) : ($e_day. ' 23:59:59'); 
    	$map['add_time'] = array('between',array($s_day,$e_day));
    	$this->_list($model, $map);
    	$this->assign('rank_type',array('0'=>'全部图解','1'=>'电影','2'=>'剧集'));
    	$this->display();
    }
    
    public function historyInfo(){
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    	$vo = D('MovieRankRecord')->where(array('id'=>$id))->find();
    	if (empty($vo)) {
    		$this->error('参数错误');
    	}
    	
		$vo['pay'] = (100 * $vo['total_rank_a']  + 50 * $vo['total_rank_b']  + 25 * $vo['total_rank_c']) / $vo['tm'];
    	$this->assign('vo', $vo);
    	
    	$movie_a  = json_decode($vo['rank_a'], true);
    	$movie_b  = json_decode($vo['rank_b'], true);
    	$movie_c  = json_decode($vo['rank_c'], true);
    	
    	$a_id = $this->_getMovieInfo($movie_a);
    	$b_id = $this->_getMovieInfo($movie_b);
    	$c_id = $this->_getMovieInfo($movie_c);
    	
    	$movie_all = array_merge($a_id, $b_id, $c_id);
    	
    	$map = array('id'=>array('in',$movie_all));
    	$movies = D('Movie')->where($map)->getField('id,name,grapher');
    	
    	if (empty ( $movies )) {
			$this->error ( '数据库错误' );
		} else {
			$grapher_ids = array ();
			foreach ( $movies as $val ) {
				$grapher_ids [$val ['grapher']] = true;
			}
			$graphers = D ( 'Member' )->where ( array ('id' => array ('in', array_keys ( $grapher_ids ) ) ) )->getField ( 'id,name', true );
		}
		
		$this->assign('movies', $movies);
		$this->assign('graphers', $graphers);
//    {$vo['s1'] * $vo['mr']}

		$rule['shen'] = array(
			'p' => $vo['p1'] * $vo['mr'],
			'l' => $vo['q1'] * $vo['mlr'],
			'po' => $vo['s1'] * $vo['mr'],
			'lo' => $vo['t1'] * $vo['mlr']
		);
		$rule['jin'] = array(
			'p' => $vo['p2'] * $vo['mr'],
			'l' => $vo['q2'] * $vo['mlr'],
			'po' => $vo['s2'] * $vo['mr'],
			'lo' => $vo['t2'] * $vo['mlr']
		);
		$rule['diao'] = array(
			'p' => $vo['p3'] * $vo['mr'],
			'l' => $vo['q3'] * $vo['mlr'],
			'po' => $vo['s3'] * $vo['mr'],
			'lo' => $vo['t3'] * $vo['mlr']
		);
		$movie_a = $this->_poge($movie_a, $rule['shen']);
		$movie_b = $this->_poge($movie_b, $rule['jin']);
		$movie_c = $this->_poge($movie_c, $rule['diao']);
		
		$this->assign('movie_a', $movie_a);
		$this->assign('movie_b', $movie_b);
		$this->assign('movie_c', $movie_c);
		
    	$this->display();
    }
    
    /**
     * 预评级结果查看
     * 
     */
    public function preRankView(){
    	$model = D('MovieRankPre');
    	$map = array();
    	$s_day = I('s_day');
    	$e_day = I('e_day');
    	$s_day = empty($s_day) ? 0 : ($s_day.' 00:00:00');
    	$e_day = empty($e_day) ? toDate(NOW_TIME) : ($e_day. ' 23:59:59'); 
    	$map['add_time'] = array('between',array($s_day,$e_day));
    	$this->_list($model, $map);
    	$this->assign('rank_type',array('0'=>'全部图解','1'=>'电影','2'=>'剧集'));
    	$this->display();
    }
    
    /**
     * 预评级详细评级结果
     * 
     */
    public function preRankInfo(){
    	$model = D('MovieRankCurve');
    	$id = I('id');
    	if (!is_numeric($id) && $id < 1) {
    		$this->error('参数错误');
    	}
    	$map = array('id'=>$id);
    	$vo = D('MovieRankPre')->where($map)->find();
    	
    	$map = array('record_id'=>$id);
    	$list  = $model->where($map)->select();
    	
    	if (empty($list)) {$this->error('擦擦，没有记录。');}
    	
    	$shen = $jin = $diao = $po_shen = $po_jin = $po_diao = array();
    	$movie_ids = array();
    	foreach ($list as $val) {
    		$val['movie_json'] = $this->_zhuanArray(json_decode($val['movie_json'], true));
    		$movie_ids = array_merge($movie_ids, array_keys($val['movie_json']));
    		if ($val['rank_key_1'] == 'p1' && $val['rank_key_2'] == 'q1') { // 神作
    			$shen[$val['id']] = $val;
    		} else if ($val['rank_key_1'] == 'p2' && $val['rank_key_2'] == 'q2') { // 震精
    			$jin[$val['id']] = $val;
    		} else if ($val['rank_key_1'] == 'p3' && $val['rank_key_2'] == 'q3') { // 略屌
    			$diao[$val['id']] = $val;
    		} else if ($val['rank_key_1'] == 's1' && $val['rank_key_2'] == 't1') { // 破格-神作
    			$po_shen[$val['id']] = $val;
    		} else if ($val['rank_key_1'] == 's2' && $val['rank_key_2'] == 't2') { // 破格-震精
    			$po_jin[$val['id']] = $val;
    		} else if ($val['rank_key_1'] == 's3' && $val['rank_key_2'] == 't3') { // 破格-略屌
    			$po_diao[$val['id']] = $val;
    		}
    	}
    	
    	//order('score desc')->last();
    	$last = D('MovieRankRecord')->field('tm,total_rank_a,total_rank_b,total_rank_c')->order('add_time desc')->find();
    	$this->assign('last',$last);
    	
    	$vo['pay'] = (100 * $last['total_rank_a']  + 50 * $last['total_rank_b']  + 25 * $last['total_rank_c']) / $vo['tm'];
    	$this->assign('vo', $vo);
    	
    	$map = array('played'=>array('gt',0));
    	$movies = D('Movie')->where($map)->getField('id,name', true);
    	$this->assign('movie', $movies);
    	
    	$map = array('cellcover' => array('gt',0));
    	$rank_movies = D('Movie')->where($map)->getField('id,name,cellcover');
    	foreach ($rank_movies as $m) {
    		$rank_m[$m['cellcover']][] = $m;
    	}
    	$this->assign('rank_m', $rank_m);
    	
    	$this->assign('shen',$shen);
    	$this->assign('jin', $jin);
    	$this->assign('diao', $diao);
    	$this->assign('po_shen', $po_shen);
    	$this->assign('po_jin', $po_jin);
    	$this->assign('po_diao', $po_diao);
    	$this->display();
    }
    
    /**
     * 发布评级
     * Enter description here ...
     */
    public function doRank(){
//    	$this->display('index');exit();
    	$pram = array('shen','jin','diao','po_shen','po_jin','po_diao');
    	$ids = array();
    	foreach ($pram as $val){
    		$tmp = explode(',', I($val));
    		if (isset($tmp[1]) && $tmp[1]>0) {
    			$ids[] = $tmp[1];
    		}
    	}
    	if (empty($ids)) {
    		$this->error('至少选一项哦');
    	}
    	$model = D('MovieRankCurve');
    	$map = array('id'=>array('in', $ids));
    	$rst = $model->where($map)->select();
    	if (empty($rst)) {
    		$this->error('擦，非法数据');
    	}
    	$data = array(
    		'p1'=>0,'p2'=>0,'p3'=>0,
    		'q1'=>0,'q2'=>0,'q3'=>0,
    		's1'=>0,'s2'=>0,'s3'=>0,
    		't1'=>0,'t2'=>0,'t3'=>0
    	);
    	
    	$movies = array('p1q1'=>array(),'p2q2'=>array(),'p3q3'=>array(),'s1t1'=>array(),'s2t2'=>array(),'s3t3'=>array());
    	foreach ($rst as $val) {
    		$data[$val['rank_key_1']] = $val['rank_value_1'];
    		$data[$val['rank_key_2']] = $val['rank_value_2'];
    		$record_id = $val['record_id'];
    		$movies[$val['rank_key_1'].$val['rank_key_2']] = $this->_zhuanArray(json_decode($val['movie_json'],true));
    	}
    	
    	$rank_pre = D('MovieRankPre');
    	$map = array('id'=>$record_id);
    	$vo = $rank_pre->where($map)->field('mr,ml,mlr,tm,tl,tr,add_time,y,m,d,h,rank_type,rank_num,pub_status')->find();
    	if (empty($vo)) {$this->error('擦，数据木油了。');}
    	if ($vo['pub_status'] == 1 || $vo['pub_status'] == 2) {
    		$this->error('咯咯咯咯咯咯，已发布，或者已失效的，就不要调皮点发布了嘛！');
    	}
    	unset($vo['pub_status']);
    	
    	$rank_movie = array('shen'=>array(),'jin'=>array(),'diao'=>array());
    	$rank_movie['shen'] = $movies['p1q1'];
    	
    	foreach ($movies['s1t1'] as $id => $val) { // 去除正常评选的电影
    		if (!isset($rank_movie['shen'][$id])) {
    			$rank_movie['shen'][$id] = $val;
    		}
    	}
    	
    	foreach ($movies['p2q2'] as $id => $val) { // 去除神作电影
    		if (!isset($rank_movie['shen'][$id])) {
    			$rank_movie['jin'][$id] = $val;
    		}
    	}
    	
    	foreach ($movies['s2t2'] as $id => $val) { // 去除神作，以及正常的震精电影
    		if (!isset($rank_movie['shen'][$id]) && !isset($rank_movie['jin'][$id])) {
    			$rank_movie['jin'][$id] = $val;
    		}
    	}
    	
    	foreach ($movies['p3q3'] as $id => $val) { // 去除神作，震精电影
    		if (!isset($rank_movie['shen'][$id]) && !isset($rank_movie['jin'][$id])) {
    			$rank_movie['diao'][$id] = $val;
    		}
    	}
    	
    	foreach ($movies['s3t3'] as $id => $val) { // 去除神作，震精，以及正常的略屌电影
    		if (!isset($rank_movie['shen'][$id]) && !isset($rank_movie['jin'][$id]) && !isset($rank_movie['diao'][$id])) {
    			$rank_movie['diao'][$id] = $val;
    		}
    	}
//    	echo 'shen:'.count($rank_movie['shen'])."<br />";
//    	echo 'jin:'.count($rank_movie['jin'])."<br />";
//    	echo 'diao:'.count($rank_movie['diao'])."<br />";

    	$data['rank_a'] = json_encode(array_merge($rank_movie['shen']));
    	$data['rank_b'] = json_encode(array_merge($rank_movie['jin']));
    	$data['rank_c'] = json_encode(array_merge($rank_movie['diao']));
    	
    	if (!empty($rank_movie['shen'])) {
//    		print_r(array_keys($rank_movie['shen'])); exit();
    		$this->_updateMovie(array_keys($rank_movie['shen']), 3);
    	} 
    	if (!empty($rank_movie['jin'])) {
    		$this->_updateMovie(array_keys($rank_movie['jin']), 2);
    	}
    	if (!empty($rank_movie['diao'])) {
    		$this->_updateMovie(array_keys($rank_movie['diao']), 1);
    	}
    	
    	$map = array('id'=>$record_id);
    	if (FALSE === $rank_pre->where($map)->setField('pub_status',1)) {
    		$this->error('更新movie_rank_record_pre表出错');
    	}
    	
    	$map = array('pub_status'=>array('neq',1));
    	if ($vo['rank_type'] != '0') { // 如果不是全部的，则只同类型的置成不可发布状态，否则，全部置成不可发布状态
    		$map['rank_type'] = array('in',$vo['rank_type'].',0');
    	}
    	if (FALSE === $rank_pre->where($map)->setField('pub_status',2)) {
    		$this->error('更新movie_rank_record_pre表其他记录出错');
    	}
    	
    	$data = array_merge($vo,$data);
    	
    	$data['rank_a_mr'] = $data['mr'] * $data['p1'];
    	$data['rank_b_mr'] = $data['mr'] * $data['p2'];
    	$data['rank_c_mr'] = $data['mr'] * $data['p3'];
    	
    	$data['rank_a_mlr'] = $data['mlr'] * $data['q1'];
    	$data['rank_b_mlr'] = $data['mlr'] * $data['q2'];
    	$data['rank_c_mlr'] = $data['mlr'] * $data['q3'];
    	
    	$data['rank_a_r'] = floor($data['mr'] * $data['s1']); // 神作破格阅读数
    	$data['rank_b_r'] = floor($data['mr'] * $data['s2']); // 震精破格阅读数
    	$data['rank_c_r'] = floor($data['mr'] * $data['s3']); // 略屌破格阅读数
    	
    	$data['rank_a_l'] = floor($data['ml'] * $data['t1']); // 神作破格点赞数
    	$data['rank_b_l'] = floor($data['ml'] * $data['t2']); // 震精破格点赞数
    	$data['rank_c_l'] = floor($data['ml'] * $data['t3']); // 略屌破格点赞数
    	
    	$movie = D('Movie');
    	$shen_movie_ids = $movie->where(array('cellcover'=>3))->getField('id',true); // 神作
    	$jin_movie_ids = $movie->where(array('cellcover'=>2))->getField('id',true); // 震精
    	$diao_movie_ids = $movie->where(array('cellcover'=>1))->getField('id',true); // 略屌
    	
    	// 更新 movie_level_map
    	$this->_updateLevelMap($diao_movie_ids, 1, '略屌');
    	$this->_updateLevelMap($jin_movie_ids, 2, '震精');
    	$this->_updateLevelMap($shen_movie_ids, 3, '神作');
    	
    	$data['total_rank_a'] = count($shen_movie_ids); // 神作部数
    	$data['total_rank_b'] = count($jin_movie_ids); // 震精部数
    	$data['total_rank_c'] = count($diao_movie_ids); // 略屌部数
    	
    	$data['min_movieid'] = $data['max_movieid'] = 0;
    	
    	if (FALSE === D('MovieRankRecord')->add($data)){
    		$this->error('最后插入movie_rank_record 表失败!');
    	}
    	
    	$this->success('发布成功');
    }
    
    private function _updateLevelMap($movie_ids, $level, $level_name){
    	$movie_level_map = D('MovieLevelMap');
    	$map = array('level'=>$level);
    	$tmp_one = $movie_level_map->where($map)->find();
    	$update_data = array('match_movie'=>implode(',', $movie_ids), 'level_name'=>$level_name, 'level'=>$level);
    	if (empty($tmp_one)) {
    		$movie_level_map->create($update_data);
    		if ($movie_level_map->add() === false) {
    			$this->error('添加 movie_level_map '.$level_name.'出错');
    		}
    	} else {
    		if ($movie_level_map->where('id='.$tmp_one['id'])->save($update_data) === false){
    			$this->error('添加 movie_level_map '.$level_name.'出错');
    		}
    	}
    }
    
    /**
     * 预评级页面
     * Enter description here ...
     */
    public function preRank(){
    	$rank_type_list = array(0,1,2);
    	$last = array();
    	foreach ($rank_type_list as $rank_type) {
    		$map = array('rank_type'=>$rank_type);
    		$tmp = D('MovieRankRecord')->where($map)->field('p1,p2,p3,q1,q2,q3,s1,s2,s3,t1,t2,t3')->order('add_time desc')->find();
    		if (!empty($tmp)) {
    			$last[$rank_type] = $tmp;
    		} else {
    			$last[$rank_type] = isset($last[0]) ? $last[0] : null;     			
    		}
    	}
    	$this->assign('last',$last);
    	
    	$map = array('id'=>1);
    	$onlineMapPC = D('OnlineMapPC'); // 各个平台渠道上线模型
    	$one = $onlineMapPC->where($map)->find();
    	if (empty($one)) {$this->error('系统错误');}
    	$movie_online_ids = explode(',', $one['online_movie']);
    	$movie_online_times = explode(',', $one['online_movie_time']);
    	$min_date = date('Y-m-d H:i:s',strtotime('-45day'));
    	$rand_movie_ids = array();
    	foreach ($movie_online_times as $key=>$time) {
    		if ($time < $min_date) break;
    		$rand_movie_ids[] = $movie_online_ids[$key];
    	}
    	if (empty($rand_movie_ids)) {$this->error('没有满足评级的影片。');}
    	$map = array('id'=>array('in',$rand_movie_ids));
    	$movie_list = D('Movie')->where($map)->field('id,name,vol_count')->select();
    	$rank_movie = array();
    	foreach ($movie_list as $one) {
    		$rank_movie[$one['vol_count']][] = $one;
    	}
    	$this->assign('rank_list', $rank_movie);
    	$this->assign('movie_list', $movie_list);
    	$this->display();
    }
    
    /**
     * 预先评级，由于javascript跨域问题（只有json 木油jsonp），所以，通过这个方法，进行兼容了。
     * Enter description here ...
     */
    public function doPreRank(){
    	$rst = array('rst' => 0);
    	$index = I('index', NULL);
    	if (!isset($index)) {
    		$rst['msg'] = '参数错误';
    		$this->ajaxReturn($rst);	exit();
    	}else if ($index == 0) { // 第一部电影
    		$this->_nextPreRank(); 
    	}
    	$rank_type = I('rank_type', NULL);
    	if (!isset($rank_type)) {
    		$rst['msg'] = '参数错误';
    		$this->ajaxReturn($rst);	exit();
    	}
    	$fiter = array('p1','p2','p3','q1','q2','q3','s1','s2','s3','t1','t2','t3','start','len');
    	foreach ($fiter as $key) {
    		$data[$key] = I($key);
    		if (empty($data[$key])) {
    			$rst['msg'] = '参数错误';
    			$this->ajaxReturn($rst);
    			exit();
    		}
    	}
    	$len = $data['len']; unset($data['len']);
    	if ($index == 1) { // 第二部电影，因为第一部电影，就会调用接口生成一条记录了。这时候，修改记录内容
    		// 获取最后一条记录，则代表正在预评级的记录
    		$map = array('y'=>date('Y'),'m'=>date('m'), 'd'=>date('d'), 'pub_status'=>0);
    		$model = D('MovieRankPre'); 
    		$list = $model->where($map)->select();
    		if (count($list) != 1) {
    			$rst['msg'] = '预评级出现错误，存在两人同时评级的情况。';
    			$this->ajaxReturn($rst);  exit();
    		}
    		$map = array('id'=>1);
    		$onlineMapPC = D('OnlineMapPC'); // 各个平台渠道上线模型
    		$one = $onlineMapPC->where($map)->find();
    		if (empty($one)) {$this->error('系统错误');}
    		$movie_online_ids = explode(',', $one['online_movie']);
    		$model->save(array('id'=>$list[0]['id'],'tm'=>count($movie_online_ids),'rank_num'=>$len, 'rank_type'=>$rank_type));
    	}
    	
    	
    	$data['limit'] = 1; //电影部数
    	$data['pre'] = 1; // 是否预评级(参数已经无用，不过，可以带着)
    	$data['min_id'] = $data['max_id'] = 0; // 电影区间，0代表全部
    	$url = C('POST_API.movie_rank_pre');
    	$request = getHttpClient($url,$data);
    	if (empty($request)) {
    		$rst['msg'] = '啊。不好了呢。评级接口受阻...';
    		$this->_nextPreRank();
    		$this->ajaxReturn($rst);
    		exit();
    	} else {
    		$rst1 = $request->getContent();
    		$result = json_decode($rst1, true);
    		if (!is_array($result)) {
    			$rst['msg'] = '接口挂掉了返回结果如下:'.$result;
    			$this->_nextPreRank();
    			$this->ajaxReturn($rst);exit();
    		} else {
    			if ($result['status'] == 0) {
    				$this->_nextPreRank();
    			}
    			$rst['rst'] = $result['status'];
    			$rst['msg'] = $result['error'];
    			$rst['usetime'] = $result['usetime'];
    			$this->ajaxReturn($rst);exit();
    		}
    	}
    }
    
    /**
     * 下一次预评级
     * Enter description here ...
     */
    private function _nextPreRank(){
    	//当前时间
		$y = date('Y');
		$m = date('m');
		$d = date('d');
    	$map = array('y'=>$y,'m'=>$m, 'd'=>$d);
    	// 设置当前年月日预评级状态为不可预评级状态
    	return D('MovieRankPre')->where($map)->setField('pub_status',3);
    }
    
    private function _zhuanArray($arr, $key='m'){
    	$rst = array();
    	foreach ($arr as $val) {
    		$rst[$val[$key]] = $val;
    	}
    	return $rst;
    }
    
    private function _poge ($movie, $rule){
    	if (!empty($movie)) {
    		foreach ($movie as $key => $val) {
    			$movie[$key]['po'] = '';
    			$bi = $val['l']/$val['p'];
    			if ($val['p'] < $rule['p'] || $val['l'] < $rule['l'] || ($bi< $rule['l']/$rule['p'])) {
    				// 不满足条件 则就是破格的 
    				if ($val['p']>$rule['po']){
    					$movie[$key]['po'] .= '播,';
    				}
    				if ($val['l'] > $rule['lo']) {
    					$movie[$key]['po'] .= '顶,';
    				}
    				if ($bi > $rule['lo'] / $rule['po']) {
    					$movie[$key]['po'] .= '比,';
    				}
    				
    			}
    		}
    	}
    	return $movie;
    }
    
	private function _getMovieInfo ($arr){
		if (!is_array($arr)) {
			$arr = json_decode($arr, true);
		}
		$movie_id = array();
		if (!empty($arr)) {
			foreach ($arr as $val) {
				$movie_id[] = $val['m'];
			}
		}
		return $movie_id;
	}
	
	/**
	 * 修改电影状态
	 * Enter description here ...
	 */
	private function _updateMovie($ids,$type){
		if (empty($ids)) {return false;}
		$movie = D('Movie');
		$map = array('id'=>array('in', $ids));
		
		$movie_list = $movie->where($map)->select();
		$grapher = array();
		foreach ($movie_list as $key=>$val) {
			if (!empty($val['grapher'])) {
				$tmp = explode(',', $val['grapher']);
				foreach ($tmp as $key2=>$val2) {
					$grapher[] = array($val2, $val['name'], $val['cellcover']);
				}
			}
			
			if ($val['jian']==0) { // 只有jian值从0变为1时 才更新movie_tags_map表
				// 更新 movie_tags_map
				$nameIds = $this->_getTagsZoneTimeIds($val);
				if (!$nameIds['rst']) {
			    	$this->error($nameIds['msg']);
			    }
			    $tags1 = array_keys($nameIds['tags']);
			    $zones1 = array_keys($nameIds['zones']);
			    $showtimes1 = array_keys($nameIds['showtimes']);
			    $this->_updateTagsMap($val, $tags1, $zones1, $showtimes1); // 更新movieTagsMap
			}
		}
		
		// 发动态给用户
		$cellcover = array('【无】','【略屌】','【震精】','【神作】');
		$tmp_add_time = date('Y-m-d H:i:s', NOW_TIME);
		foreach ($grapher as $val) {
			$new_msg =  'ヽ(･∀･)ﾉ您的作品《'.$val[1].'》评级升级'.$cellcover[$val[2]].'>'.$cellcover[$type];
			$news_data[] = array(
				'user_id'=>1, 'to_user_id'=>$val[0], 
				'comment_content'=>$new_msg, 'reply_comment_id' => 0, 'readed' => 0,
				'reply_from' => 'notice', 'reply_from_data' => '1',
				'pre_length' => 0, 'secret_send' => 0,
				'add_time' => $tmp_add_time
			);
		}
		
		$memberNew = D('MemberNew');
		if (false === $memberNew->addAll($news_data)){
			$this->error('发送用户动态出错');
		}
		
		$data = array('jian'=>1, 'cellcover'=>$type);
		if (FALSE === $movie->where($map)->setField($data)){
			$this->error('更新Movie表错误，'.$cellcover[$type]);
		}
	}
	
	/**
     * 修改Tag_Map 老的，已废弃;
     * Enter description here ...
     * @param unknown_type $movie
     * @param unknown_type $tags
     * @param unknown_type $zones
     * @param unknown_type $showtimes
     */
    private function _updateTagsMap($movie, $tags, $zones, $showtimes){
    	foreach ($tags as $tag) {
    		foreach ($zones as $zone) {
    			foreach ($showtimes as $showtime) {
    				$onlineMapTags = D('OnlineMapTags');
    				$map = array( 'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime , 'jian'=>1);
    				$tmp = $onlineMapTags->where($map)->field('id,tag_id,zone_id,showtime_id,jian,match_movie')->select();
    				$update = true; // 更新记录的开关 
    				if (empty($tmp)) { 
    					$update = false; //关闭更新
    					$data = array (
    						'tag_id' => $tag, 'zone_id' => $zone, 'showtime_id' => $showtime, 'jian' => 1,
    						'movie_count' => 1, 'match_movie' => $movie['id'], 'last_movie_name' => $movie['name'],
    						'last_movie_pic' => $movie['bpic'], 'last_movie_id' => $movie['id']
    					);
    					$onlineMapTags->create($data);
   						$onlineMapTags->create($data);
   						if(false === $onlineMapTags->add()) {
   							$this->error('插入movie_tags_map出错jian1');
   						}
    				} elseif (count($tmp) == 1) { 
    					// 直接更新
    				} else { 
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
    
    /* 2015年12月1日19:36:06
    public function shoudongPingji(){
    	$rank_movie = array();
    	$data = array(
    			'mr'=>83139,
    			'ml' =>1248.23,'mlr'=>0.015014,
    			'tm' => 1670, 'tl'=>1670*83139, 'tr'=>floor(1670*1248.23),
    			'p1'=>0.49,'p2'=>0.31,'p3'=>0.24,
    			'q1'=>2.03,'q2'=>1.63,'q3'=>3.17,
    			's1'=>0,'s2'=>0,'s3'=>0,
    			't1'=>0,'t2'=>0,'t3'=>0,
    			'y'=>2015,'m'=>'12','d'=>1,'h'=>19,
    			'rank_type'=>2,'rank_num'=>1670,
    			'add_time'=>date('Y-m-d H:i:s')
    	);
    	
    	$shen_list = explode(',', '5742,6358,5994,6189,6207,5882,5942,5682,5802,5387,6064,5362,5965,5987,5884,6027,6581,5127,5244,5546,5077,6140,4291,5649,6038');
    	$jin_list = explode(',', '6153,5114,6288,4237,5207,6136,5152,5782,6008,5122,5403,5614,5713,5803,5252,5842,5472,5551,4892,5213,6017,3446,5537,4952,5853,5117,6093,5671,5243,5552,6124,6404,6056,5512,5277,6061,6074,6020,6120,5260,2273,6126,5815,5646,5578,5868,5583,5470,4299,5569,2634,5344,6308,5326,5762,5629,5258,5440,5347,6281,5364,5607,5453,4611,5798,5612,5749,5397,5469,5579,6359');
    	$diao_list = explode(',', '5963,6453,6496,5509,5336,6451,3691,5907,3561,5683,6289,4561,6529,5264,6096,6161,5588,5534,5233,4822,5638,5284,4298,6310,5071,5461,4154,6215,3137,6075,5676,4644,3920,5695,5801,3302,6013,6012,6689,5072,5643,6015,5174,5572,6276,5074,4371,5933,4988,3635,4242,5819,3288,6521,6559,5214,5602,5696,4250,5162,4498,5608,5787,5281,3988,6856,6152,5627,5407,5734,3294,6324,4356,5039,4126,5633,3665,5700,2831,5040,5698,5634,5390,6167,5758,4128,3822,3893,5439,2667,3336');
    	
    	echo count($shen_list).'<br />';
    	echo count($jin_list).'<br />';
    	echo count($diao_list).'<br />';
    	
    	$movie_list = json_decode(file_get_contents('movie_list.txt'), true);
    	//{"m":"6861","p":"214098","l":"2560","pg":0}
    	foreach ($shen_list as $movie_id) {
    		$tmp = $movie_list[$movie_id];
    		$rank_movie['shen'][$movie_id] = array(
    			'm' => $movie_id,
    			'p' => $tmp['played'],
    			'l'=> $tmp['ding'],
    			'pg' => 0
    		);
    	}
    	foreach ($jin_list as $movie_id) {
    		$tmp = $movie_list[$movie_id];
    		$rank_movie['jin'][$movie_id] = array(
    				'm' => $movie_id,
    				'p' => $tmp['played'],
    				'l'=> $tmp['ding'],
    				'pg' => 0
    		);
    	}
    	foreach ($diao_list as $movie_id) {
    		$tmp = $movie_list[$movie_id];
    		$rank_movie['diao'][$movie_id] = array(
    				'm' => $movie_id,
    				'p' => $tmp['played'],
    				'l'=> $tmp['ding'],
    				'pg' => 0
    		);
    	}
    	
    	$data['rank_a'] = json_encode(array_merge($rank_movie['shen']));
    	$data['rank_b'] = json_encode(array_merge($rank_movie['jin']));
    	$data['rank_c'] = json_encode(array_merge($rank_movie['diao']));
    	
    	if (!empty($rank_movie['shen'])) {
//    		print_r(array_keys($rank_movie['shen'])); exit();
    		$this->_updateMovie(array_keys($rank_movie['shen']), 3);
    	} 
    	if (!empty($rank_movie['jin'])) {
    		$this->_updateMovie(array_keys($rank_movie['jin']), 2);
    	}
    	if (!empty($rank_movie['diao'])) {
    		$this->_updateMovie(array_keys($rank_movie['diao']), 1);
    	}
    	 
    	$data['rank_a_mr'] = $data['mr'] * $data['p1'];
    	$data['rank_b_mr'] = $data['mr'] * $data['p2'];
    	$data['rank_c_mr'] = $data['mr'] * $data['p3'];
    	 
    	$data['rank_a_mlr'] = $data['mlr'] * $data['q1'];
    	$data['rank_b_mlr'] = $data['mlr'] * $data['q2'];
    	$data['rank_c_mlr'] = $data['mlr'] * $data['q3'];
    	 
    	$data['rank_a_r'] = floor($data['mr'] * $data['s1']); // 神作破格阅读数
    	$data['rank_b_r'] = floor($data['mr'] * $data['s2']); // 震精破格阅读数
    	$data['rank_c_r'] = floor($data['mr'] * $data['s3']); // 略屌破格阅读数
    	 
    	$data['rank_a_l'] = floor($data['ml'] * $data['t1']); // 神作破格点赞数
    	$data['rank_b_l'] = floor($data['ml'] * $data['t2']); // 震精破格点赞数
    	$data['rank_c_l'] = floor($data['ml'] * $data['t3']); // 略屌破格点赞数
    	 
    	$movie = D('Movie');
    	$shen_movie_ids = $movie->where(array('cellcover'=>3))->getField('id',true); // 神作
    	$jin_movie_ids = $movie->where(array('cellcover'=>2))->getField('id',true); // 震精
    	$diao_movie_ids = $movie->where(array('cellcover'=>1))->getField('id',true); // 略屌
    	 
    	// 更新 movie_level_map
    	$this->_updateLevelMap($diao_movie_ids, 1, '略屌');
    	$this->_updateLevelMap($jin_movie_ids, 2, '震精');
    	$this->_updateLevelMap($shen_movie_ids, 3, '神作');
    	 
    	$data['total_rank_a'] = count($shen_movie_ids); // 神作部数
    	$data['total_rank_b'] = count($jin_movie_ids); // 震精部数
    	$data['total_rank_c'] = count($diao_movie_ids); // 略屌部数
    	 
    	$data['min_movieid'] = $data['max_movieid'] = 0;
    	 
    	if (FALSE === D('MovieRankRecord')->add($data)){
    		$this->error('最后插入movie_rank_record 表失败!');
    	}
    	 
    	$this->success('发布成功');
    }
    //*/
}