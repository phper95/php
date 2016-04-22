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
    	foreach ($online_movie as $id=>$time) {
    		$tags = explode('|', $all_movie[$id]['tags']);
    		foreach ($total as $key=>$t) {
    			$num = $all_movie[$id][$key];
    			if ($num == 0) continue;
    			$total[$key] += $num;
    			foreach ($tags as $tag) {
    				$total_tags[$tag][$key] += $num;
    			}
    			$movie_max[$key] = isset($movie_max[$key][$key]) ? ($movie_max[$key][$key]<$num ? $all_movie[$id] : $movie_max[$key]) : $all_movie[$id];
    		}
    	}
    	
    	$total['all_nums'] = count($all_movie);
    	$total['online_nums'] = count($online_movie);
    	$this->assign('total', $total);
    	$this->assign('total_tags', $total_tags);
    	$this->assign('moive_max', $movie_max);
    	
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
}
?>