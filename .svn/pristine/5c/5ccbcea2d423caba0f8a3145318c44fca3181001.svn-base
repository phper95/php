<?php
// 资讯模块
class NewsAction extends CommonAction {
	private $_news_file_path = '../../appweb/news/';
	/**
	 * 每日一图操作
	 */
	public function dayPic(){
		$map = array();
		$this->_list(D('DayPic'), $map);
		$this->display();
	}
	
	/**
	 * 添加每日一图
	 */
	public function addDayPic(){
		$this->display();
	}
	
	/**
	 * 添加每日一图前的处理 
	 */
	function _before_doAddDayPic(){
		$DayPic = D('DayPic');
		if (false === $DayPic->create()) {
			$this->error($DayPic->getError());
		}
		$map = array('online_time'=>I('online_time'));
		$one = $DayPic->where($map)->getField('id');
		if (!empty($one)) {
			$this->error('这一天已经上线了一张图片了哦。');
		}
		$this->_uploadImg2();
		$image_url = I('image_url',null);
		if (empty($image_url)) {
			$this->error('没选择文件，就不要提交了嘛~~~');
		}
		unset($DayPic);
	}
	
	/**
	 * Do 添加每日一图
	 */
	public function doAddDayPic(){
		$DayPic = D('DayPic');
		if (false === $DayPic->create()) {
			$this->error($DayPic->getError());
		} else {
			if (false === $DayPic->add()) {
				$this->error('插入数据失败');
			} else {
				$this->success('添加成功',getCurrentUrl());
			}
		}
	}
	
	/**
	 * 编辑每日一图
	 */
	public function editDayPic(){
		$id = I('id',NULL);
		if (empty($id) || !is_numeric($id)) {$this->error('参数错误');}
		$map = array('id'=>$id);
		$vo = D('DayPic')->where($map)->find();
		if (empty($vo)) {$this->error('找不到这个记录哟，骚年。');}
		$this->assign('vo',$vo);
		$this->display();
	}
	
	/**
	 * Do 编辑每日一图前的处理
	 */
	function _before_updateDayPic(){
		$id = I('id'); 
		if (empty($id) || !is_numeric($id)) {$this->error('参数错误');}
		$DayPic = D('DayPic');
		if (false === $DayPic->create()) {
			$this->error($DayPic->getError());
		}
		
		$map = array('online_time'=>I('online_time'),'id'=>array('neq',$id));
		$one = $DayPic->where($map)->getField('id');
		if (!empty($one)) {
			$this->error('这一天已经上线了一张图片了哦。');
		}
		$this->_uploadImg2();
		unset($DayPic);
	}
	
	/**
	 * Do 编辑每日一图
	 * Enter description here ...
	 */
	public function updateDayPic(){
		$DayPic = D('DayPic');
		if (false === $DayPic->create()) {
			$this->error($DayPic->getError());
		} else {
			if (false === $DayPic->save()) {
				$this->error('更新数据失败');
			} else {
				$this->success('更新成功',getCurrentUrl());
			}
		}
	}
	
	/**
	 * 资讯列表
	 */
	public function newsList(){
		$map = array();
		$name = I('name');
		if (!empty($name)) {
			$map['name'] = array('like',"%$name%");
		}
		$this->_list(D('News'), $map);
		$list = $this->get('list');
		if (!empty($list)) {
			$channel_list = D('NewsChannel')->where(array('open'=>1,'level'=>1))->getField('id,id,name');
			$this->assign('channel_list',$channel_list);
			$model = D('NewsUChannel');
			foreach ($list as $key=>$item) {
				$channel_id = $model->where(array('paper_id'=>$item['id']))->getField('channel_id');
				$list[$key]['channel'] = empty($channel_id) ? '' : $channel_list[$channel_id]['name']; 
			}
			
			// 2016年1月23日16:13:39 V4.8 版本获取是否推荐至首页标签
			$Tuijian = D('HomeTuijian');
			foreach ($list as $key=>$item) {
				$map = array('t_id'=>$item['id'], 't_type'=>4);
				$tmp = $Tuijian->where($map)->getField('jian_time');
				if (!empty($tmp)) {
					$list[$key]['tuijian_time'] = $tmp;
				}
			}
			
			$this->assign('list',$list);
		}
		$this->display();
	}
	
	/**
	 * 添加资讯
	 */
	public function addNews(){
		$graphers = getConfigList(C('CONFIG_DB_TYPE.NEWS_GRAPHER'));
		if (!empty($graphers)) {
			$Member = D('Member');
			foreach ($graphers as $member_id=>$value) {
				$name = $Member->where(array('id'=>$member_id))->getField('name');
				$graphers[$member_id] = $member_id.' - '.$name;
			}
			$this->assign('graphers',$graphers);
		}
		$this->display();
	}
	
	public function _before_doAddNews(){
		$grapher = I('grapher',null);
		$tmp = explode('-', $grapher);
		if (empty($tmp[0]) && !is_numeric($tmp[0])) {
			$this->error('作者参数错误');
		}
		$grapher = trim($tmp[0]);
		$id = D('Member')->where(array('id'=>$grapher))->getField('id');
		if (empty($id)) {
			$this->error('数据库没有该作者哦');
		}
		$_POST['grapher'] = $id; // 修改ID
	}
	
	/**
	 * Do添加资讯
	 */
	public function doAddNews(){
		$News = D('News');
		if (false === $News->create()) {
			$this->error($News->getError());
		}elseif (false === $News->add()) {
			$this->error('插入数据库错误');
		}else {
			addConfigItem(I('grapher'),C('CONFIG_DB_TYPE.NEWS_GRAPHER'));
			$this->success('添加成功',getCurrentUrl());
		}
	}
	
	/**
	 * 编辑资讯信息 
	 */
	public function editNews(){
		$id = I('id',NULL);
		if (empty($id) || !is_numeric($id)) {$this->error('参数错误');}
		$vo = D('News')->where(array('id'=>$id))->find();
		$this->assign('vo',$vo);
		
		$graphers = getConfigList(C('CONFIG_DB_TYPE.NEWS_GRAPHER'));
		if (!empty($graphers)) {
			$Member = D('Member');
			foreach ($graphers as $member_id=>$value) {
				$name = $Member->where(array('id'=>$member_id))->getField('name');
				$graphers[$member_id] = $member_id.' - '.$name;
			}
			$this->assign('graphers',$graphers);
		}
		
		$this->display();
	}
	
	public function _before_updateNews(){
		$grapher = I('grapher',null);
		$tmp = explode('-', $grapher);
		if (empty($tmp[0]) && !is_numeric($tmp[0])) {
			$this->error('作者参数错误');
		}
		$grapher = trim($tmp[0]);
		$id = D('Member')->where(array('id'=>$grapher))->getField('id');
		if (empty($id)) {
			$this->error('数据库没有该作者哦');
		}
		$_POST['grapher'] = $id; // 修改ID
	}
	
	/**
	 * 更新资讯信息 
	 */
	public function updateNews() {
		$News = D('News');
		if (false === $News->create()) {
			$this->error($News->getError());
		}elseif (false === $News->save()) {
			$this->error('更新数据库错误');
		}else {
			$this->execChange(I('id'));
			$this->success('更新成功',getCurrentUrl());
		}
	}
	
	/**
	 * 获取电影，广告，专辑，资讯，用户信息
	 * Enter description here ...
	 */
	public function getOneInfo(){
		$type_arr = array(
			'movie'=>array('Movie','id,name,bpic,spic'),
			'adv'=>array('Adv','id,name,bpic,spic,fullscreen_show_pic'),
			'topic'=>array('Topic','id,name,bpic,spic'),
			'user'=>array('Member','id,name,avatar'),
			'news'=>array('News','id,name')
		);
		$id = I('id');
		$type = I('type');
		$rst = array('rst'=>0,'msg'=>'');
		
		if($type=='user') {
			$id = userIdKeyDecode($id);
		}
		
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
				$one['bpic'] = getImgServerURL(6).'/'.$one['bpic'];
				$one['spic'] = getImgServerURL(6).'/'.$one['spic'];
			}
			$rst['rst'] = 1;
			$rst['data'] = $one;
			$this->ajaxReturn($rst); return;
		}
	}
	
	/**
	 * 上传资讯图片 
	 */
	public function uploadImg(){
		$id = I('id',null);
		if (empty($id) || !is_numeric($id)) {
			$this->error('参数错误');
		}
		$map = array('id'=>$id);
		$one = D('News')->where($map)->field('id,name,spic,bpic,blur_pic')->find();
		$this->assign('vo',$one);
		$this->display();
	}
	
	/**
	 * 实实在在的上传资讯图片操作 
	 */
	public function doUploadImg(){
		$id = I('id',null);
		if (empty($id) || !is_numeric($id)) {
			$this->error('参数错误');
		}
		$this->_uploadImg3($id);
		$news = D('News');
		// 这里加上这个，是因为添加之前不知道ID，而每一个资讯，都需要上传图片的，所以添加在了这里
		$_POST['read_url'] = 'http://ser3.graphmovie.com/appweb/news/read_'.$id.'.html';
		$_POST['share_url'] = 'http://ser3.graphmovie.com/appweb/news/share_'.$id.'.html';
		if (false === $news->create()) {
			$this->error($news->getError());
		} else {
			if (FALSE === $news->save()) {
				$this->error('插入数据库失败');
			} else {
				$this->execChange($id);
				$this->success('更新成功',getCurrentUrl());
			}
		}
	}
	
	/**
	 * 修改所有ID
	 */
	public function execChangeAll(){
		$map = array('open'=>1);
		$ids = D('News')->where($map)->getField('id',true);
		foreach ($ids as $id){
			$this->execChange($id);
		}
		$this->success('欧了',U('News/index'));
	}
	
	
	/**
	 * 当有修改之后，渲染模板 
	 */
	private function execChange($news_id){
		$news = D('News')->where(array('id'=>$news_id))->find();
		$news['grapher_id'] = userIdKeyEncode($news['grapher']);
		$news['avatar'] = $news['grapher_name'] = '';
		if (!empty($news['grapher'])) {
			$member = D('Member')->where(array('id'=>$news['grapher']))->field('name,avatar')->find();
			$news['avatar'] = $member['avatar'];
			$news['grapher_name'] = $member['name'];
		}
		
		// 内链转换
		$html_arr = htmlExchangeOurUrl($news['html_content'], $news['zhineng']);
		$news['html_content'] = $html_arr['html'];
		
		$tmp = explode(' ', $news['online_time']);
		$news['online_time'] = $tmp[0];
		
		$assign = array('id'=>'id','title'=>'name','bpic'=>'bpic','news_online_time'=>'online_time','news_title'=>'name','news_sub_title'=>'sub_title','news_summary'=>'summary','grapher_avatar'=>'avatar','grapher_name'=>'grapher_name','content'=>'html_content','grapher_id'=>'grapher_id');
		
		$tpl = file_get_contents('static/news.tpl');
		foreach ($assign as $key=>$item) {
			$tpl = str_replace('{$'.$key.'}', $news[$item], $tpl);
		}
		$filename = $this->_news_file_path.'read_'.$news_id.'.html';
		file_put_contents($filename, $tpl);
		
		$tpl = file_get_contents('static/share_news.tpl');
		$news['html_content'] = $html_arr['share'];
		foreach ($assign as $key=>$item) {
			$tpl = str_replace('{$'.$key.'}', $news[$item], $tpl);
		}
		$filename = $this->_news_file_path.'share_'.$news_id.'.html';
		file_put_contents($filename, $tpl);
	}

	
	/**
     * 上传每日一图图片
     * @param unknown_type $id
     */
	private function _uploadImg2(){
    	if (!empty($_FILES)) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../dayPic';
          
    		if (!is_dir($dir)) { 
                mkdir($dir, 0777);
            }
            
            $dir .= '/';
            $upload->savePath = $dir;
            
            // 设置引用图片类库包路径
            $upload->imageClassPath = '@.ORG.Util.Image';
            
            $saveName = date('YmdHis').'_';
            //删除原图
            $upload->thumbRemoveOrigin = true;
            
            $server_pre = getImgServerURL(0)."/";
            
            $files = array();
    		if (!empty($_FILES['image_url']) && $_FILES['image_url']['error'] != 4) {
            	$upload->saveRule = "d".date('mdHis');
            	$fileInfo = $upload->uploadOne($_FILES['image_url']);
            	if ($fileInfo) {
            		$_POST ['image_url'] = $server_pre.'dayPic/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'image_url','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
            
    		// 2014年9月23日15:03:06 文件上传至图片服务器
    		// 2015年1月20日19:27:29 不需要提交至图片服务器
    		// 2015年2月9日16:00:43 文件上传至imgs5服务器
           if (empty($files)) {
//            	$this->error('没选择文件，就不要提交了嘛~~~');
           } else {
           	$rst = sendFileToImgSevr('dayPic',0,$files);
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
    
    
	/**
     * 上传资讯封面图
     * @param int $id
     */
	private function _uploadImg3($id){
    	if (!empty($_FILES)) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../dayPic/'.$id;
          
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
            // 大图
    		if (!empty($_FILES['bpic']) && $_FILES['bpic']['error'] != 4) {
            	$upload->saveRule = $saveName.'b';
            	$fileInfo = $upload->uploadOne($_FILES['bpic']);
            	if ($fileInfo) {
            		$_POST ['bpic'] = $server_pre.'dayPic/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'bpic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
            // 小图
    		if (!empty($_FILES['spic']) && $_FILES['spic']['error'] != 4) {
            	$upload->saveRule = $saveName.'s';
            	$fileInfo = $upload->uploadOne($_FILES['spic']);
            	if ($fileInfo) {
            		$_POST ['spic'] = $server_pre.'dayPic/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'spic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
            // 虚化图
    		if (!empty($_FILES['blur_pic']) && $_FILES['blur_pic']['error'] != 4) {
            	$upload->saveRule = $saveName.'blur';
            	$fileInfo = $upload->uploadOne($_FILES['blur_pic']);
            	if ($fileInfo) {
            		$_POST ['blur_pic'] = $server_pre.'dayPic/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'blur_pic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                	if($strerror!="没有选择上传文件"){
                    	$this->error($strerror);
                	}
            	}
            }
            
            
    		// 2014年9月23日15:03:06 文件上传至图片服务器
    		// 2015年1月20日19:27:29 不需要提交至图片服务器
    		// 2015年2月9日15:59:45 文件上传至imgs5 服务器
           if (empty($files)) {
           	$this->error('没选择文件，就不要提交了嘛~~~');
           } else {
           	$rst = sendFileToImgSevr('news',$id,$files);
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
    
    
    /**
     * 修改画报频道
     */
    public function updateChannel(){
    	$rst = array('rst'=>0);
    	$model = D('NewsUChannel');
    	if (false === $model->create()){
    		$rst['msg'] = $model->getError();
    		$this->ajaxReturn($rst);
    	}
    	$model->where(array('paper_id'=>I('paper_id')))->delete();
    	
    	if (false === $model->add()){
    		$rst['msg'] = $model->getError();
    		$this->ajaxReturn($rst);
    	}
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    /**
     * 推荐一部微图解，进片场首页
     */
    public function doTuijian(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('error');
    	}
    	$news = D('News')->where(array('id'=>$id))->field('bpic,sub_title,open')->find();
    	if (empty($news) || $news['open'] == 0) {
    		$this->error ('画报不存在或者被关闭了');
    	}
    	$model = D('JianMap');
    	$data = array('online_id'=>$id, 'online_type'=>JianMapModel::$TYPE_PAPER, 'online_time'=>I('online_time'), 'pic'=>$news['bpic'], 'intro'=>$news['sub_title']);
    	$model->where(array('online_id'=>$id,'online_type'=>JianMapModel::$TYPE_PAPER))->delete();
    	if ($model->create($data) === false || $model->add() === false) {
    		$this->error($model->getError());
    	}
    	$this->success('推荐成功');
    }
}
?>