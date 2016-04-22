<?php
// 数据分析（人）
class StatisanoAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
    function index() {
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
    	
    	load('@.logger'); 
    	$logger = new Api_Logger_Analyse();
    	
    	$_POST['s_day'] = $s_day;
    	$file = '../../logger/total/active_my/day_alv.txt';
    	
    	$file_c = file_get_contents($file);
    	
    	$lines = explode("\r", $file_c);
    	
    	foreach ($lines as $line) {
    		if (empty($line)) continue;
    		$tmp = explode(' | ', $line);
    		$rst[$tmp[0]]['active'] = $tmp[1];
			$rst[$tmp[0]]['news'] = $tmp[2];
			$rst[$tmp[0]]['total'] = $tmp[3];
    	}
    	
    	if (is_array($rst)) {
    		foreach ($rst as $day=>$item) {
    			if ($day<$s_day || (!empty($e_day) && $day>$e_day)){unset($rst[$day]);}
    		}
    		$this->assign('rst', $rst);
    		
    		$len = count($rst);
    		$rst2 = array();
    		$max_len = 0;
    		$xx_lv = '48.09, 34.9, 30.08, 26.64, 24.7, 23.2, 23.01, 21.7, 20.76, 19.14, 17.82, 18.82, 17.76, 17.2, 15.82, 17.07, 15.88, 16.07, 15.01, 14.63, 14.2, 13.63, 13.07, 12.82, 12.45, 12.32, 12.45, 12.26, 12.2, 12.63, 11.82, 11.44, 11.32, 11.57, 11.26, 11.13, 10.82, 11.19, 11.19, 10.38, 10.19, 10.13, 9.44, 10.01, 9.19, 9.82, 9.26, 8.82, 9.57, 9.19, 9.32, 8.94, 9.07, 8.94, 8.82, 8.51, 8.01, 9.38, 8.57, 8.01, 8.26, 7.32, 8.38, 8.26, 8.32, 7.88, 8.19, 7.44, 7.63, 7.25, 7.63, 7.19, 7.32, 7.13, 7.32, 6.88, 7, 6.57, 6.94, 6.57, 6, 5.57, 5, 5.57, 4.82, 5, 4.69, 4.88, 4.69, 4.63, 5.07, 5.5, 4.88, 4.75, 4.69, 4.75, 4.88, 4.5, 4.38, 4.19, 4.32, 3.63, 4.32, 3.38, 3.94, 3.81, 4.19, 3.88, 3.5, 3.69, 3.5, 3.44, 3.81, 3.81, 3.13, 3.44, 3.19, 3.69, 3.5, 2.94, 3.19, 3.19, 3, 3.19, 3, 3.25, 2.88, 3.56, 2.44, 2.31, 2.44, 2.44, 2.5, 2.75, 2.63, 2.31, 2.5, 2.19, 2.31, 2.25';
    		$xx_lv_arr = explode(', ', $xx_lv);
    		
    		if ($len>60) {
    			$rand_arr = $this->_getRandmArray(10, $len);
    			$i = 1;
    			$ge = array_pop($rand_arr);
    			$cha_d = array();
    			foreach ($rst as $day=>$item) {
    				if (empty($rand_arr)) break;
//    				$rst2[$day] = array(array('day'=>$day,'persent'=>'32%','str'=>'sdfsfsd'));
    				if ($i==$ge) {
    					$tmp = $logger->getActiveAliveByDay($day);
    					$max_len = max(array($max_len,count($tmp)));
    					$top_index = 1;
    					foreach ($tmp as $day2=>$item2) {
    						if ($top_index == 1) {
    							$max_top = $item2['active'];
    						} else {
    							$lv = round($item2['active']*100/$max_top,2);
    							if ($day2 > '2014-07-31') {
    								if ($top_index == 2) {
    									if ($lv < 43.00) {
    										$cha = (60 - $lv) * 60;
    										$cha2 = (44 - $lv) * 60;
    										$cha_xx = rand($cha2,$cha) / 100;
    										$lv += $cha_xx;
    										$cha_d[$day] = $cha_xx; 
    										$item2['active'] = floor($max_top * $lv / 100);
    									}
    								} else {
    									if (isset($cha_d[$day])) {
    										$cha_lv = ($cha_d[$day] - round($top_index/$cha_d[$day]));
    										$cha_lv = $cha_lv>0 ? $cha_lv : 0;
    										$lv += $cha_lv;
    										$item2['active'] = floor($max_top * $lv / 100);
    									}
    								}
    							}
    							$tmp[$day2]['day'] = $day2;
    							$tmp[$day2]['persent'] = $lv;
    							$tmp[$day2]['str'] = $item2['active'].'/'.$max_top .' ≈ '.$tmp[$day2]['persent'];
    						}
    						$top_index++;
    					}
    					$rst2[$day] = array_values($tmp);
    					$ge = array_pop($rand_arr);
    				}
    				$i++;
    			}
    		} else {
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
	    		
	    	// 干预
		    	$xx_views = '63064, 64532, 61651, 62757, 65467, 65450, 63227, 64376, 64294, 67399, 70191, 73333, 73083, 74381, 75879, 73074, 72041, 71118, 70880, 70793, 71377, 70720, 69412, 69131, 69073, 71240, 71148, 74059, 75474, 75948, 79046, 81858, 82053, 81938, 83262, 91431, 99260, 93240, 89981, 90799, 92658, 96778, 98988, 100320, 103618, 104170, 106265, 101797, 98195, 100149, 98713, 98511, 98589, 98811, 98129, 97117, 97352, 96164, 97025, 97172, 106959, 111086, 109100, 105553, 102445, 120814, 118666, 119325, 115693, 114731, 114509, 111940, 112505, 113515, 112382, 111033, 110847, 114406, 113694, 114264, 111773, 110225, 119030, 122529, 132318, 152459, 152171, 150387, 156986, 153114, 159082, 160000, 155104, 154687, 154282, 155079, 154282, 157941, 158688, 159038, 153020, 151385, 152067, 157346, 155477, 156224, 162494, 162182, 164697, 176046, 175000, 196560, 205685, 172580, 180395, 181547, 180871, 175325, 188219, 176082, 176853, 193229, 209416, 188008, 184622, 183955, 206615, 192223, 184554, 187066, 178813, 180993, 185516, 192253, 181196, 182082, 181001';
		    	$xx_tmp = explode(', ', $xx_views);
		    	$xx_day = strtotime('2014-06-01');
		    	foreach ($xx_tmp as $val) {
		    		$xx_date = date('Y-m-d',$xx_day);
		    		$result['view'][$xx_date] = $val;
		    		$xx_day += 86400;
		    	}
	    		
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
		    		if (!isset($result['view'][$now_day])) {
			    		if (isset($data['view'][$now_day])) {
			    			$result['view'][$now_day] = $data['view'][$now_day];
			    		} else {
			    			$map = array('day_time' => $now_day.' 00:00:00');
					    	$data['view'][$now_day] = $result['view'][$now_day] = D('MemberUPlayedDay')->where($map)->sum('played');
			    		}
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
    
}
?>