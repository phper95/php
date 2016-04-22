<?php
// 平台渠道管理模块
class PlatChannelAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
    
    /**
     * 针对渠道上线电影管理
     * Enter description here ...
     */
    public function offlineMovie(){
    	$platform = I('platform');
    	$channel = I('channel');
    	if (empty($platform) || empty($channel)) {
    		$this->error('参数错误');
    	}
    	
    	$map = array('pub_platform'=>$platform, 'pub_channel'=>$channel);
    	$online_movie = D('OnlineMapPC')->where($map)->field('id,online_movie,await_movie,online_movie_time,await_movie_time')->select();
    	if (empty($online_movie)) {
    		$this->error('该平台渠道下，木油上线电影');
    	} else if (count($online_movie) > 1) {
    		$this->error('该平台渠道下，数据库出错咧。');
    	} else {
    		$movie_list = D('Movie')->getField('id,name,tags');
    		$movie_ids = explode(',', implode(',', array($online_movie[0]['await_movie'],$online_movie[0]['online_movie'])));
    		$online_times = explode(',', implode(',', array($online_movie[0]['await_movie_time'],$online_movie[0]['online_movie_time'])));
    		if (empty($movie_ids)){
    			$this->error('该平台渠道下，木油上线电影2');
    		}
    		foreach ($movie_ids as $key=>$id) {
    			if (empty($id))continue;
    			$movies[$id] = array('name'=>$movie_list[$id]['name'],'tags'=>$movie_list[$id]['tags'],'online_time'=>$online_times[$key]);
    		}
    		$this->assign('movies', $movies);
    	}
    	$this->display();
    }
    
    public function doOfflineMovie(){
    	$platform = I('platform');
    	$channel = I('channel');
    	$off_movie_ids = explode(',', I('movie_ids'));
    	if (empty($platform) || empty($channel) || empty($off_movie_ids)) {
    		$this->error('参数错误');
    	}
    	
    	$map = array('pub_platform'=>$platform, 'pub_channel'=>$channel);
    	$onlineMapPC = D('OnlineMapPC');
    	$online_movie = $onlineMapPC->where($map)->field('id,online_movie,await_movie,online_movie_time,await_movie_time')->select();
    	if (empty($online_movie)) {
    		$this->error('该平台渠道下，木油上线电影');
    	} else if (count($online_movie) > 1) {
    		$this->error('该平台渠道下，数据库出错咧。');
    	} else {
    		$online_movie_ids = empty($online_movie[0]['online_movie']) ? array () : explode(',', $online_movie[0]['online_movie']);
    		$online_times = empty($online_movie[0]['online_movie_time']) ? array() : explode(',', $online_movie[0]['online_movie_time']);
    		$await_movie_ids = empty($online_movie[0]['await_movie']) ? array() : explode(',', $online_movie[0]['await_movie']);
    		$await_times = empty($online_movie[0]['await_movie_time']) ? array() : explode(',', $online_movie[0]['await_movie_time']);
    		foreach ($off_movie_ids as $id) {
    			$find = false;
    			if (!empty($online_movie_ids)) { // 删除上线列表
    				foreach ($online_movie_ids as $key=>$val) {
    					if ($id==$val) {
    						unset($online_movie_ids[$key], $online_times[$key]);
    						$find = true; 
    						break;
    					}
    				}
    				if ($find) continue;
    			}
    			if (!empty($await_movie_ids)) { // 删除等待列表
    				foreach ($await_movie_ids as $key=>$val) {
    					if ($id==$val) {
    						unset($await_movie_ids[$key], $await_times[$key]);
    						break;
    					}
    				}
    			}
    		}
    		
	    	$data = array(
	    		'id' => $online_movie[0]['id'],
	    		'online_movie' => implode(',',$online_movie_ids), 'await_movie' => implode(',',$await_movie_ids),
	    		'online_movie_time' => implode(',', $online_times), 'await_movie_time'=> implode(',', $await_times),
	    		'online_movie_count' => count($online_movie_ids), 'await_movie_count' => count($await_movie_ids)
	    	);
	    	$onlineMapPC->create($data);
	    	if (false === $onlineMapPC->save()) {
	    		$this->error('更新渠道上线表出错');
	    	}else {
//	    		$this->display('index');
	    		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            	$this->success('更新成功!');
	    	}
    	}
    }
    
    
	public function index() {
		$this->display();
    }
    
    public function PlatformList() {
    	$platform = D('Platform');
    	$this->_list($platform);
    	$this->display();
    }
    
    public function ChannelList() {
    	
//    	$platform = D('Channel');
//    	$this->_list($platform,array());

    	$model = D('PlatformVChannel');
//    	$map = array('open'=>1);
    	$map = array();
    	$this->_list($model,array());
    	
    	$platform = D('Platform')->where($map)->getField('id,name,open');
    	$channel = D('Channel')->where($map)->getField('id,name,open');
    	
    	$this->assign('platform', $platform);
    	$this->assign('channel', $channel);
    	$this->display();
    }
    
    public function addChannel(){
    	$model = D('PlatformVChannel');
    	$map = array('open'=>1);
    	$list = $model->where($map)->getField('id,platform_id,channel_id');
    	$channelList = D('Channel')->where($map)->getField('id,name');
    	$platformList = D('Platform')->where($map)->getField('id,name');
    	$rst = array();
    	foreach ($list as $val) {
    		$rst[] = array('platform'=>$platformList[$val['platform_id']], 'channel'=>$channelList[$val['channel_id']]);
    	}
    	$this->assign('platform', $platformList);
    	$this->assign('list', $rst);
    	$this->display();
    }
    
    public function doAddChannel(){
    	$name = strtolower(I('name'));
    	$platform = I('platform');
    	$copy = I('copy');
    	if (empty($name) || empty($platform) || empty($copy)) {
    		$this->error('参数错误');
    	}
//    	if(!preg_match("/^[a-z]*$/i",$name)) {
//    		$this->error('名字只能输入小写字母啊！');
//    	}
    	
        $platform_name = D('Platform')->where(array('id'=>$platform))->getField('name');
        if (empty($platform_name)) {
        	$this->error('平台不存在');
        }
    	
    	$data = array(
    		'name' => $name,
    		'open' => 1
    	);
    	$model = D('Channel');
    	$exits_id = $model->where(array('name'=>$name))->getField('id');
    	if (!empty($exits_id)) {
    		$chanel_id = $exits_id;
    		$model = D('PlatformVChannel');
    	} else {
	    	if (false === $model->create($data)) {
	            $this->error($model->getError());
	        }
	        $chanel_id = $model->add();
	        if (empty($chanel_id)) {
	        	$this->error('插入渠道错误');
	        }
    	}
        
        $data = array(
        	'channel_id' => $chanel_id,
        	'platform_id' => $platform,
        	'open' => 1
        );
        
        $model = D('PlatformVChannel'); 
        if (false === $model->add($data)) {
        	$this->error('插入平台渠道关系错误');
        }
        
        // 跟新上线电影表
        $tmp = explode(',', $copy);
        $copy_platform = $tmp[0];
        $copy_channel = $tmp[1];
        $map = array('pub_platform'=>$copy_platform, 'pub_channel'=>$copy_channel);
        
        $online_movie = D('OnlineMapPC');
        $data = $online_movie->where($map)->find();
        if (empty($data)) {
        	$this->error('选择的Copy平台渠道的电影数据为空');
        }
        
        unset($data['id']);
        $data['update_time'] = $data['add_time'] = date('Y-m-d H:i:s', NOW_TIME);
        $data['pub_platform'] = $platform_name;
        $data['pub_channel'] = $name;
        if (false === $online_movie->add($data)) {
        	$this->error('Copy 上线电影表的时候出错');
        }
        
        // 跟新上线广告表
        $online_adv = D('AdvOnlineMap');
        $data = $online_adv->where($map)->find();
        if (empty($data)) {
        	$this->error('选择的Copy平台渠道的广告数据为空');
        }
        
        unset($data['id']);
        $data['update_time'] = $data['add_time'] = date('Y-m-d H:i:s', NOW_TIME);
        $data['pub_platform'] = $platform_name;
        $data['pub_channel'] = $name;
        if (false === $online_adv->add($data)) {
        	$this->error('Copy 上线广告表的时候出错');
        }
        
        
        // 跟新上线专辑表
        $online_topic = D('OnlineMapTopic');
        $data = $online_topic->where($map)->find();
        if (empty($data)) {
        	$this->error('选择的Copy平台渠道的专辑列表为空');
        }
    	unset($data['id']);
        $data['update_time'] = $data['add_time'] = date('Y-m-d H:i:s', NOW_TIME);
        $data['pub_platform'] = $platform_name;
        $data['pub_channel'] = $name;
        if (false === $online_topic->add($data)) {
        	$this->error('Copy 上线专辑的时候出错');
        }
        
        // 更新区域广告
        $online_adv2 = D('Adv2OnlineMap');
        $data = $online_adv2->where($map)->find();
        if (!empty($data)) {
	    	unset($data['id']);
	        $data['update_time'] = $data['add_time'] = date('Y-m-d H:i:s', NOW_TIME);
	        $data['pub_platform'] = $platform_name;
	        $data['pub_channel'] = $name;
	        if (false === $online_adv2->add($data)) {
	        	$this->error('Copy 上线区域广告错误');
	        }
        }
        
        // 更新首页配置
    	$online_home = D('HomeOnlineMap');
        $list = $online_home->where($map)->select();
        if (!empty($list)) {
        	$dataList = array();
        	foreach ($list as $data) {
        		unset($data['id']);
        		$data['update_time'] = $data['add_time'] = date('Y-m-d H:i:s', NOW_TIME);
		        $data['pub_platform'] = $platform_name;
		        $data['pub_channel'] = $name;
		        $dataList[] = $data;
        	}
	        if (false === $online_home->addAll($dataList)) {
	        	$this->error('Copy 上线首页配置错误');
	        }
        }
        
        $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
        $this->success('添加成功!');
    }
}
?>