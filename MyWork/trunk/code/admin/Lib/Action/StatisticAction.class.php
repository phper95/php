<?php
// 后台用户模块
class StatisticAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
    function index() {
    	$this->display();
    }
    
    /**
     * 
     * Enter description here ...
     */
    function apiError(){
    	$day = I('day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['day'] = $day;
    	
    	load('@.logger'); 
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getApiError($day);
    	if (is_array($rst)) {
    		$this->assign('rst',$rst);
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    
    /**
     * 
     * Enter description here ...
     */
    function ipAddrActive(){
    	$day = I('day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;
    	$_POST['day'] = $day;
    	
    	load('@.logger'); 
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getIpAddrActive($day);
    	if (is_array($rst)) {
    		$sort = array();
    		foreach ($rst as $key=>$item){
    			$sort[$key] = $item[1];
    		}
    		arsort($sort);
    		$result = array();
    		foreach ($sort as $key=>$item) {
    			$result[] = $rst[$key];
    		}
    		$this->assign('rst',$result);
    	} else {
	    	$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    	$this->assign('error_info',$rst);
	    }
	    $this->display();
    }
    
    
    /**
     * IMEI用户活跃数
     * Enter description here ...
     */
    function imeiActive(){
    	$day = I('s_day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['s_day'] = $day;
    	
    	$e_day = I('e_day');
    	load('@.logger'); 
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getImeiActive($day,$e_day);
    	
	    if (is_array($rst)) {
	    	if (empty($e_day)) {
	    		$this->assign('rst',$rst);
	    	} else {
	    		$rst2 = array();
	    		foreach ($rst as $day=>$val) {
	    			$rst2[$day] = $val['total'];
	    		}
	    		$this->assign('rst',$rst2);
	    	}
	    } else {
	    	$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    	$this->assign('error_info',$rst);
	    }
    	
		$this->display();
    }
    
    private  function _getRandmArray($len,$max,$min=1) {
    	$arr = array();
    	for ($i=$min; $i<=$max; $i++) {
    		$arr[$i] = $i;
    	}
    	
    	$rst = array();
    	$cha = $max-$min;
    	$arr = array_values($arr);
    	for ($i=0; $i<$len; $i++) {
    		$x = rand(0, $cha);
    		$rst[] = $arr[$x];
    		unset($arr[$x]);
    		$arr = array_values($arr);
    		$cha --;
    	}
    	rsort($rst);
    	return $rst;
    }
    
    /**
     * 活跃留存统计
     * Enter description here ...
     */
    function activeAlive(){
    	$s_day = I('s_day','2014-04-01');
    	$e_day = I('e_day','2099-01-01');
    	$type = I('type','day');
    	$_POST['s_day'] = $s_day;
    	$_POST['type'] = $type;
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getActiveAlive($s_day, $e_day, $type);
    	if (is_array($rst)) {
    		foreach ($rst as $day=>$item) {
    			if ($day<$s_day || (!empty($e_day) && $day>$e_day)){unset($rst[$day]);}
    		}
    		$this->assign('rst', $rst);
    		
    		$len = count($rst);
    		$rst2 = array();
    		$max_len = 0;
    		if ($len>60) {
    			$rand_arr = $this->_getRandmArray(10, $len);
    			$i = 1;
    			$ge = array_pop($rand_arr);
    			foreach ($rst as $day=>$item) {
    				if (empty($rand_arr)) break;
    				if ($i==$ge) {
    					$tmp = $logger->getActiveAliveByDay($day);
    					$max_len = max(array($max_len,count($tmp)));
    					$top_index = 1;
    					foreach ($tmp as $day2=>$item2) {
    						if ($top_index == 1) {
    							$max_top = $item2['active'];
    						} else {
    							$tmp[$day2]['day'] = $day2;
    							$tmp[$day2]['persent'] = round($item2['active']*100/$max_top,2);
    							$tmp[$day2]['str'] = $item2['active'].'/'.$max_top .' ≈ '.$tmp[$day2]['persent'];
    						}
    						$top_index = 0;
    					}
    					$rst2[$day] = array_values($tmp);
    					$ge = array_pop($rand_arr);
    				}
    				$i++;
    			}
    		} else {
    			$rst['2014-07-01'] = array();
    			$rst['2014-07-03'] = array();
    			foreach ($rst as $day=>$item) {
    				$tmp = $logger->getActiveAliveByDay($day);
    				$max_len = max(array($max_len,count($tmp)));
    				$top_index = 1;
    				foreach ($tmp as $day2=>$item2) {
    					if ($top_index == 1) {
    						$max_top = $item2['active'];
    					} else {
    						$tmp[$day2]['day'] = $day2;
    						$tmp[$day2]['persent'] = round($item2['active']*100/$max_top,2);
    						$tmp[$day2]['str'] = $item2['active'].'/'.$max_top .' ≈ '.$tmp[$day2]['persent'];
    					}
    					$top_index = 0;
    				}
   					$rst2[$day] = array_values($tmp);
    			}
    		}
    		
    		$this->assign('rst2',$rst2);
    		$this->assign('max_len',$max_len);
    		$this->assign('total_str','终端统计分析');
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    
    /**
     * 获取电影弹幕
     * Enter description here ...
     */
    function poptxt(){
    	$movie_id = I('id');
    	$day = I('day');
    	if (empty($movie_id) || empty($day)) {
    		$this->error('参数错误');
    	}
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getPoptxt($day);
    	if (is_array($rst)) {
    		$map = array('movie_id'=>$movie_id);
	    	$model = D('MComment');
	    	$max_p_id = $model->where($map)->max('id');
	    	$min_p_id = $model->where($map)->min('id');
	    	
	    	$result = array();
	    	$min_id = $max_id = 0;
	    	foreach ($rst as $time => $lev1) {
	    		$hour = intval(substr($time,-2));
	    		foreach ($lev1 as $pvc=>$lev2) {
	    			foreach ($lev2 as $item) {
	    				$tmp = explode(' | ', $item);
	    				if ($tmp[0] == $movie_id) {
	    					$tmp2 = explode('-', $tmp[1]);
	    					if ($tmp2[0] != $tmp2[1] && $tmp2[0] >= $min_p_id && $tmp2[1]<=$max_p_id){
	    						$min_id = ($min_id == 0 || $min_id > $tmp2[0] ) ? $tmp2[0] : $min_id;
	    						$max_id = $max_id < $tmp2[1] ? $tmp2[1] : $max_id;
	    						$page = ($tmp2[0]-$min_p_id) . '-' . ($tmp2[1]-$min_p_id); 
	    						$result[$page][$pvc][$hour] = array('p'=>$tmp[3],'u'=>$tmp[4],'m'=>$tmp[5]);
	    					}
	    				}
	    			}
	    		}
	    	}
	    	if (empty($result)) {
	    		$this->assign('error_info','文件存在，不过没有数据哟！');
	    	} else {
	    		$this->assign('cha_nums',$max_p_id-$min_p_id);
	    		$this->assign('rst',$result);
	    	}
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    
    
    /**
     * 获取活跃IP
     * Enter description here ...
     */
    function ipActive (){
    	$day = I('day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['day'] = $day;
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getIpActive($day);
    	
    	$result = array();
    	$max_h = $max_t = 0;
    	if (is_array($rst)) {
	    	foreach ($rst as $pvc => $lev1) {
	    		foreach ($lev1 as $time => $lev2) {
	    			$num = count($lev2);
	    			$hour = intval(substr($time,-2));
	    			$result[$pvc][$hour] = $num;
	    			$result[$pvc]['t'] = isset($result[$pvc]['t']) ? array_merge($result[$pvc]['t'],$lev2) : $lev2;
	    			$total = count($result[$pvc]['t']);
	    			$max_h = $max_h > $num ? $max_h : $num;
	    			$max_t = $max_t > $total ? $max_t : $total;
	    		}
	    	}
	    	
    		foreach ($result as $key=>$v) {
    			$result[$key]['t'] = count($v['t']);
    		}
	    	
	    	$this->assign('max_h', $max_h);
    		$this->assign('max_t', $max_t);
	    	$this->assign('rst',$result);
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	
    	$this->display();
    	
    }
    
    /**
     * 获取活跃用户By渠道
     * Enter description here ...
     */
    function pvcActiveMember (){
    	$day = I('day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['day'] = $day;
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getPvcActiveMember($day);
    	$max_t = $max_h = 0;
    	if (is_array($rst)) {
    		$result = array();
    		foreach ($rst as $time=>$lev1) {
    			$hour = intval(substr($time,-2));
    			foreach ($lev1 as $pvc=>$members) {
    				$tmp = explode(' | ', $pvc);
    				$value = count($members);
//					$value = $members;
    				$result[$pvc][$hour] = $value;
    				$result[$pvc]['t'] = isset($result[$pvc]['t']) ? (array_unique(array_merge($result[$pvc]['t'],$members))) : $members;
//    				$result[$pvc]['t'] = isset($result[$pvc]['t']) ? ($result[$pvc]['t']+$value) : $value;
    				$max_h = $max_h > $value ? $max_h : $value;
//    				$max_t = $max_t > ($result[$pvc]['t']) ? $max_t : ($result[$pvc]['t']);
    				$max_t = $max_t > count($result[$pvc]['t']) ? $max_t : count($result[$pvc]['t']);
    			}
    		}
    		foreach ($result as $key=>$v) {
    			$result[$key]['t'] = count($v['t']);
    		}
    		$this->assign('max_h', $max_h);
    		$this->assign('max_t', $max_t);
    		$this->assign('rst',$result);
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    
    /**
     * 
     * Enter description here ...
     */
    function pvcMoviePlayed () {
    	$day = I('day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['day'] = $day;
    	
    	$p = I('platform','');
    	$v = I('ver','');
    	$c = I('channel','');
    	$max_t_p = 0;
    	$map_p = 0;
    	
    	$platform_list = $ver_list = $channel_list = array();
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getPvcMoviePlayed($day);
    	if (is_array($rst)) {
    		$result = array();
	    	foreach($rst as $movie_id => $lev1) {
	    		foreach ($lev1 as $time=>$lev2) {
	    			$hour = intval(substr($time,-2));
	    			foreach ($lev2 as $pvc => $nums) {
	    				$tmp = explode(' | ', $pvc);
	    				$platform_list[$tmp[0]] = true;
	    				$ver_list[$tmp[2]] = true;
	    				$channel_list[$tmp[1]] = true;
	    				if ((empty($p) || $p == $tmp[0]) && (empty($v) || $v==$tmp[2]) && (empty($c) || $c==$tmp[1]) ) { 
	    					$arr = isset($result[$movie_id][$hour]) ? $result[$movie_id][$hour] : array('play'=>0,'user'=>0,'max_p'=>0);
	    					$arr['play'] += $nums['play'];
	    					$arr['user'] += $nums['user'];
	    					$arr['max_p'] += $nums['max_p'];
	    					$max_p = $arr['play'] > $max_p ? $arr['play'] : $max_p;
	    					$result[$movie_id][$hour] = $arr;
	    					$result[$movie_id]['t_p'] = isset($result[$movie_id]['t_p']) ? ($result[$movie_id]['t_p']+$nums['play']) : $nums['play'];
	    					$max_t_p = $result[$movie_id]['t_p'] > $max_t_p ? $result[$movie_id]['t_p'] : $max_t_p; 
	    				}
	    			}
	    		}
	    	}
	    	krsort ( $result );
			$map = array ('id' => array ('in', array_keys ( $result ) ) );
			$movies = D ( 'Movie' )->getField ( 'id,name' );
			$this->assign ( 'rst', $result );
			$this->assign ( 'movies', $movies );
			$this->assign('p_list', array_keys($platform_list));
			$this->assign('v_list', array_keys($ver_list));
			$this->assign('c_list', array_keys($channel_list));
			$this->assign('max_p', $max_p);
			$this->assign('max_t_p', $max_t_p);
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    	$this->assign('error_info',$rst);
    	}
		$this->display();
    }
   
    
	function movieDay() {
    	$day = I('post.s_day');
    	if (!empty($day)){
	    	$yestoday = toDate(strtotime('-1 day'), 'Y-m-d');
	    	$day = empty($day)? $yestoday: $day;  
	    	$_POST['s_day'] = $day;
	    	
	    	$e_day = I('e_day');
	    	
	    	if ($day > $yestoday || $e_day > $yestoday) {
	    		$this->error('参数错误啊');
	    	}
	    	
	    	load('@.logger');
	    	$logger = new Api_Logger_Analyse();
	    	/*
	    	 * $map = array('add_time' => $day.' 00:00:00');
    	$total_play_day = D('MemberUPlayedDay')->where($map)->sum('played');
    	print_r($total_play_day);
	    	 */
	    	$fileNames = array(
	    		'play' => array('model'=>'MemberUMoviePlayed','file' => 'movie_analyse/movie_play.txt'),
	    		'comment' => array('model'=>'CommentMovie','file' => 'movie_analyse/movie_comment.txt'),
	    		'poptxt' => array('model'=>'PoptxtMovie','file' => 'movie_analyse/movie_poptxt.txt'),
	    		'keep' => array('model'=>'KeepMovie','file' => 'movie_analyse/movie_keep.txt'),
	    		'ding' => array('model'=>'DingMovie','file' => 'movie_analyse/movie_ding.txt')
	    	);
	    	$data = array();
	    	foreach ($fileNames as $key=>$item) {
	    		$data[$key] = $logger->getTotalFile($item['file']);
	    		$models[$key] = D($item['model']);
	    	}
	    	
	    	// view 不同于其他处理方法
	    	$data['view'] = $logger->getTotalFile('movie_analyse/movie_view.txt');
	    	
	    	if (empty($e_day)) {
	    		$all_users = $all_movies = array();
	    		$users = $movies = array();
	    		$result = array();
	    		foreach ($models as $key=>$model) {
	    			$list = array();
	    			$map = array('add_time'=>array('between',array($day.' 00:00:00',$day.' 23:59:59')));
	    			$list = $model->where($map)->field('user_id,movie_id,add_time')->select();
	    			$total[$key] = count($list);
	    			$all_users[$key] = $all_movies[$key] = array();
	    			
		    		
			    	for ($i=0; $i<24; $i++) {
			    		$result[$key][$i] = array('total'=>0,'users'=>0, 'movies'=>0);
			    	}
			    	
		    		foreach ($list as $item) {
		    			$all_users[$key][$item['user_id']] = true;
		    			$all_movies[$key][$item['movie_id']] = isset($all_movies[$key][$item['movie_id']])?($all_movies[$key][$item['movie_id']]+1) : 1;
		    			$hour = intval(substr($item['add_time'], 11, 2));
		    			$users[$key][$hour][$item['user_id']] = true;
		    			$movies[$key][$hour][$item['movie_id']] = true;
		    			$result[$key][$hour]['total']++;
		    		}
		    		
		    		foreach ($all_movies[$key] as $key2=>$val){
			    		if ($val <=10) {unset($movies[$key][$key2]);}
			    	}
			    	
			    	if (!isset($data[$key][$day])) {
			    		$data[$key][$day] = array('total'=>$total[$key],'users'=>count($all_users[$key]),'movies'=>count($all_movies[$key]));
			    		$logger->setTotalFile($fileNames[$key]['file'], $data[$key]);
			    	}
		    		
	    			for ($i=0; $i<24; $i++) {
			    		$result[$key][$i]['users'] = count($users[$key][$i]);
			    		$result[$key][$i]['movies'] = count($movies[$key][$i]);
			    	}
		    		unset($list);
	    		}
	    		
	    		$rst = array();
	    		for($i=0; $i<24; $i++) {
	    			$rst[$i] = array(
	    				'total'=>$result['play'][$i]['total'],
	    				'users'=>$result['play'][$i]['users'],
	    				'movies'=>$result['play'][$i]['movies'],
	    				'comments'=>$result['comment'][$i]['total'],
	    				'poptxts'=>$result['poptxt'][$i]['total'],
	    				'keeps'=>$result['keep'][$i]['total'],
	    				'dings'=>$result['ding'][$i]['total']
	    			);
	    		}
	    		
	    		$this->assign('rst',$rst);
	    		$this->assign('total_str','总共播放：'.$total.' 次，播放人数：'.count($all_users).' (已经去重)，影片：'.count($all_movies));
	    	} else {
	    		$now_day = $day;
	    		$list = array();
	    		for($now_day = $day; $now_day<=$e_day; $now_day=date('Y-m-d',strtotime($now_day)+86400)){
		    		foreach ($models as $key=>$model) {
		    			if (isset($data[$key][$now_day])) {
		    				$result[$key][$now_day] = $data[$key][$now_day]; 
		    			} else {
		    				$tmp = $this->_getMovieAnaly($model, $now_day);
		    				$result[$key][$now_day] = $tmp[$now_day];
		    				$data[$key][$now_day] = $result[$key][$now_day]; 
		    			}
		    		}
		    		
		    		// 2014年10月16日15:11:05 加上View 因为View不同于其他统计方法，故加在此
		    		if (isset($data['view'][$now_day])) {
		    			$result['view'][$now_day] = $data['view'][$now_day];
		    		} else {
		    			$map = array('day_time' => $now_day.' 00:00:00');
				    	$data['view'][$now_day] = $result['view'][$now_day] = D('MemberUPlayedDay')->where($map)->sum('played');
				    	
		    		}
		    		
		    		$list[$now_day] = array(
		    			'total'=>$result['play'][$now_day]['total'],
	    				'users'=>$result['play'][$now_day]['users'],
	    				'movies'=>$result['play'][$now_day]['movies'],
	    				'comments'=>$result['comment'][$now_day]['total'],
	    				'poptxts'=>$result['poptxt'][$now_day]['total'],
	    				'keeps'=>$result['keep'][$now_day]['total'],
	    				'dings'=>$result['ding'][$now_day]['total'],
		    			'views'=>$result['view'][$now_day]
		    		);
	    		}
	    		foreach ($models as $key=>$model) { // 写入缓存
	    			$logger->setTotalFile($fileNames[$key]['file'], $data[$key]);
	    		}
	    		$logger->setTotalFile('movie_analyse/movie_view.txt', $data['view']);
	    		$this->assign('rst',$list);
	    	}
    	}
    	$this->display('movieDay');
    }
    
    private function _getMovieAnaly(Model $model,$day){
    	$map = array('add_time'=>array('between',array($day.' 00:00:00',$day.' 23:59:59')));
    	$list = $model->where($map)->field('user_id,movie_id,add_time')->select();
    	$total = count($list);
    	$users = $movies = array();
    	foreach ($list as $key=>$item) {
    		$users[$item['user_id']] = true;
    		$movies[$item['movie_id']] = isset($movies[$item['movie_id']]) ? ($movies[$item['movie_id']]+1) : 1;
    	}
    	unset($list);
    	foreach ($movies as $key=>$val){
    		if ($val <=10) {unset($movies[$key]);}
    	}
    	
    	return array($day=>array('total'=>$total, 'users'=>count($users), 'movies'=>count($movies)));
    }
    
    /**
     * 影片日数据曲线
     * Enter description here ...
     */
    function movieDayBylog (){
    	$day = I('s_day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['s_day'] = $day;
    	
    	$e_day = I('e_day');
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	
    	if (empty($e_day)) {
    		$rst = $logger->getMoviePlayed($day);
    		if (is_array($rst)) {
    			$result = array();
    			foreach ($rst as $hour=>$val) {
    				$result[$hour] = isset($result[$hour]) ?  $result[$hour] : 0;
    				foreach ($val as $val2) {
    					$result[$hour] += $val2['play'];
    				}
    			}
    			$this->assign('rst',$result);
    		} else {
    			$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    		$this->assign('error_info',$rst);
    		}
    		
    	} else {
    		$rst = $logger->getMoviePlayed($day,$e_day);
    		if (is_array($rst)) {
    			$result = array();
	    		foreach ($rst as $day=>$val) {
	    			if (is_numeric($day)) {$day = date('Y-m-d',$day);}
	    			$result[$day] = isset($result[$day]) ? $result[$day] : 0;
	    			foreach ($val as $val2) {
	    				$result[$day] += $val2['play'];
	    			}
	    		}
	    		$this->assign('rst',$result);
    		} else {
    			$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    		$this->assign('error_info',$rst);
    		}
    	}
    	$this->display();
    }
    
    
    /**
     * 影片播放统计
     * Enter description here ...
     */
    function moviePlayed () {
    	$day = I('s_day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['s_day'] = $day;
    	
    	$e_day = I('e_day');
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	
    	if (empty($e_day)) {
	    	$rst = $logger->getMoviePlayed($day);
	    	if (is_array($rst)) {
	    		$result = array();
	    		foreach ($rst as $hour=>$val) { // 转成 电影-小时-数据
	    			foreach ($val as $val2) {
	    				$result[$val2['id']][$hour] = $val2;
	    				$result[$val2['id']]['t_p'] = isset($result[$val2['id']]['t_p']) ? ($result[$val2['id']]['t_p']+$val2['play'] ) : $val2['play'];
	    				$result[$val2['id']]['t_u'] = isset($result[$val2['id']]['t_u']) ? ($result[$val2['id']]['t_u']+$val2['user'] ) : $val2['user'];
	    				$result[$val2['id']]['t_m'] = isset($result[$val2['id']]['t_m']) ? ($result[$val2['id']]['t_m']+$val2['max_p'] ) : $val2['max_p'];
	    			}
	    		}
	    		krsort($result);
	    		$map = array('id'=>array('in',array_keys($result)));
	    		$movies = D('Movie')->getField('id,name');
	    		$this->assign('rst', $result);
	    		$this->assign('movies',$movies);
	    	} else {
	    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    		$this->assign('error_info',$rst);
	    	}
    	} else {
	    	// 获取总文件  (未完成，注意注意。。。。。。。)
	    	$day2 = $e_day;
	    	$rst = $logger->getMoviePlayed($day,$day2);
	    	if (is_array($rst)) {
	    		$result = array();
	    		foreach ($rst as $day =>$val) { // 转成 电影-天-数据
	    			foreach ($val as $val2) {
	    				$result[$val2['id']][$day] = $val2;
	    				$result[$val2['id']]['t_p'] = isset($result[$val2['id']]['t_p']) ? ($result[$val2['id']]['t_p']+$val2['play'] ) : $val2['play'];
	    				$result[$val2['id']]['t_u'] = isset($result[$val2['id']]['t_u']) ? ($result[$val2['id']]['t_u']+$val2['user'] ) : $val2['user'];
	    				$result[$val2['id']]['t_m'] = isset($result[$val2['id']]['t_m']) ? ($result[$val2['id']]['t_m']+$val2['max_p'] ) : $val2['max_p'];
	    			}
	    		}
	    		krsort($result);
	    		$map = array('id'=>array('in',array_keys($result)));
	    		$movies = D('Movie')->getField('id,name');
	    		print_r($result);
	    	} else {
	    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    		$this->assign('total_str', $rst);
	    	}
    	}
    	
    	$this->display();
    }
    
    /**
     * 获取电影列表习惯 
     */
    function movieList(){
    	$day = I('day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['day'] = $day;
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getMovieList($day);
    	if (is_array($rst)) {
    		$showtime = D('MShowtime')->getField('id,name');
    		$zone = D('MZone')->getField('id,name');
    		$tag = D('MovieTag')->getField('id,name');
    		$this->assign('showtime',$showtime);
    		$this->assign('zone', $zone);
    		$this->assign('tag', $tag);
    		$this->assign('rst',$rst);
    		
    		$list = array();
    		$total = 0;
    		foreach ($rst as $p_num => $lev1) {
    			foreach ($lev1 as $pvc => $lev2) {
    				foreach ($lev2 as $t => $lev3) {
    					foreach ($lev3 as $nums) {
    						$total += $nums['post'];
	    					$key = $nums['z_id'].'-'.$nums['st_id'].'-'.$nums['t_id'].'-'.$nums['jin'].'-'.$nums['l_px'];
	    					if (isset($list[$key])) {
	    						$list[$key]['post'] += $nums['post'];
	    					} else {
	    						$list[$key] = $nums;
	    					}
    					}
    				}
    			}
    		}
    		$this->assign('total', $total);
    		$this->assign('list', $list);
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    
    /**
     * 活跃用户统计
     * Enter description here ...
     */
    function activeMember(){
    	$day = I('s_day');
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['s_day'] = $day;
    	
    	$e_day = I('e_day');
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	
    	if (empty($e_day)) {
	    	$rst = $logger->getActiveMember($day);
	    	if (!empty($rst)) {
	    		$this->assign('rst', $rst);
	    	} else {
	    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
	    		$this->assign('error_info',$rst);
	    	}
    	}
    	
    	// 获取总文件
    	$day2 = empty($e_day) ? $day : $e_day;
    	$rst = $logger->getActiveMember($day,$day2);
    	if (!empty($rst)) {
    		if (empty($e_day)) {
	    		$str = '';
	    		foreach ($rst as $key=>$val) {
	    			$str = "$key  总活跃用户数量：".$val;
	    		}
    		} else {
    			$str = '--';
	    		$this->assign('rst', $rst);
    		}
    		$this->assign('total_str', $str);
    	} else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('total_str', $rst);
    	}
    	$this->display();
    }

    /**
     * 接口日志分析
     * Enter description here ...
     */
    function totalStat(){
    	$day = I('day');
    	
    	$day = empty($day)?toDate(strtotime('-1 day'), 'Y-m-d') : $day;  
    	$_POST['day'] = $day;
    	
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	$rst = $logger->getTotalStat($day);
    	if (is_array($rst)) {
	    	$total = array();
	    	$need_key = array (
	    		'post' => 'array_sum', // 请求次数
				'memc' => 'array_sum', // memcache 请求次数
				'user' => 'array_sum', // 请求用户数
				'u_max' => 'max', // 单个用户最大请求次数
				'err' => 'array_sum', // 错误次数
				'json' => 'array_sum', // 该接口全部请求返回JSON的总计字符长度
				'j_max' => 'max', // 该接口全部请求返回JSON中，最长的字符长度
				'j_min' => 'min', // 该接口全部请求返回JSON中，最短的字符长度
				'time' => 'array_sum', // 该接口该时段请求总计耗时
				't_max' => 'max', // 该接口该时段请求最大耗时
				't_min' => 'min'// 该接口该时段请求最小耗时
	    	);
	    	$api_total = array();
	    	foreach($rst as $api => $xx) {
	    		foreach ($xx as $dvc=>$xx2) {
	    			foreach ($xx2 as $time=>$tmp) {
	    				foreach ($need_key as $key=>$v) {
	    					$num = isset($api_total[$api][$key]) ? $api_total[$api][$key] : 0;
	    					if ($v == 'min') {$num = $tmp[$key];}
	    					$api_total[$api][$key] = $v(array($num, $tmp[$key]));
	    				}
	    			}
	    		}
	    	}
	    	
	    	$api = '总计';
	    	$tmp = array();
	    	foreach ( $api_total as $val ) {
				foreach ( $need_key as $key => $v ) {
					$num = isset ( $tmp [$api] [$key] ) ? $tmp [$api] [$key] : $val[$key];
					$tmp [$api] [$key] = $v ( array ($num, $val [$key] ) );
				}
				
			}
			$api_total = array_merge($tmp,$api_total);
			
			
			$this->assign('need_key', $need_key);
	    	$this->assign('api_total', $api_total);
	    	$this->assign('rst',$rst);
    	}else {
    		$rst = empty($rst) ? '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。' : $rst;
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    /**
     * 电影数据统计
     * Enter description here ...
     */
    function movie(){
    	$online_movie = $this->_getOnlineIdList();
    	$online_movie_ids = array_keys($online_movie);
    	$all_movie = D('Movie')->getField('id,name,played,ding,cai,tags,share,keep,comment_count,poptxt_count');
    	$all_movie_ids = array_keys($all_movie);
    	$total = array('played'=>0, 'ding'=>0, 'cai'=>0, 'share'=>0, 'keep'=>0, 'comment_count'=>0, 'poptxt_count'=>0); // 获取总上线影片的各项次数
    	$total_tags = array(); // 不同类型下的各项数据
    	$movie_max = array(); // 各项数据下最大数据的对应电影 
    	$movie_top = array(); // 电影各项数据排行榜
    	foreach ($online_movie as $id=>$time) {
    		$tags = explode('|', $all_movie[$id]['tags']);
    		foreach ($total as $key=>$t) {
    			$num = $all_movie[$id][$key];
    			if ($num == 0) continue;
    			$total[$key] += $num;
    			$movie_top[$key][$id] = $num; 
    			foreach ($tags as $tag) {
    				$total_tags[$tag][$key] += $num;
    			}
    			$movie_max[$key] = isset($movie_max[$key][$key]) ? ($movie_max[$key][$key]<$num ? $all_movie[$id] : $movie_max[$key]) : $all_movie[$id];
    		}
    	}
    	$movie_top_show = array();
    	foreach ($movie_top as $key=>$val) {
    		arsort($movie_top[$key]);
    		$i = 0;
    		foreach ($movie_top[$key] as $movie_id => $num) {
    			$movie_top_show[$key][$movie_id] = $all_movie[$movie_id];
    			$movie_top_show[$key][$movie_id]['online_time'] = $online_movie[$movie_id];
    			$cha = NOW_TIME - strtotime($online_movie[$movie_id]);
    			$movie_top_show[$key][$movie_id]['to_day'] = round($cha / 86400, 1);
//    			$movie_top_show[$key][$movie_id]['one_day'] = round($num / $movie_top_show[$key][$movie_id]['to_day'],1); 
    			$i ++;
    			if ($i == 50) {break;}
    		}
    	}
    	
    	
    	$total['all_nums'] = count($all_movie);
    	$total['online_nums'] = count($online_movie);
    	$this->assign('total', $total);
    	$this->assign('total_tags', $total_tags);
    	
    	// 2015年8月14日19:48:14
    	foreach($movie_top_show as $key=>$val) {
    		if ($key == 'share') {
    			$rand = array(3,5,7,1,3,6,8,5,1,4,5,6,3,2,0,2,7,5,8,4,5,6,9,2,1,3,0);
    			$len = count($rand);
    			$i = 0;
    			foreach ($val as $movie_id=>$m) {
    				$i++;
    				$movie_top_show[$key][$movie_id]['share'] = $m['share'] * 10 + $rand[$i%$len];
    			}
    		}
    	}
    	
    	$this->assign('moive_max', $movie_max);
    	$this->assign('movie_top', $movie_top_show);
    	$this->assign('tag_names', array('played'=>'播放','ding'=>'喜欢','cai'=>'踩','share'=>'分享','keep'=>'收藏','poptxt_count'=>'弹幕','comment_count'=>'评论'));
    	
    	$this->display();
    }
    
    
    /**
     * 获取已经上线的电影ID列表
     * Enter description here ...
     */
    private function _getOnlineIdList(){
    	$rst = array();
    	$map = array();
    	$map['pub_platform'] = 'all';
    	$map['pub_channel'] = 'all';
    	$onlineMapPC = D('OnlineMapPC'); // 各个平台渠道上线模型
    	$list = $onlineMapPC->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			$movie_ids = explode(',', $item['online_movie']);
    			$online_times = explode(',', $item['online_movie_time']);
    			foreach ($movie_ids as $key => $val) { // 直接用 key=value 免去求交集的烦恼。
    				$rst[$val] = $online_times[$key]; // 组成movie_id => 上线时间， 这样一个数组
    			}
    		}
    	}
    	return $rst;
    } 
    
    
    /*------------- 以下记录为测试数据 ------------------*/
    
    public function activeMAlive(){
    	$s_day = I('s_day',date('Y-m-d', time() - 86400*30));
    	$e_day = I('e_day','2099-01-01');
    	$e_day = empty($e_day) ? '2099-01-01' : $e_day;
    	$p_os = I('os','');
    	$type = I('type','day');
    	$_POST['s_day'] = $s_day;
    	$_POST['type'] = $type;
    	$rst = file_get_contents('day.txt');
    	$rst = explode(PHP_EOL, $rst);
    	foreach ($rst as $key => $ll) {
    		$rst[$key] = explode(' | ', $ll);
    	}
    	
    	$rst2 = array();
    	foreach ($rst as $ll) {
    		if ($ll[0] < $s_day) continue;
    		if ($ll[0] > $e_day) break;
    		if ($p_os == 'android') {
    			$active = $ll[2];
    			$news = $ll[1];
    		} else if ($p_os == 'ios') {
    			$active = $ll[4];
    			$news = $ll[3];
    		} else {
    			$active = $ll[2]+$ll[4];
    			$news = $ll[1]+$ll[3];
    		}
    		$rst2[$ll[0]]['active'] = $active;
    		$rst2[$ll[0]]['news'] = $news;
    	}
    	$this->assign('rst', $rst2);
    	
    	
    	$p_ver = I('ver','');
    	$type = I('type','day');
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	 
    	 
    	$path = array(
    			'android' => array('3.0', '3.2', '3.3', '3.7','3.8','4.0'),
    			'ios' => array('3.0','3.4','3.8','4.0')
    	);
    	
    	$ge = ' | ';
    	 
    	$data = array();
    	foreach ($path as $os=>$item) {
    		foreach ($item as $ver) {
    			$content = file_get_contents('../../logger/other/'.$os.'/'.$ver.'/nar.txt');
    			$tmp = explode(PHP_EOL, $content);
    			foreach ($tmp as $line) {
    				if (!empty($line)) {
    					$tmp2 = explode($ge, $line);
    					if (!empty($tmp2)) {
    						$data[$os][$ver][$tmp2[0]] = array('new'=>$tmp2[1], 'active'=>$tmp2[2]);
    					}
    				}
    			}
    		}
    	}
    	 
    	$rst = array();
    	$days = array(1,2,3,4,5,7,30);
    	$rst2 = array(); // 记录留存率
    	if (!empty($data)) {
    		foreach ($data as $os=>$item) {
    			if (!empty($p_os) && $os != $p_os) {continue;}
    			foreach ($item as $ver=>$item2) {
    				if (!empty($p_ver) && $ver != $p_ver) {continue;}
    				foreach ($item2 as $day => $tmp) {
    					if ($day < $s_day || $day > $e_day) {continue;}
    					$rst[$day]['active'] = isset($rst[$day]) ? ($rst[$day]['active'] + $tmp['active']) : intval($tmp['active']);
    					$rst[$day]['news'] = isset($rst[$day]['news']) ? ($rst[$day]['news'] + $tmp['new']) : intval($tmp['new']);
    	
    					$lv_content = file_get_contents('../../logger/other/'.$os.'/'.$ver.'/'.$day.'.txt');
    					$tmp_lv = explode($ge, $lv_content);
    					unset($tmp_lv[0]);
    					$tmp_lv = array_values($tmp_lv);
    					if (isset($rst2[$day])){
    						foreach ($tmp_lv as $key=>$value) {
    							$rst2[$day]['cun'][$key] += $value;
    						}
    					} else {
    						$rst2[$day]['cun'] = $tmp_lv;
    					}
    				}
    			}
    		}
    		//$this->assign('rst',$rst);
    			
    		foreach ($rst as $day=>$item) {
    			foreach ($days as $key=>$xxd) {
    				if (strtotime($day) + (($xxd+2)*86400) > NOW_TIME) {
    					$rst2[$day][$xxd] = '-';
    				} else {
    					$rst2[$day][$xxd] = round($rst2[$day]['cun'][$key+1] / $rst2[$day]['cun'][0] * 100,1) ." %";
    				}
    			}
    		}
    		
    		$this->assign('rst2',$rst2);
    		$this->assign('max_len',10);
    		$this->assign('total_str',$p_os.' - '.$p_ver.' 终端统计分析');
    	} else {
    		$rst = '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。';
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    	
    }
    
    /*
    function activeMAlive(){
    	$s_day = I('s_day','2014-06-01');
    	$e_day = I('e_day','2099-01-01');
    	$e_day = empty($e_day) ? '2099-01-01' : $e_day;
    	$p_os = I('os','');
    	$p_ver = I('ver','');
    	$type = I('type','day');
    	$_POST['s_day'] = $s_day;
    	$_POST['type'] = $type;
    	load('@.logger');
    	$logger = new Api_Logger_Analyse();
    	
    	
		$path = array(
			'android' => array('3.0', '3.2', '3.3', '3.7','3.8','4.0'),
			'ios' => array('3.0','3.4','3.8','4.0')
		);
		
		$ge = ' | ';
    	
    	$data = array();
	    foreach ($path as $os=>$item) {
			foreach ($item as $ver) {
				$content = file_get_contents('../../logger/other/'.$os.'/'.$ver.'/nar.txt');
				$tmp = explode(PHP_EOL, $content);
				foreach ($tmp as $line) {
					if (!empty($line)) {
						$tmp2 = explode($ge, $line);
						if (!empty($tmp2)) {
							$data[$os][$ver][$tmp2[0]] = array('new'=>$tmp2[1], 'active'=>$tmp2[2]);
						}
					}
				}
			}
		}
    	
		$rst = array();
		$days = array(1,2,3,4,5,7,30);
		$rst2 = array(); // 记录留存率
    	if (!empty($data)) {
	    	foreach ($data as $os=>$item) {
				if (!empty($p_os) && $os != $p_os) {continue;}
				foreach ($item as $ver=>$item2) {
					if (!empty($p_ver) && $ver != $p_ver) {continue;}
					foreach ($item2 as $day => $tmp) {
						if ($day < $s_day || $day > $e_day) {continue;}
						$rst[$day]['active'] = isset($rst[$day]) ? ($rst[$day]['active'] + $tmp['active']) : intval($tmp['active']);
						$rst[$day]['news'] = isset($rst[$day]['news']) ? ($rst[$day]['news'] + $tmp['new']) : intval($tmp['new']);
						
						$lv_content = file_get_contents('../../logger/other/'.$os.'/'.$ver.'/'.$day.'.txt');
						$tmp_lv = explode($ge, $lv_content);
						unset($tmp_lv[0]);
						$tmp_lv = array_values($tmp_lv);
						if (isset($rst2[$day])){
							foreach ($tmp_lv as $key=>$value) {
								$rst2[$day]['cun'][$key] += $value;
							}
						} else {
							$rst2[$day]['cun'] = $tmp_lv;
						}
					}
				}
			}
			$this->assign('rst',$rst);
			
			foreach ($rst as $day=>$item) {
				foreach ($days as $key=>$xxd) {
					if (strtotime($day) + (($xxd+2)*86400) > NOW_TIME) { 
						$rst2[$day][$xxd] = '-';
					} else {
						$rst2[$day][$xxd] = round($rst2[$day]['cun'][$key+1] / $rst2[$day]['cun'][0] * 100,1) ." %";
					}
				}
			}
			
			$this->assign('rst2',$rst2);
			print_r($rst);
			$this->assign('max_len',10);
			$this->assign('total_str',$p_os.' - '.$p_ver.' 终端统计分析');
    	} else {
    		$rst = '获取失败 ！可能情况分析：1文件不存在，2 读取文件超时，3外星人降临地球。';
    		$this->assign('error_info',$rst);
    	}
    	$this->display();
    }
    //*/
}
?>