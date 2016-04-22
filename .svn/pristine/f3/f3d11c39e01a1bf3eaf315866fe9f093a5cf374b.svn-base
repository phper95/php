<?php
// 首页上线表
class HomeOnlineAction extends CommonAction {
	
	function index(){
		$this->display();
	}
	
	function edit(){
		$id = I('id');
		$model = D('HomeOnlineConf');
		$vo = $model->where(array('id'=>$id))->find();
		if (empty($vo)) {$this->error('啊呀，ID找不到了呢');}
		$vo['ids'] = explode(',', $vo['online_id']);
		$vo['pic_a'] = explode(';', $vo['pics']);
		$vo['intro_a'] = explode(';', $vo['intros']);
		$map = array('id'=>array('in',$vo['ids']));
		if($vo['online_type']<=2) {
			$model = D('Movie');
			$names = $model->where($map)->getField('id,name');
		} else {
			$model = D('Wei');
			$names = $model->where($map)->getField('id,title as name');
		}
		$this->assign('names',$names);
		$this->assign('vo',$vo);
		$this->assign('typeList',array('1'=>'电影','2'=>'新番','3'=>'微图解'));
	}
	
	/**
	 * 
	 */
	function onlineMovie(){
		$model = D('HomeOnlineConf');
		$map = array('online_type'=>1);
		$this->_list($model, $map);
		$list = $this->get('list');
		foreach ($list as $key=>$item) {
			$list[$key]['pic_a'] = explode(';', $item['pics']);
			$map = array('config_id'=>$item['id'],'pub_channel'=>'xiaomi','pub_platform'=>'android');
			$list[$key]['online_time'] = D('HomeOnlineMap')->where($map)->getField('online_time');
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	 * 上线的新番配置 
	 */
	function onlineFan(){
		$model = D('HomeOnlineConf');
		$map = array('online_type'=>2);
		$this->_list($model, $map);
		$list = $this->get('list');
		foreach ($list as $key=>$item) {
			$list[$key]['pic_a'] = explode(';', $item['pics']);
			$map = array('config_id'=>$item['id'],'pub_channel'=>'xiaomi','pub_platform'=>'android');
			$list[$key]['online_time'] = D('HomeOnlineMap')->where($map)->getField('online_time');
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	 * 上线的微图解配置 
	 */
	function onlineWei(){
		$model = D('HomeOnlineConf');
		$map = array('online_type'=>3);
		$this->_list($model, $map);
		$list = $this->get('list');
		foreach ($list as $key=>$item) {
			$list[$key]['pic_a'] = explode(';', $item['pics']);
			$map = array('config_id'=>$item['id'],'pub_channel'=>'xiaomi','pub_platform'=>'android');
			$list[$key]['online_time'] = D('HomeOnlineMap')->where($map)->getField('online_time');
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	function addMovieConf(){
		$this->display();
	}
	
	function doAddMovieConf(){
		$ids = I('id');
		$pics = I('up_img_v');
		$intros = I('sub_title');
		if (empty($ids) || !is_array($ids)) {
			$this->error('电影ID为空');
		}
		foreach ($ids as $key=>$id) {
			if (empty($id) || empty($pics[$key]) || empty($intros[$key])) {
				$this->error($key.'卧槽，你资料没填写完全呐!');
			}
			$this->_uploadImg($id,$key);
			$intros[$key] = str_replace(';', '；', $intros[$key]);
		}
		$pics = I('up_img_v');
		$data = array(
			'online_id' => implode(',', $ids),
			'online_type' => 1,
			'pics' => implode(';', $pics),
			'intros' => implode(';', $intros)
		);
		$model = D('HomeOnlineConf');
		if (FALSE === $model->create($data)) {
			$this->error($model->getError());
		} else {
			if (false === $model->add()) {
				$this->error($model->getError());
			}
		}
		$this->success('添加成功！',getCurrentUrl());
	}
	
	function addFanConf(){
		$this->display();
	}
	
	function doAddFanConf(){
		$ids = I('id');
		$pics = I('up_img_v');
		$intros = I('sub_title');
		if (empty($ids) || !is_array($ids)) {
			$this->error('电影ID为空');
		}
		foreach ($ids as $key=>$id) {
			if (empty($id) || empty($pics[$key]) || empty($intros[$key])) {
				$this->error($key.'卧槽，你资料没填写完全呐!');
			}
			$this->_uploadImg($id,$key);
			$intros[$key] = str_replace(';', '；', $intros[$key]);
		}
		$pics = I('up_img_v');
		$data = array(
			'online_id' => implode(',', $ids),
			'online_type' => 2,
			'pics' => implode(';', $pics),
			'intros' => implode(';', $intros)
		);
		$model = D('HomeOnlineConf');
		if (FALSE === $model->create($data)) {
			$this->error($model->getError());
		} else {
			if (false === $model->add()) {
				$this->error($model->getError());
			}
		}
		$this->success('添加成功！',getCurrentUrl());
	}
	
	function addWeiConf(){
		$this->display();
	}
	
	function doAddWeiConf(){
		$ids = I('id');
		$pics = I('up_img_v');
		if (empty($ids) || !is_array($ids)) {
			$this->error('电影ID为空');
		}
		foreach ($ids as $key=>$id) {
			if (empty($id) || empty($pics[$key])) {
				$this->error($key.'卧槽，你资料没填写完全呐!');
			}
			$this->_uploadImg($id,$key,'wei');
		}
		$pics = I('up_img_v');
		$data = array(
			'online_id' => implode(',', $ids),
			'online_type' => 3,
			'pics' => implode(';', $pics),
			'intros' => ''
		);
		$model = D('HomeOnlineConf');
		if (FALSE === $model->create($data)) {
			$this->error($model->getError());
		} else {
			if (false === $model->add()) {
				$this->error($model->getError());
			}
		}
		$this->success('添加成功！',getCurrentUrl());
	}
	
	/**
	 * 获取电影，广告，专辑，资讯，用户信息
	 * Enter description here ...
	 */
	public function getOneInfo(){
		$type_arr = array(
			'movie'=>array('Movie','id,name,bpic,spic,sub_title,tags,played,ding,comment_count,grapher,mark,name_tag,vol_count'),
			'wei'=>array('Wei','id,title,pic,user_id,intro,played'),
			'paper' => array('News','id,name,bpic,grapher,sub_title,played'),
			'adv' => array('Adv2','id,name,pic'),
			'topic' => array('Topic','id,name,spic,bpic,sub_title,editor_note,name_tag'),
			'image' => array('DayPic','id,name,image_url')
		);
		$id = I('id');
		$type = I('type');
		$rst = array('rst'=>0,'msg'=>'');
		if (empty($id) || !is_numeric($id) || empty($type) || !in_array($type, array_keys($type_arr))) {
			$rst['msg'] = '参数错误';
			$this->ajaxReturn($rst); return;
		}
		$map = array('id'=>$id);
		$one = D($type_arr[$type][0])->where($map)->field($type_arr[$type][1])->find();
		if (empty($one)) {
			$rst['msg'] = 'ID不存在';
			$this->ajaxReturn($rst); return;
		} else {
			if ($type == 'movie') {
				$one['bpic'] = getImgServerURL(8).'/graphmovie/'.$one['bpic'];
				$one['spic'] = getImgServerURL(8).'/graphmovie/'.$one['spic'];
				$tmp = explode(',', $one['grapher']);
				$member_id = $tmp[0];
				
				// 获取上线时间数据
				$onlineMapPC = D('OnlineMapPC');
				$map = array('pub_platform'=>'android','pub_channel'=>'xiaomi');
				$online_one = $onlineMapPC->where($map)->field('online_movie,online_movie_time,await_movie,await_movie_time')->find();
				if (!empty($online_one)) {
					$tmp_online_movie = explode(',', $online_one['online_movie']);
					$tmp_online_time = explode(',', $online_one['online_movie_time']);
					$tmp_await_movie = explode(',', $online_one['await_movie']);
					$tmp_await_time = explode(',', $online_one['await_movie_time']);
					foreach ($tmp_online_movie as $key=>$m_id) {
						$online_movie[$m_id] = $tmp_online_time[$key];
					}
					foreach ($tmp_await_movie as $key=>$m_id) {
						$online_movie[$m_id] = $tmp_await_time[$key];
					}
					
					if (empty($online_movie[$one['id']])) {
						$rst['msg'] = '该图解还没有上线啊 啊啊啊 啊啊啊 啊啊啊啊 啊啊啊啊啊啊';
						$this->ajaxReturn($rst);
					}
					$one['online_time'] = $online_movie[$one['id']];
				}
				
			} else if ($type == 'wei') {
				$member_id = $one['user_id'];
			} else if ($type == 'paper') {
				$member_id = $one['grapher'];
			}
			if (!empty($member_id)) {
				$map = array('id'=>$member_id);
				$member = D('Member')->where($map)->field('name,avatar')->find();
				if (empty($member)) {
					$rst['msg'] = '获取作者信息出错';
					$this->ajaxReturn($rst); return;
				}
				$one['avatar'] = $member['avatar'];
				$one['grapher_name'] = $member['name'];
			}
			
			$rst['rst'] = 1;
			$rst['data'] = $one;
			$this->ajaxReturn($rst); return;
		}
	}
	
	
	public function online(){
		$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$hOConf = D('HomeOnlineConf')->getById($id);
    	if (empty($hOConf)){
    		$this->error('参数错误');
    	}
    	$type = I('type');
    	if (empty($type)) {
    		$this->error('参数错误');
    	}
    	
    	$platform = D('Platform');
		$map = array('open' => 1);
		$platformList = $platform->where($map)->getField('name,id');
		
		$channel = D('Channel');
		$channelList = $channel->where($map)->getField('name,id');
		
		$plVch = D('PlatformVChannel');
		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
		
		
		$onlineMapPC = D('HomeOnlineMap');
		$map = array ( 
			'config_id' => $id,
			'online_type' => $type
		);
		$list = $onlineMapPC->where($map)->select();
		
		$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
		if (!empty($list)) {
			foreach ( $list as $val ) {
				if (!isset($onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]])) {
					$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $val['online_time']);
					$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
				}
			}
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
    	$conf_id = I('id');
    	if (empty($conf_id)) {$this->error('参数错误');}
    	
    	$online_options = array();
    	foreach ($online_list as $val) {
    		$time = I($val.',time','');
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1],
    			'time' => $time
    		);
    	}
    	
    	$homeOnlineConf = D('HomeOnlineConf')->getById($conf_id);
    	if (empty($homeOnlineConf)) {
    		$this->error('擦嘞，配置被删了');
    	}
    	
    	$ids = explode(',', $homeOnlineConf['online_id']);
    	$pics = explode(';', $homeOnlineConf['pics']);
    	$intros = explode(';', $homeOnlineConf['intros']);
    	$online_type = $homeOnlineConf['online_type'];
    	$dataList = array();
    	$add_time = toDate(NOW_TIME);
    	
    	$onlineMap = D('HomeOnlineMap');
    	$map = array ( 
			'config_id' => $conf_id,
			'online_type' => $online_type
		);
		
		$online_p_c = array();
		$list = $onlineMap->where($map)->select();
		if (!empty($list)) {
			foreach ($list as $item) {
				$online_p_c[$item['pub_platform'].'-'.$item['pub_channel']] = true;
			}
		}
    	
    	foreach($online_options as $option) {
    		$exists_key = $option['pub_platform'].'-'.$option['pub_channel'];
    		if (isset($online_p_c[$exists_key])) {
    			$this->error($exists_key.' 渠道已经上线过了，或者数据出错了，你要联系Bobo哟！');
    		}
    		foreach ($ids as $key=>$id) {
    			$dataList[] = array(
    				'pub_platform' => $option['pub_platform'],
    				'pub_channel' => $option['pub_channel'],
    				'online_id' => $id,
    				'online_type' => $online_type,
    				'pic' => $pics[$key],
    				'intro' => $intros[$key],
    				'online_time' => !empty($option['time']) ? ($option['time'].":00") : $add_time,
    				'config_id' => $conf_id,
    				'add_time' => $add_time
    			);
    		}
    	}
    	
    	// 	把id，type 相同的记录OPen置为0
    	$data = array('open'=>0);
    	$map = array('online_id'=>array('in',$ids),'online_type'=>$online_type);
    	if (false === $onlineMap->where($map)->save($data)){
    		$this->error('上线成功，不过更新上线列表失败');
    	} else {
	    	if (false === $onlineMap->addAll($dataList)) {
	    		$this->error("插入数据库失败呢！");
	    	} else {
		    	$this->success('上线成功');
	    	}
    	}
    }
    
    
	/**
     * 实实在在的下线操作
     * Enter description here ...
     */
    public function doOffline(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$conf_id = I('id');
    	if (empty($conf_id)) {$this->error('参数错误');}
    	
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    			'pub_platform' => $tmp[0],
    			'pub_channel' => $tmp[1]
    		);
    	}
    	
    	$homeOnlineConf = D('HomeOnlineConf')->getById($conf_id);
    	if (empty($homeOnlineConf)) {
    		$this->error('擦嘞，配置被删了');
    	}
    	$ids = explode(',', $homeOnlineConf['online_id']);
    	$online_type = $homeOnlineConf['online_type'];
    	
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	
    	$onlineMap = D('HomeOnlineMap');
    	$offline_x = array(); // 平台-渠道-下线数据ID 用于最近一条上线ID 修改open为1
    	foreach ($offline_options as $option) {
    		$map = array(
    			'pub_platform' => $option['pub_platform'],
    			'pub_channel' => $option['pub_channel'],
    			'config_id' => $conf_id
    		);
    		if (false === $onlineMap->where($map)->delete()) {
    			$this->error('在下线' .$option['pub_platform'].'-'.$option['pub_channel'].' 渠道，失败鸟啊。。。。');
    		}
    		
    		foreach ($ids as $id) {
    			$offline_x[$option['pub_platform'].'-'.$option['pub_channel'].'-'.$id] = 1;
    		}
    	}
    	
    	// 	2014年12月29日17:56:28 把id，type 相同的记录OPen置为1
    	$map = array('online_id'=>array('in',$ids),'online_type'=>$online_type);
    	// 根据Online_time 降序
    	$list = $onlineMap->where($map)->field('id,pub_platform,pub_channel,online_id,online_type')->order('online_time desc')->select();
    	if(!empty($list)) {
    		foreach ($list as $item) {
    			$key = $item['pub_platform'].'-'.$item['pub_channel'].'-'.$item['online_id'];
    			if (isset ($offline_x[$key]) && $offline_x[$key]==1) {
    				$offline_x[$key] = 0;
    				$data = array('open'=>1,'id'=>$item['id']);
    				if (false === $onlineMap->save($data)){
    					$this->error('下线成功，不过在更新列表时失败了');
    				}
    			}
    		}
    	}
    	$this->success('下线成功');
    }
    
	/**
     * 上传封面图片
     * Enter description here ...
     * @param unknown_type $id
     */
	private function _uploadImg($id = 0,$index, $type= 'movies'){
    	if (!empty($_FILES)) {
    		$file_key = 'up_img_'.$index;
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../'.$type.'/'.$id;
          
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
            if (!empty($_FILES[$file_key]) && $_FILES[$file_key]['error'] != 4) { 
            	$upload->saveRule = $saveName."h";
            	$fileInfo = $upload->uploadOne($_FILES[$file_key]);
            	if ($fileInfo) {
            		$_POST [$file_key] = $type.'/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>$index,'file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (empty($files)) {
            } else {
            	$rst = sendFileToImgSevr($type,$id,$files);
            	if (is_array($rst)) {
            		foreach ($files as $file) {
            			if (isset($rst['succ'][$file['key']])) {
            				$_POST['up_img_v'][$file['key']] = $rst['succ'][$file['key']]['url'];
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
    
    
    
    public function onlineScript(){
    	$this->_list(D('HomeOnline4'), array());
    	$map = array('pub_channel'=>'xiaomi', 'pub_platform'=>'android');
    	$list = $this->get('list');
    	if (!empty($list)) {
    		foreach ($list as $key=>$item) {
    			$map['online_id'] = $item['id'];
    			$list[$key]['online_time'] = D('HomeOnlineMap4')->where($map)->getField('online_time'); 
    		}
    	}
    	$this->assign('list',$list);
    	$this->display();
    }
    
    public function doEditScript(){
    $rst = array('rst'=>0);
    	$data = $this->_getScriptdata();
    	if (!is_array($data)) {
    		$rst['msg'] = $data;
    		$this->ajaxReturn($rst);
    	}
    	$data['id'] = I('id');
    	$model = D('HomeOnline4');
    	$homeOnline4 = $model->getById($data['id']);
    	if (false === $model->create($data) || false === $model->save()) {
    		$rst['msg'] = $model->getError();
    		$this->ajaxReturn($rst);
    	}
    	
    	$one = $model->find($data['id']);
    	if ($homeOnline4['script'] != $one['script']) {
    		$rst['msg'] = $this->_updateOnlineMap($one);
    	} else {
    		$rst['msg'] = '';
    	}
    	
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    public function editScript(){
    	$id = I('id');
    	$type_list = array(
    		0=>'title',
    		1=>'movie',
    		2=>'wei',
    		3=>'paper',
    		4=>'adv',
    		5=>'topic',
    		6=>'image'
    	);
    	$vo = D('HomeOnline4')->getById($id);
    	if (empty($vo)) {
    		$this->error('找不到记录哟！请刷新一下，嗯');
    	}
    	$vo['s'] = json_decode($vo['script'],true);
    	$adv2 = D('Adv2');
    	foreach ($vo['s'] as $key=>$tmp) {
    		if ($tmp['type'] == 4) { // 广告
   				$vo['s'][$key]['image'] = $adv2->where(array('id'=>$tmp['adv2_id']))->getField('pic');
    		}
    		$tmp['type'] = $type_list[$tmp['type']];
    		$vo['s'][$key]['item'] = json_encode($tmp);
    	}
    	$this->assign('vo',$vo);
    	$this->assign('type_list',$type_list);
    	$this->display();
    }
    
    public function doAddScript(){
    	$rst = array('rst'=>0);
    	$data = $this->_getScriptdata();
    	if (!is_array($data)) {
    		$rst['msg'] = $data;
    		$this->ajaxReturn($rst);
    	}
    	
    	$model = D('HomeOnline4');
    	if (false === $model->create($data) || false === $model->add()) {
    		$rst['msg'] = $model->getError();
    		$this->ajaxReturn($rst);
    	}
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    public function _getScriptdata (){
    	$magic_flag = get_magic_quotes_gpc(); // 获取PHP是否自动转义斜杠等
    	$item_list = I('item');
    	 
    	if (empty($item_list) || !is_array($item_list)) {
    		return '参数错误';
    	}
    	$type_list = array(
    			'title' => 0,
    			'movie' => 1,
    			'wei' => 2,
    			'paper' => 3,
    			'adv' => 4,
    			'topic' => 5,
    			'image' => 6
    	);
    	$data_list = array();
    	foreach ($item_list as $item) {
    		$item = $magic_flag ? stripslashes(htmlspecialchars_decode($item)) : htmlspecialchars_decode($item);
    		$item = json_decode($item, true);
    		if (!isset($item['type']) || !isset($type_list[$item['type']])) {
    			return '解析错误';
    		}
    		$item['type'] = $type_list[$item['type']];
    		$data_list[] = $item;
    	}
    	$data = array('name'=>I('name'),'remark'=>I('remark'),'plan_time'=>I('plan_time'));
    	$data['script'] = $magic_flag ? addslashes(json_encode($data_list)) : json_encode($data_list);
    	return $data;
    }
    
    /*
     * 脚本上线
     */
    public function online4(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$hOConf = D('HomeOnline4')->getById($id);
    	if (empty($hOConf)){
    		$this->error('参数错误');
    	}
    	 
    	$platform = D('Platform');
    	$map = array('open' => 1);
    	$platformList = $platform->where($map)->getField('name,id');
    	
    	$channel = D('Channel');
    	$channelList = $channel->where($map)->getField('name,id');
    	
    	$plVch = D('PlatformVChannel');
    	$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
    	
    	
    	$onlineMapPC = D('HomeOnlineMap4');
    	$map = array (
    			'online_id' => $id
    	);
    	$list = $onlineMapPC->where($map)->select();
    	
    	$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
    	if (!empty($list)) {
    		foreach ( $list as $val ) {
    			if (!isset($onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]])) {
    				$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $val['online_time']);
    				$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
    			}
    		}
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
    function doOnline4(){
    	$online_list = I('online');
    	if (empty($online_list)) {$this->error('选择要上线的平台渠道啊');}
    	$online_id = I('id');
    	if (empty($online_id)) {$this->error('参数错误');}
    	 
    	$online_options = array();
    	foreach ($online_list as $val) {
    		$time = I($val.',time','');
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1],
    				'time' => $time
    		);
    	}
    	 
    	$homeOnline4 = D('HomeOnline4')->getById($online_id);
    	if (empty($homeOnline4)) {
    		$this->error('擦嘞，配置被删了');
    	}
    	 
    	$onlineMap4 = D('HomeOnlineMap4');
    	$map = array (
    			'online_id' => $online_id
    	);
    
    	$online_p_c = array();
    	$list = $onlineMap4->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			$online_p_c[$item['pub_platform'].'-'.$item['pub_channel']] = true;
    		}
    	}
    	
    	$add_time = toDate(NOW_TIME);
    	foreach($online_options as $option) {
    		$exists_key = $option['pub_platform'].'-'.$option['pub_channel'];
    		if (isset($online_p_c[$exists_key])) {
    			$this->error($exists_key.' 渠道已经上线过了，或者数据出错了，你要联系Bobo哟！');
    		}
    		$dataList[] = array(
    					'pub_platform' => $option['pub_platform'],
    					'pub_channel' => $option['pub_channel'],
    					'online_id' => $online_id,
    					'online_time' => !empty($option['time']) ? ($option['time'].":00") : $add_time,
    					'add_time' => $add_time
    			);
    	}
    	 
    	if (false === $onlineMap4->addAll($dataList)) {
    		$this->error("插入数据库失败呢！");
    	} else {
    		$rst = $this->_updateOnlineMap($homeOnline4);
    		if (!empty($rst)) {
    			$this->success('上线成功，但是'.$rst);
    		}else {
    			$this->success('上线成功');
    		}
    	}
    }
    
    /**
     * 实实在在的下线操作
     * Enter description here ...
     */
    public function doOffline4(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$online_id = I('id');
    	if (empty($online_id)) {$this->error('参数错误');}
    	 
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1]
    		);
    	}
    	 
    	$homeOnline4 = D('HomeOnline4')->getById($online_id);
    	if (empty($homeOnline4)) {
    		$this->error('擦嘞，配置被删了');
    	}
    	 
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	 
    	$onlineMap4 = D('HomeOnlineMap4');
    	$offline_x = array(); // 平台-渠道-下线数据ID 用于最近一条上线ID 修改open为1
    	foreach ($offline_options as $option) {
    		$map = array(
    				'pub_platform' => $option['pub_platform'],
    				'pub_channel' => $option['pub_channel'],
    				'online_id' => $online_id
    		);
    		if (false === $onlineMap4->where($map)->delete()) {
    			$this->error('在下线' .$option['pub_platform'].'-'.$option['pub_channel'].' 渠道，失败鸟啊。。。。');
    		}
    	}
    	
    	$rst = $this->_updateOnlineMap($homeOnline4);
    	if (!empty($rst)) {
    		$this->success('下线成功，但是'.$rst);
    	}else {
    		$this->success('下线成功');
    	}
    }
    
    /**
     * 更新推荐列表
     * @param unknown $HomeOnline4
     * @return string
     */
    private function _updateOnlineMap($HomeOnline4){
    	// step 1 删除已经上线的列表
    	$map = array('script_id'=>$HomeOnline4['id']);
    	$HomeOnlineMap = D('HomeOnlineMap');
    	$tmp_list = $HomeOnlineMap->where($map)->field('pub_channel,pub_platform,online_id,online_type')->select();
    	$old_online_list = array();
    	if (!empty($tmp_list)) {
    		foreach ($tmp_list as $tmp) {
    			$key = implode(',', $tmp);
    			$old_online_list[$key] = $tmp;
    		}
    	}
    	if (false === $HomeOnlineMap->where($map)->delete()) {
    		return '删除老的上线列表失败';
    	}
    	
    	// step2  根据script获取上线ID,信息
    	$script = json_decode($HomeOnline4['script'], true);
    	$dataList = array();
    	$Movie = D('Movie');
    	$time = toDate(NOW_TIME);
    	foreach ($script as $item) {
    		$data = array('open'=>1,'online_id'=>'','online_type'=>'','pic'=>'','intro'=>'','script_id'=>$HomeOnline4['id'],'online_time'=>'','add_time'=>$time);
    		if ($item['type'] == 1) {
    			$one = $Movie->where(array('id'=>$item['movie_id']))->field('imgserver_use,bpic,vol_count')->find();
    			$data['online_type'] = '';
    			$data['online_id'] = $item['movie_id'];
    			//$data['pic'] = getImgServerURL2($one['imgserver_use']).'/graphmovie/'.$one['bpic'];
    			$data['pic'] = getImgServerURL2(8).'/graphmovie/'.$one['bpic'];
    			$data['intro'] = $item['sub_title'];
    			if ($one['vol_count'] == 1) {
    				$data['online_type'] = 1;
    			} else if ($one['vol_count'] == 2) {
    				$data['online_type'] = 2;
    			}
    			if (!empty($data['online_type'])) {
    				$dataList[] = $data;
    			}
    		} else if ($item['type'] == 2) { // 微图解
    			$data['online_type'] = 3;
    			$data['online_id'] = $item['wei_id'];
    			$data['pic'] = $item['image'];
    			$dataList[] = $data;
    		} else if ($item['type'] == 3) { // 画报
    			$data['online_type'] = 4;
    			$data['online_id'] = $item['paper_id'];
    			$data['pic'] = $item['image'];
    			$dataList[] = $data;
    		} else if ($item['type'] == 5) { // 专辑
    			$data['online_type'] = 5;
    			$data['online_id'] = $item['topic_id'];
    			$data['pic'] = $item['image'];
    			$dataList[] = $data;
    		} else if ($item['type'] == 6) { // 每日一图
    			$data ['online_id'] = $item['pic_id'];
    			$data ['online_type'] = 6;
    			$dataList[] = $data;
    		}
    	}
    	
    	// step3 查询上线的平台渠道，然后再上线
    	$online_list = D('HomeOnlineMap4')->where(array('online_id'=>$HomeOnline4['id'],'open'=>1))->select();
    	if (!empty($online_list) && !empty($dataList)) {
    		$insert_data = array();
    		foreach ($online_list as $item) {
    			foreach ($dataList as $data) {
    				$data['online_time'] = $item['online_time'];
    				$data['pub_channel'] = $item['pub_channel'];
    				$data['pub_platform'] = $item['pub_platform'];
    				
    				// 注意Key的顺序，和上面顺序保持一致
    				$key = $data['pub_channel'].','.$data['pub_platform'].','.$data['online_id'].','.$data['online_type'];
    				if (isset($old_online_list[$key])) {
    					unset($old_online_list[$key]);
    				}
    				
    				// 关闭之前上线过的电影，避免重复
    				$map = array('pub_channel'=>$data['pub_channel'],'pub_platform'=>$data['pub_platform'],'online_id'=>$data['online_id'],'online_type'=>$data['online_type']);
    				//2016年1月23日17:28:31 如果存在新版上线的数据，则不覆盖
    				$map ['config_id'] = array('egt',10000);
    				$one = $HomeOnlineMap->where($map)->find();
    				if (!empty($one)) { // 如果存在V4.8版上线规则
    					$data['open'] = 0;
    				}
    				$map ['config_id'] = array('lt',10000);
    				$HomeOnlineMap->where($map)->save(array('open'=>0));
    				
    				$insert_data[] = $data;
    			}
    		}
    		if (false === $HomeOnlineMap->addAll($insert_data)) {
    			return '插入推荐列表失败';
    		}
    	}
    	
    	// 这个时候得到的数据，是之前上线过的渠道ID，现在被下线了，这些数据，需要重新打开
    	if (!empty($old_online_list)) {
    		foreach ($old_online_list as $tmp) {
    			$map = $tmp;
    			$map ['config_id'] = array('egt',10000);
    			$one = $HomeOnlineMap->where($map)->find();
    			if (empty($one)) { // 如果存在V4.8版上线规则，则以新版为准，就不重新打开了
    				$one = $HomeOnlineMap->where($tmp)->field('id')->order('online_time desc')->find();
    				if (!empty($one)) {
    					$one['open'] = 1;
    					$HomeOnlineMap->save($one);
    				}
    			}
    		}
    	}
    	
    	return '';
    }
     
    public function addUploadImage(){
    	$rst = array('rst'=>0);
    	// 针对不支持HTTP_X_FILENAME 默认设置为default.jpg
	    $fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : 'default.jpg');
		if ($fn) {
			$tmp = explode('.', $fn);
			$savePath = '../homeImg/'.toDate(NOW_TIME,'Ymd').'/';
			$saveName = NOW_TIME.rand(1000, 9999).'.'.end($tmp);
			if (mk_dir($savePath)){
				// 获取输入流，存到文件里
				$files = array();
				$files[] = array('key'=>'homeImg','file'=>$savePath.$saveName);
				file_put_contents($savePath.$saveName,file_get_contents('php://input'));
				
				$imgRst = sendFileToImgSevr('homeImg',0,$files);
				if (is_array($imgRst)) {
					foreach ($files as $file) {
						if (isset($imgRst['succ'][$file['key']])) {
							$rst['rst'] = 1;
							$rst['data']['url'] = $imgRst['succ'][$file['key']]['url'];
							$this->ajaxReturn($rst);
						} else {
							$rst['msg'] = '上传到图片服务器出错';
							$this->ajaxReturn($rst);
						}
					}
				}else {
					if (empty($imgRst)) {
						$rst['msg'] = '上传图片服务器错误';
						$this->ajaxReturn($rst);
					} else {
						$rst['msg'] = $imgRst;
						$this->ajaxReturn($rst);
					}
				}
			} else {
				$rst['msg'] = '创建目录失败';
				$this->ajaxReturn($rst);
			}
		} else {
			$rst['msg'] = '服务器环境不支持HTML5 文件上传格式'; 
			$this->ajaxReturn($rst);
		}
    }
    
    
    /**
     * 2015年6月12日14:58:04 获取
     */
    public function getReadData(){
    	$id = I('id');
    	if (empty($id) || !is_numeric($id)) { $this->error('参数错误');}
    	$one = D('HomeOnline4')->getById($id);
    	if (empty($one)) {$this->error('内容不存在');}
    	$map = array('pub_channel'=>'xiaomi', 'pub_platform'=>'android', 'online_id'=>$id);
    	$online_time = D('HomeOnlineMap4')->where($map)->getField('online_time');
    	if (empty($online_time)) {$this->error('还没上线过呢！');}
    	$this->assign('online_time', $online_time);
    	$online_time = strtotime($online_time);
    	$table_time = array();
    	for ($i=-2; $i<=7; $i++) {
    		$table_time[] = toDate($online_time + (86400*$i), 'Y-m-d 00:00:00');
    	}
    	$script = json_decode($one['script'], true);
    	$model = D('StatMovieReaddata');
    	$rst = array();
    	$movie_list = array();
    	foreach ($script as $item) {
    		if (isset($item['movie_id'])) {
    			$movie_list[$item['movie_id']] = D('Movie')->where(array('id'=>$item['movie_id']))->getField('name');
    			$map = array('movie_id'=>$item['movie_id'], 'day_time'=>array('between',array($table_time[0], $table_time[count($table_time)-1])));
    			$list = $model->where($map)->select();
    			foreach ($list as $item) {
    				$rst[$item['movie_id']][$item['day_time']] = $item;
    			}
    		}
    	}
    	$this->assign('table_time', $table_time);
    	$this->assign('rst', $rst);
    	$this->assign('movie', $movie_list);
    	$this->display();
    }
    
    /**
     * 2015年6月17日17:02:39  获取2
     */
    public function getReadData2() {
    	$stime = I('stime');
    	$etime = I('etime');
    	if (empty($stime) || empty($etime)) {$this->display(); return;}
    	$map = array('pub_channel'=>'xiaomi', 'pub_platform'=>'android', 'online_time'=>array('between', array($stime, $etime)));
    	$online_list = D('HomeOnlineMap4')->where($map)->field('online_id,online_time')->order('online_time desc')->select();
    	if (empty($online_list)) {
    		$this->error('这段时间小米渠道没有上线的脚本哟。', U('HomeOnline/getReadData2'));
    	}
    	$list = array();
    	$model = D('StatMovieReaddata');
    	foreach ($online_list as $item) {
    		$one = D('HomeOnline4')->getById($item['online_id']);
    		$table_time = array();
    		for ($i=-2; $i<=7; $i++) {
    			$table_time[] = toDate(strtotime($item['online_time']) + (86400*$i), 'Y-m-d 00:00:00');
    		}
    		$script_list = json_decode($one['script'], true);
    		foreach ($script_list as $script) {
    			if (isset($script['movie_id'])) {
    				$data = array(
    					'name' => $one['name'],
    					'remark' => $one['remark'],
    					'movie_id' => $script['movie_id'],
    					'movie_name' => D('Movie')->where(array('id'=>$script['movie_id']))->getField('name'),
    					'online_time'=>$item['online_time'],
    				);
    				$map = array('movie_id'=>$script['movie_id'], 'day_time'=>array('between',array($table_time[0], $table_time[count($table_time)-1])));
    				$play_list = $model->where($map)->select();
    				$play_tmp = array();
    				foreach ($play_list as $play) {
    					$play_tmp[$play['day_time']] = $play;
    				}
    				foreach ($table_time as $day) {
    					$data['list'][] = isset($play_tmp[$day]) ? $play_tmp[$day] : 0; 
    				}
    				$list [] = $data;
    			}
    		} 
    	}
    	$this->assign('list', $list);
    	$this->display();
    }
    
    
    /**
     * 2015年12月18日12:07:13 以下是4.8版本添加的
     */
    
    public function homeConfig(){
    	$file = 'APP_DATA/V4.8/curr.boo';
    	if (file_exists($file)) {
    		$this->assign('script',json_decode(file_get_contents($file),true));
    	}
    	$this->display();
//     	$rst = array(
//     			'menubar' => array(
//     					'btn1' => array('name'=>'排行榜','icon'=>'http://121.41.88.44:8080/icon/index_icon_rank.png'),
//     					'btn2' => array('name'=>'猜你喜欢','icon'=>'http://121.41.88.44:8080/icon/index_icon_rank.png'),
//     					'btn3' => array('name'=>'今日茶点','icon'=>'http://121.41.88.44:8080/icon/index_icon_rank.png'),
//     			),
//     			1 => array('title'=>'标题','icon'=>'http://www.graphmovie.com/icon.png','movies'=>array(1212,3232,2525,5455),'adv'=>array(20,40)),
//     			2 => array('title'=>'标题','icon'=>'http://www.graphmovie.com/icon.png','adv'=>array()),
//     			3 => array('title'=>'标题','icon'=>'http://www.graphmovie.com/icon.png','adv'=>array()),
//     	);
    	
//     	for ($i = 10; $i<26; $i++) {
//     		$rst[$i] = array('title'=>'标题','icon'=>'http://www.graphmovie.com/icon.png','adv'=>array());
//     	}
//     	print_r(json_encode($rst));
    } 
    
    
    public function tuijian(){
    	$map = $this->_search('HomeTuijian');
    	$this->_list(D('HomeTuijian'), $map);
    	$list = $this->get('list');
    	foreach ($list as $key=>$item) {
    		$data = array();
    		if ($item['t_type']==1 || $item['t_type'] == 2) { // 图解
    			$one = D('Movie')->where(array('id'=>$item['t_id']))->find();
    			$data['name'] = $one['name'];
    			$data['pic'] = $one['bpic'];
    		} else if ($item['t_type']==3){ // 微图解
    			$one = D('Wei')->where(array('id'=>$item['t_id']))->find();
    			$data['name'] = $one['title'];
    			$data['pic'] = $one['pic'];
    		} else if ($item['t_type']==4){ // 画报
    			$one = D('News')->where(array('id'=>$item['t_id']))->find();
    			$data['name'] = $one['name'];
    			$data['pic'] = $one['bpic'];
    		}
    		$map = array('pub_channel'=>'xiaomi','pub_platform'=>'android','online_type'=>$item['t_type'],'online_id'=>$item['t_id'],'config_id'=>$item['id']);
    		$list[$key]['online_time'] = D('HomeOnlineMap')->where($map)->getField('online_time');
    		$list[$key]['data'] = $data;
    	}
    	$this->assign('type',array(1=>'电影', 2=>'剧集', 3=>'微图解', '4'=>'画报'));
    	$this->assign('list',$list);
    	$this->display();
    } 
    
    public function doAddTuijian(){
    	$model = D('HomeTuijian');
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	if (false === $model->add()) {
    		$this->error($model->getError());
    	}
    	$this->success('添加成功');
    }
    
    /**
     * 推荐的数据，上下线
     */
    public function onlineTuijian(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$tuijian = D('HomeTuijian')->getById($id);
    	if (empty($tuijian)){
    		$this->error('参数错误');
    	}
    	$type = I('type');
    	if (empty($type)) {
    		$this->error('参数错误');
    	}
    	 
    	$platform = D('Platform');
    	$map = array('open' => 1);
    	$platformList = $platform->where($map)->getField('name,id');
    	
    	$channel = D('Channel');
    	$channelList = $channel->where($map)->getField('name,id');
    	
    	$plVch = D('PlatformVChannel');
    	$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
    	
    	
    	$onlineMapPC = D('HomeOnlineMap');
    	$map = array (
    			'config_id' => $id,
    			'online_type' => $type
    	);
    	$list = $onlineMapPC->where($map)->select();
    	
    	$onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
    	if (!empty($list)) {
    		foreach ( $list as $val ) {
    			if (!isset($onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]])) {
    				$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'online_time' => $val['online_time']);
    				$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
    			}
    		}
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
     * 下线推荐数据
     */
    public function doOfflineTuijian(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$config_id = I('id');
    	if (empty($config_id)) {$this->error('参数错误');}
    	 
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1]
    		);
    	}
    	 
    	$tuijian = D('HomeTuijian')->getById($config_id);
    	if (empty($tuijian)) {
    		$this->error('擦嘞，配置被删了');
    	}
    	$ids = array($tuijian['t_id']);
    	$online_type = $tuijian['t_type'];
    	 
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    	 
    	$onlineMap = D('HomeOnlineMap');
    	$offline_x = array(); // 平台-渠道-下线数据ID 用于最近一条上线ID 修改open为1
    	foreach ($offline_options as $option) {
    		$map = array(
    				'pub_platform' => $option['pub_platform'],
    				'pub_channel' => $option['pub_channel'],
    				'config_id' => $config_id
    		);
    		if (false === $onlineMap->where($map)->delete()) {
    			$this->error('在下线' .$option['pub_platform'].'-'.$option['pub_channel'].' 渠道，失败鸟啊。。。。');
    		}
    	
    		foreach ($ids as $id) {
    			$offline_x[$option['pub_platform'].'-'.$option['pub_channel'].'-'.$id] = 1;
    		}
    	}
    	 
    	//  把id，type 相同的记录OPen置为1
    	$map = array('online_id'=>array('in',$ids),'online_type'=>$online_type);
    	// 根据Online_time 降序
    	$list = $onlineMap->where($map)->field('id,pub_platform,pub_channel,online_id,online_type')->order('online_time desc')->select();
    	if(!empty($list)) {
    		foreach ($list as $item) {
    			$key = $item['pub_platform'].'-'.$item['pub_channel'].'-'.$item['online_id'];
    			if (isset ($offline_x[$key]) && $offline_x[$key]==1) {
    				$offline_x[$key] = 0;
    				$data = array('open'=>1,'id'=>$item['id']);
    				if (false === $onlineMap->save($data)){
    					$this->error('下线成功，不过在更新列表时失败了');
    				}
    			}
    		}
    	}
    	$this->success('下线成功');
    }
    
    /**
     * 从其他地方，直接推荐来的数据
     */
    public function doTuijian(){
    	$online_channel = array(
    			'android' => array('91','qq','xiaomi','other','wandoujia','360','google','anzhi','me','lenovo','meizu','appchina','jinli','anzhuo','hiapk','nduo','pad','889wan','gfan','letv','yy','woshop','zaker','163','mybug','taobao','oppo','gmoper','mytest1'),
    			'ios' => array('appstore','91','pp'),
    			'androidpad' => array('xiaomi')
    	);
    	$id = I('id',null);
    	$type = I('type',null);
    	$online_time = I('online_time',null);
    	//推荐的类型，1电影，2新番，3微图解，4画报，5专辑，6每日一图
    	$type_list = array('1'=>'Movie', '2'=>'Movie', '3' => 'Wei', '4'=> 'News', '5'=>'Topic');
    	if (empty($id) || empty($type) || !is_numeric($id) || !is_numeric($type) || !isset($type_list[$type])) {
    		$this->error('参数错误');
    	}
    	
    	$model = D($type_list[$type]);
    	$id = $model->where(array('id'=>$id))->getField('id');
    	if (empty($id)) {
    		$this->error('数据不存在');
    	}
    	
    	// step2 查询推荐列表里，是否有数据
    	$map = array('t_id'=>$id,'t_type'=>$type);
    	$Tuijian = D('HomeTuijian');
    	$onlineMap = D('HomeOnlineMap');
    	$t_id = $Tuijian->where($map)->getField('id');
    	if (empty($t_id)) { // 没有查询到，则插入一条数据
    		$type_arr = array('1'=>'movie', '2'=>'movie', '3' => 'wei', '4'=> 'paper', '5'=>'topic');
    		$_POST = array( // 这里防止TuijianModel里的东西，暂时没时间改。
    			't_id' => $id,
    			'tuijian_type' => $type_arr[$type],
    			'jian_time' => $online_time
    		);
    		if (false === $Tuijian->create()) {
    			$this->error($Tuijian->getError());
    		} else {
    			$t_id = $Tuijian->add();
    			if (empty($t_id)) {
    				$this->error('插入推荐数据失败');
    			}
    		}
    	} else { // 删除老的上线数据
    		$map = array('config_id'=>$t_id);
    		$onlineMap->where($map)->delete();
    	}
    	
    	// step3 插入新的上线数据
    	$dataList = array();
    	$add_time = toDate(NOW_TIME);
    	foreach ($online_channel as $platform => $item) {
    		foreach ($item as $channel) {
	    		$dataList[] = array(
	    					'pub_platform' => $platform,
	    					'pub_channel' => $channel,
	    					'online_id' => $id,
	    					'online_type' => $type,
	    					'pic' => '',
	    					'intro' => '',
	    					'online_time' => !empty($online_time) ? ($online_time) : $add_time,
	    					'config_id' => $t_id,
	    					'add_time' => $add_time
	    			);
	    		// step3.1 下线旧的数据
	    		$map = array('pub_platform' => $platform,'pub_channel' => $channel,'online_id' => $id,'online_type' => $type);
	    		$onlineMap->where($map)->save(array('open'=>0));
    		}
    	}
    	if (false === $onlineMap->addAll($dataList)) {
    		$this->error('插入上线数据错误');
    	} 
    	
    	$this->success('推荐成功');
    }
    
    /**
     * 上线推荐数据
     */
    public function doOnlineTuijian(){
    	$online_list = I('online');
    	if (empty($online_list)) {$this->error('选择要上线的平台渠道啊');}
    	$conf_id = I('id');
    	if (empty($conf_id)) {$this->error('参数错误');}
    	 
    	$online_options = array();
    	foreach ($online_list as $val) {
    		$time = I($val.',time','');
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1],
    				'time' => $time
    		);
    	}
    	 
    	$tuijian = D('HomeTuijian')->getById($conf_id);
    	if (empty($tuijian)) {
    		$this->error('擦嘞，配置被删了');
    	}
    	 
    	$ids = array($tuijian['t_id']);
    	$pics = array(''); // 图片为空
    	$intros = array(''); // 解说为空
    	$online_type = $tuijian['t_type'];
    	$dataList = array();
    	$add_time = toDate(NOW_TIME);
    	 
    	$onlineMap = D('HomeOnlineMap');
    	$map = array (
    			'config_id' => $conf_id,
    			'online_type' => $online_type
    	);
    	
    	$online_p_c = array();
    	$list = $onlineMap->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			$online_p_c[$item['pub_platform'].'-'.$item['pub_channel']] = true;
    		}
    	}
    	 
    	foreach($online_options as $option) {
    		$exists_key = $option['pub_platform'].'-'.$option['pub_channel'];
    		if (isset($online_p_c[$exists_key])) {
    			$this->error($exists_key.' 渠道已经上线过了，或者数据出错了，你要联系Bobo哟！');
    		}
    		foreach ($ids as $key=>$id) {
    			$dataList[] = array(
    					'pub_platform' => $option['pub_platform'],
    					'pub_channel' => $option['pub_channel'],
    					'online_id' => $id,
    					'online_type' => $online_type,
    					'pic' => $pics[$key],
    					'intro' => $intros[$key],
    					'online_time' => !empty($option['time']) ? ($option['time'].":00") : $add_time,
    					'config_id' => $conf_id,
    					'add_time' => $add_time
    			);
    		}
    	}
    	 
    	// 	把id，type 相同的记录OPen置为0
    	$data = array('open'=>0);
    	$map = array('online_id'=>array('in',$ids),'online_type'=>$online_type);
    	if (false === $onlineMap->where($map)->save($data)){
    		$this->error('上线成功，不过更新上线列表失败');
    	} else {
    		if (false === $onlineMap->addAll($dataList)) {
    			$this->error("插入数据库失败呢！");
    		} else {
    			$this->success('上线成功');
    		}
    	}
    }
    
    
    /**
     * 首页弹窗配置
     */
    public function homeDialog(){
    	$map = $this->_search('HomeDialog');
    	$this->_list(D('HomeDialog'), $map);
    	$list = $this->get('list');
    	foreach ($list as $key=>$item) {
    		$map = array('pub_channel'=>'xiaomi','pub_platform'=>'android','dialog_id'=>$item['id']);
    		$list[$key]['online'] = D('HomeDialogOnlineMap')->where($map)->field('start_time,end_time')->find();
    	}
    	$this->assign('list',$list);
    	$this->display();
    }
    
    public function doAddHomeDialog(){
    	$model = D('HomeDialog');
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	if (isset($_POST['up_img_0'])) {unset($_POST['up_img_0']);}
    	$this->_uploadImg(0,0,'homeImg');
    	if (!isset($_POST['up_img_0'])){
    		$this->error('图片不能为空');
    	}else {
    		$_POST['image'] = 'http://avatar.graphmovie.com/boo/'.$_POST['up_img_0'];
    	}
    	if (false === $model->create() || false === $model->add()){
    		$this->error($model->getError());
    	}
    	$this->success('添加成功',getCurrentUrl());
    }
    
    /**
    * 推荐的弹窗，上下线
    */
    public function onlineHomeDialog(){
	    $id = I('id');
	    if (empty($id)) {
	    	$this->error('参数错误');
	   	}
	    $dialog = D('HomeDialog')->getById($id);
	    if (empty($dialog)){
	    	$this->error('参数错误');
	    }
    
    	$platform = D('Platform');
    	$map = array('open' => 1);
    	$platformList = $platform->where($map)->getField('name,id');
   		$channel = D('Channel');
   		$channelList = $channel->where($map)->getField('name,id');
   		$plVch = D('PlatformVChannel');
   		$plVchList = $plVch->where($map)->field('platform_id,channel_id')->select();
    				 
		$onlineMapPC = D('HomeDialogOnlineMap');
    	$map = array (
    		'dialog_id' => $id,
    	);
        $list = $onlineMapPC->where($map)->select();
        	 
        $onlineList = array(); $onlineArr = array(); // 记录已经上线的平台ID渠道ID
        if (!empty($list)) {
        	foreach ( $list as $val ) {
	        	if (!isset($onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]])) {
	        		$onlineList[$val['pub_platform']][] = array('channel' => $val['pub_channel'], 'start_time' => $val['start_time'], 'end_time' => $val['end_time']);
	    			$onlineArr[$platformList[$val['pub_platform']]."-".$channelList[$val['pub_channel']]] = true;
	        	}
        	}
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
     * 下线推荐数据
     */
    public function doOfflineHomeDialog(){
    	// step1 判断参数合法性
    	$offline_list = I('offline');
    	if (empty($offline_list)) {$this->error('要选择需要下线的平台渠道呀');}
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    
    	$offline_options = array();
    	foreach ($offline_list as $val) {
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$offline_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1]
    		);
    	}
    
    	$dialog = D('HomeDialog')->getById($id);
    	if (empty($dialog)) {
    		$this->error('擦嘞，记录被删了');
    	}
    	// setp2 找出已经上线的平台和渠道 ，判断出是否是全部下线
    
    	$onlineMap = D('HomeDialogOnlineMap');
    	foreach ($offline_options as $option) {
    		$map = array(
    				'pub_platform' => $option['pub_platform'],
    				'pub_channel' => $option['pub_channel'],
    				'dialog_id' => $id
    		);
    		if (false === $onlineMap->where($map)->delete()) {
    			$this->error('在下线' .$option['pub_platform'].'-'.$option['pub_channel'].' 渠道，失败鸟啊。。。。');
    		}
    	}
    
    	$this->success('下线成功');
    }
    
    /**
     * 上线推荐数据
     */
    public function doOnlineHomeDialog(){
    	$online_list = I('online');
    	if (empty($online_list)) {$this->error('选择要上线的平台渠道啊');}
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    
    	$online_options = array();
    	foreach ($online_list as $val) {
    		$stime = I($val.',stime','');
    		$etime = I($val.',etime','');
    		$tmp = explode(',', $val);
    		if (count($tmp) != 2) {$this->error('参数错误');}
    		$online_options[] = array(
    				'pub_platform' => $tmp[0],
    				'pub_channel' => $tmp[1],
    				'stime' => $stime,
    				'etime' => $etime
    		);
    	}
    
    	$dialog = D('HomeDialog')->getById($id);
    	if (empty($dialog)) {
    		$this->error('擦嘞，记录被删了');
    	}
    	
    
    	$dataList = array();
    	$add_time = toDate(NOW_TIME);
    
    	$onlineMap = D('HomeDialogOnlineMap');
    	$map = array (
    			'dialog_id' => $id
    	);
    	 
    	$online_p_c = array();
    	$list = $onlineMap->where($map)->select();
    	if (!empty($list)) {
    		foreach ($list as $item) {
    			$online_p_c[$item['pub_platform'].'-'.$item['pub_channel']] = true;
    		}
    	}
    
    	foreach($online_options as $option) {
    		$exists_key = $option['pub_platform'].'-'.$option['pub_channel'];
    		if (isset($online_p_c[$exists_key])) {
    			$this->error($exists_key.' 渠道已经上线过了，或者数据出错了，你要联系Bobo哟！');
    		}
    		$dataList[] = array(
    				'pub_platform' => $option['pub_platform'],
    				'pub_channel' => $option['pub_channel'],
    				'dialog_id' => $id,
    				'start_time' => !empty($option['stime']) ? ($option['stime'].":00") : $add_time,
    				'end_time' => !empty($option['etime']) ? ($option['etime'].":00") : toDate(NOW_TIME + 86400),
    				'add_time' => $add_time
    		);
    	}
    	if (!empty($dataList)) {
    		if (false === $onlineMap->addAll($dataList)){
    			$this->error($onlineMap->getError());
    		} else {
    			$this->success('上线成功');
    		}
    	} else {
    		$this->error('没有数据呢。');
    	}
    }
    
    public function updateHomeDialogScript(){
    	$id = I('id');
    	$rst = array('rst'=>0,'msg'=>'');
    	if (empty($id) || !is_numeric($id)) {
    		$rst['msg'] = '参数错误';
    		$this->ajaxReturn($rst);
    	}
    	$dialog = D('HomeDialog');
    	if ($dialog->create() === FALSE) {
    		$rst['msg'] = '修改失败1';
    		$this->ajaxReturn($rst);
    	}
    	if ($dialog->save() === FALSE) {
    		$rst['msg'] = '修改失败2';
    		$this->ajaxReturn($rst);
    	} else {
    		$rst['rst'] = 1;
    		$this->ajaxReturn($rst);
    	}
    }
}