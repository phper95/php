<?php
// 微图解相关操作
class WeiAction extends CommonAction {
	public function _filter(&$map)
    {
        $title = I('title');
        if (!empty($title)) {
        	$map['title'] = array('like',"%$title%");
        }
    }
    
 	public function index() {
 		$pram_channel_id = I('channel_id');
 		if (!empty($pram_channel_id)) {
 			$map = $this->_search('WeiUChannel');
 			$this->_list(D('WeiUChannel'), $map);
 			$tmp_list = $this->get('list');
 			$list = array();
 			foreach ($tmp_list as $key=>$item) {
 				$list[] = D('Wei')->where(array('id'=>$item['wei_id']))->find();
 			}
 			$this->assign('list',$list);
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
        
        $list = $this->get('list');
        if (!empty($list)) {
        	$members = array();
        	$Member = D('Member');
        	$channel_list = D('WeiChannel')->getField('id,title');
        	foreach ($list as $key=>$item) {
        		if (!isset($members[$item['user_id']])) {
        			$map = array('id'=>$item['user_id']);
        			$tmp = $Member->where($map)->field('avatar,name as user_name')->find();
        			$members[$item['user_id']] = empty($tmp) ? array() : $tmp;
        		}
        		$list[$key] = array_merge($members[$item['user_id']], $item);
        		
        		$channel_id = D('WeiUChannel')->where(array('wei_id'=>$item['id']))->getField('channel_id');
        		$list[$key]['channel'] = empty($channel_id) ? '' : $channel_list[$channel_id];
        	}
        	
        	// 2016年1月23日16:13:39 V4.8 版本获取是否推荐至首页标签
        	$Tuijian = D('HomeTuijian');
        	foreach ($list as $key=>$item) {
        		$map = array('t_id'=>$item['id'], 't_type'=>3);
        		$tmp = $Tuijian->where($map)->getField('jian_time');
        		if (!empty($tmp)) {
        			$list[$key]['tuijian_time'] = $tmp;
        		}
        	}
        	
        	
        	$this->assign('list',$list);
        }
        $this->assign('channel',D('WeiChannel')->field('id,title')->select());
        $this->display();
        return;
    }
    
    public function _before_insert(){
    	$act_id = I('act_id');
    	if (!empty($act_id)) {
    		$map = array('user_id'=>2,'open'=>0,'act_id'=>$act_id);
    		$one_wei = D('Wei')->where($map)->find();
    		if (!empty($one_wei)) {
    			$this->error('已经添加过了哟。');
    		}
    	}
    }
    
    /**
     * 关闭微图解
     * Enter description here ...
     */
    public function closeWei(){
    	$wei_id = I('id');
    	if (empty($wei_id)) {$this->error('error');}
    	$map = array('wei_id'=>$wei_id);
    	$data = array('id'=>$wei_id,'open'=>0,'admin_id'=>$_SESSION[C('USER_AUTH_KEY')]);
    	$one = D('Wei')->where(array('id'=>$wei_id))->field('user_id,open')->find();
    	if (false === D('Wei')->save($data)) {
    		$this->error('更新失败');
    	} else {
    		// 2014年12月26日04:04:11 统计表-1
    		if ($one['open'] == '1') {
    			D('MemberCreate')->where(array('user_id'=>$one['user_id']))->setDec('wei');
    		}
    		$this->error('关闭成功',getCurrentUrl());
    	}
    }
    
	/**
     * Open微图解
     * Enter description here ...
     */
    public function openWei(){
    	$wei_id = I('id');
    	if (empty($wei_id)) {$this->error('error');}
    	$map = array('wei_id'=>$wei_id);
    	$data = array('id'=>$wei_id,'open'=>1);
    	$one = D('Wei')->where(array('id'=>$wei_id))->field('user_id,open')->find();
    	if (false === D('Wei')->save($data)) {
    		$this->error('更新失败');
    	} else {
    		// 2014年12月26日04:04:11 统计表-1
    		if ($one['open'] == '0') {
    			$map = array('user_id'=>$one['user_id']);
    			$model = D('MemberCreate'); 
    			$tmp = $model->where($map)->find();
    			if (empty($tmp)) {
    				$data = array('user_id'=>$one['user_id'],'wei'=>1);
    				if (false === $model->add($data)) {
    					$this->error('打开成功，但是插入用户统计表失败');
    				}
    			} else {
    				if (false === $model->where($map)->setInc('wei')){
    					$this->error('打开成功，但是更新用户统计表失败');
    				}
    			}
    		}
    		$this->error('打开成功',getCurrentUrl());
    	}
    }
    
    public function addByAdmin(){
    	$this->assign('channel',D('WeiChannel')->field('id,title')->select());
    	$this->display();
    }
    
    /**
     * 后台添加微图解
     */
    public function doAddByAdmin(){
    	$model = D('Wei');
    	if (false === $model->create()) {
            $this->error($model->getError());
        }
    	load('@.qiniu');
    	if (!empty($_FILES['pic']) && $_FILES['pic']['error'] != 4) {
    		 $file_pic = $_FILES['pic'];
    		 $QiniuUp = new QiniuUp();
    		 $rst = $QiniuUp->upload($file_pic['tmp_name']);
    		 if ($rst['rst'] == 0) {
    		 	$this->error($rst['err']);
    		 } else {
    		 	$_POST['pic'] = $rst['data']['url'];
    		 }
    	}
    	
    	$_POST['open'] = 0; // 默认不上线
    	if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) { //保存成功
        	$data = array('wei_id'=>$list,'channel_id'=>I('channel'));
        	$model = D('WeiUChannel');
        	$model->create($data);
        	$model->add();
        	$film_id = I('filmArr');
        	if (!empty($film_id)) {
        		$film_id = array_unique($film_id); // 去除重复项
        		$dataList = array();
        		$time = toDate(NOW_TIME);
        		foreach ($film_id as $id) {
        			$dataList[] = array('wei_id'=>$list, 'film_id'=>$id, 'add_time'=>$time);
        		}
        		D('WeiUFilm')->addAll($dataList);
        	}
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }
    }
    
    
    public function editByAdmin(){
    	$id = I('id');
    	$vo = D('Wei')->getById($id);
    	$this->assign('vo',$vo);
    	$film_ids=D('WeiUFilm')->where(array('wei_id'=>I('id')))->getField('film_id',true);
    	$film_names = D('Film')->where(array('id'=>array('in',$film_ids)))->getField('id,name');
    	$this->assign('film_list',$film_names);
    	$this->display();
    }
    
    
    public function bindChannel (){
    	$rst = array('rst'=>0);
    	$data = array('wei_id'=>I('wei_id'));
    	$model = D('WeiUChannel');
    	if (false === $model->create()) {
    		$rst['msg'] = $model->getError();
    		$this->ajaxReturn($rst);
    	}
    	$model->where($data)->delete();
    	if (false === $model->add()) {
    		$rst['msg'] = $model->getError();
    		$this->ajaxReturn($rst);
    	}
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    /**
     * 后台添加微图解
     */
    public function doEditByAdmin(){
    	$model = D('Wei');
    	load('@.qiniu');
    	if (!empty($_FILES['pic']) && $_FILES['pic']['error'] != 4) {
    		$file_pic = $_FILES['pic'];
    		$QiniuUp = new QiniuUp();
    		$rst = $QiniuUp->upload($file_pic['tmp_name']);
    		if ($rst['rst'] == 0) {
    			$this->error($rst['err']);
    		} else {
    			$_POST['pic'] = $rst['data']['url'];
    		}
    	}
    	if (false === $model->create()) {
    		$this->error($model->getError());
    	}
    	//保存当前数据对象
    	$list = $model->save();
    	if ($list !== false) { //保存成功
    		
    		$film_id = I('filmArr');
    		if (!empty($film_id)) {
    			$film_id = array_unique($film_id); // 去除重复项
    			$film_list=D('WeiUFilm')->where(array('wei_id'=>I('id')))->getField('film_id',true);
    			$dataList = array();
    			$time = toDate(NOW_TIME);
    			foreach ($film_id as $id) {
    				if (in_array($id, $film_list)) continue;
    				$dataList[] = array('wei_id'=>I('id'), 'film_id'=>$id, 'add_time'=>$time);
    			}
    			if (!empty($dataList)) {
    				D('WeiUFilm')->addAll($dataList);
    			}
    		}
    		
    		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		$this->success('修改成功!');
    	} else {
    		//失败提示
    		$this->error('修改失败!');
    	}
    }
    
    public function volListAdmin(){
    	$wei_id = I('id');
    	if (empty($wei_id)) {$this->error('error');}
    	$map = array('wei_id'=>$wei_id);
    	$wei_vol_list = D('WeiVol')->where($map)->order('pindex asc')->select();
    	Cookie::set('_currentUrl_', __SELF__);
    	$this->assign('list',$wei_vol_list);
    	$this->display();
    }
    
    /**
     * 添加脚本
     */
    public function addVolAdmin(){
    	$wei_id = I('wei_id');
    	if (empty($wei_id)) {
    		$this->error('参数错误');
    	}
    	//step1  获取该微图解下最大的pindex
    	$map = array('wei_id'=>$wei_id);
    	$vo = D('WeiVol')->where($map)->order('pindex desc')->find();
    	$this->assign('vo',$vo);
    	$this->display();
    }
    
    /**
     * Do添加脚本
     */
    public function doAddVolAdmin(){
    	$intros = I('intro');
    	$wei_id = I('wei_id');
    	if (empty($wei_id) || !is_array($intros)) {
    		$this->error('参数错误');
    	}
    	foreach ($intros as $key=>$intro) {
    		$file_key = 'Fileimages-'.$key;
	    	if (empty($_FILES[$file_key])) {
	    		$this->error('Error 文件不能为空那');
	    	} else {
	    		if (empty($intro) && $_FILES[$file_key]['error'] == 4) {
	    			$this->error('不能存在解说和图片同时为空的情况啊！！');
	    		}
	    	}
    	}
    	//step1  获取该微图解下最大的pindex
    	$map = array('wei_id'=>$wei_id);
    	$pindex = D('WeiVol')->where($map)->max('pindex');
    	if ($pindex === NULL) {
    		$pindex = 0;
    	} else {
    		$pindex ++;
    	}
    	$dataList = array();
    	$day_time = toDate(NOW_TIME);
    	load('@.qiniu');
    	foreach ($intros as $key=>$intro) {
    		$data = array(
    				'pindex'=>$pindex,
    				'wei_id'=>$wei_id,
    				'intro'=>$intro,
    				'add_time'=>$day_time,
    				'image' => 'http://imgs5.graphmovie.com/material/overdue.png'
    		);
    		$file_key = 'Fileimages-'.$key;
	    	if (!empty($_FILES[$file_key]) && $_FILES[$file_key]['error'] != 4) {
	    		 $file_pic = $_FILES[$file_key];
	    		 $QiniuUp = new QiniuUp();
	    		 $rst = $QiniuUp->upload($file_pic['tmp_name']);
	    		 if ($rst['rst'] == 0) {
	    		 	$this->error($rst['err']);
	    		 } else {
	    		 	 $data['image']= $rst['data']['url'];
	    		 }
	    	}
	    	$dataList[] = $data;
	    	$pindex++;
    	}
    	if (false === D('WeiVol')->addAll($dataList)) {
    		$this->error('写入数据库失败');
    	} else {
    		if (false === D('Wei')->where(array('id'=>$wei_id))->setInc('total_page',count($dataList))){
    			$this->error('添加脚本成功，请告诉BOBO更新页码失败',getCurrentUrl());
    		} else {
    			$this->success('添加成功',getCurrentUrl());
    		}
    	}
    	
    }
    
    
    /**
     * 图册
     * Enter description here ...
     */
    function material(){
    	$model = D('WeiMaterial');
    	$this->_list($model, array());
    	$this->display();
    }
    
    /**
     * 关闭图册
     */
    function closeMaterial(){
    	$id = I('id');
    	if (empty($id) || $id<=0) {$this->error('参数错误');}
    	$model = D('WeiMaterial');
    	$data = array('open'=>0,'id'=>$id);
    	if (false === $model->save($data)) {
    		$this->erro('更新失败');
    	}
    	$this->success('更新成功');
    }
    
    /**
     * 打开图册
     */
    function openMaterial(){
    	$id = I('id');
    	if (empty($id) || $id<=0) {$this->error('参数错误');}
    	$model = D('WeiMaterial');
    	$data = array('open'=>1,'id'=>$id);
    	if (false === $model->save($data)) {
    		$this->erro('更新失败');
    	}
    	$this->success('更新成功');
    }
    
    /**
     * 添加图册
     * Enter description here ...
     */
    function addMaterial(){
    	$this->display();
    }
    
    /**
     * 添加图册操作
     */
    function doAddMaterial (){
    	$model = D('WeiMaterial');
    	if (false === $model->create()){
    		$this->error($model->getError());
    	} else {
    		if (false === $model->add()) {
    			$this->error($model->getError());
    		}
    		$this->success('添加成功',getCurrentUrl());
    	}
    }
    
    function uploadMatPic(){
    	$id = I('id');
    	$map = array('id'=>$id);
    	$vo = D('WeiMaterial')->where($map)->find();
    	$this->assign('vo',$vo);
    	$this->display();
    }
    
    function doUploadMatPic(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	$this->_uploadMatPic($id);
    	$model = D('WeiMaterial');
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
	private function _uploadMatPic($id){
    	if (!empty($_FILES['img_url'])) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../wei_material/'.$id;
          
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
            $server_pre = getImgServerURL(0)."/";
    		if ($_FILES['img_url']['error'] != 4) {
            	$upload->saveRule = $saveName;
            	$fileInfo = $upload->uploadOne($_FILES['img_url']);
            	if ($fileInfo) {
            		$_POST ['img_url'] = $server_pre.'wei_material/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'img_url','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            	$nofile = false;
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (!empty($files)) {
            	$rst = sendFileToImgSevr('wei_material',$id,$files);
            	if (is_array($rst)) {
            		foreach ($files as $file) {
            			if (isset($rst['succ'][$file['key']])) {
            				$_POST['img_url'] = $rst['succ'][$file['key']]['url'];
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
    
    function editMaterial(){
    	$id = I('id');
    	$map = array('id'=>$id);
    	$vo = D('WeiMaterial')->where($map)->find();
    	$this->assign('vo',$vo);
    	$this->display();
    }
    
    function doEidtMaterial(){
    	$model = D('WeiMaterial');
     	if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
    
    /**
     * 图册图片列表
     * Enter description here ...
     */
    function materialImgList(){
    	$id = I('id');
    	$map = array('mat_id'=>$id);
    	$this->_list(D('WeiMatUImg'), $map, 'sort`,`id');
    	$list = $this->get('list');
    	foreach ($list as $key=>$item) {
    		$one = D('WeiMatImg')->where(array('id'=>$item['img_id']))->find();
    		$list[$key] = array_merge($one,$item);
    	}
    	$this->assign('list',$list);
    	$this->display();
    }
    
    /**
     * 获取图片列表
     */
    function getMatImg(){
    	$rst = array('rst'=>0,'msg'=>'');
    	$name = trim(I('name'));
    	$keywords = trim(I('keywords'));
    	$tags = trim(I('tags'));
    	$map = array();
    	if (!empty($name)) {
    		$map['name'] = array('like',"%$name%");
    	}
    	if (!empty($keywords)) {
    		$map['keywords'] = array('like',"%$keywords%");
    	}
    	if (!empty($tags)) {
    		$map['tags'] = array('like',"%$tags%");
    	}
    	$skip = I('skip',0);
    	$list = D('WeiMatImg')->where($map)->limit($skip,20)->select();
    	$rst['data']['list'] = empty($list) ? array() : $list;
    	$rst['data']['skip'] = $skip+count($list);
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    /**
     * 添加图册与素材的关系
     */
    function doAddMatUImg(){
    	$mat_id = I('mat_id',0);
    	if (empty($mat_id)) {$this->error('参数错误');}
    	unset($_POST['mat_id']);
    	$dataList = array();
    	foreach ($_POST as $key=>$item) {
    		$item['mat_id'] = $mat_id;
    		$item['add_time'] = toDate(NOW_TIME);
    		$dataList[] = $item;
    	}
    	if (false === D('WeiMatUImg')->addAll($dataList)){
    		$this->error('插入数据失败');
    	} else {
    		$count = count($dataList);
    		D('WeiMaterial')->where(array('id'=>$mat_id))->setInc('imgs',$count);
    		$this->success('插入成功');
    	}
    }
    
     /**
     * 更新图册素材关系信息
     */
    public function updateMatUImg(){
    	$sort = I('sort');
    	if (is_array($sort)) {
    		$model = D('WeiMatUImg');
    		foreach ($sort as $id=>$sort) {
    			$data = array('id'=>$id,'sort'=>$sort);
    			if (FALSE === $model->save($data)){
    				$this->error('更新'.$id.' - '.$sort.' 失败');
    			}
    		}
    		$this->success('更新成功');
    	} else {
    		$this->error('参数错误');
    	}
    }
    
    
    function doDelMatUImg(){
    	$id = I('id');
    	if (empty($id)) {$this->error('删除失败');}
    	$mat_id = D('WeiMatUImg')->where(array('id'=>$id))->getField('mat_id');
    	if (empty($mat_id)) {$this->error('获取图册ID失败');}
    	D('WeiMaterial')->where(array('id'=>$mat_id))->setDec('imgs');
    	if (false === D('WeiMatUImg')->where(array('id'=>$id))->delete()){
    		$this->error("删除失败");
    	} else {
    		$this->success("删除成功");
    	}
    }
    
    
    /**
     * 素材列表
     * Enter description here ...
     */
    public function materialImg(){
    	$model = D('WeiMatImg');
    	$this->_list($model, array());
    	$this->display();
    }
    
    /**
     * Do添加素材图片
     */
    public function doAddMatImg(){
    	$data = array();
    	foreach ($_POST as $item) {
    		if (is_array($item)) {
    			$item['add_time'] = toDate(NOW_TIME);
    			$data[] = $item;
    		}
    	}
    	if (D('WeiMatImg')->addAll($data)!== FALSE) {
    		$requist = getHttpClient('http://imgs5.graphmovie.com:8099/material/me/move.php', array());
    		$content = $requist->getContent();
    		if ($content == 'ok'){
    			$this->success('添加成功');
    		} else {
    			$this->error('添加成功，但图片移动失败，请联系波波');
    		}
    	} else {
    		$this->error('添加失败');
    	}
    }
    
    
    /**
     * 活动大家一起做微图解,直接进入微图解详情
     */
    public function active(){
    	$act_id = I('act_id',null);
    	if (empty($act_id) || !is_numeric($act_id)) {
    		$this->error('参数错误');
    	}
    	// step1 找出活动对应的微图解 （微图解下，用户ID为2并且open=0的图解为大家一起做的官方微图解）
    	$map = array('user_id'=>2,'open'=>0,'act_id'=>$act_id);
    	$one_wei = D('Wei')->where($map)->find();
    	if (empty($one_wei)) { // 如果没有对应的微图解，则添加
    		$this->display('add'); return;
    	}
    	
    	// step2 找出微图解对应的解说
    	$map = array('wei_id'=>$one_wei['id']);
    	$wei_vol_list = D('WeiVol')->where($map)->order('pindex asc')->select();
    	if (empty($wei_vol_list)) { // 不存在解说，则需要添加解说
    		$this->assign('wei',$one_wei);
    		$lines = $one_wei['total_page'];
    		$list = array();
    		for ($i=0; $i<$lines; $i++) {
    			$list[] = array('pindex'=>$i);
    		}
    		$this->assign('list',$list);
    		$this->display('addVol'); return;
    	}
    	$this->assign('list',$wei_vol_list);
    	
    	// step3 找出解说对应的用户
    	$vol_ids = array();
    	foreach ($wei_vol_list as $item) {
    		if (!empty($item['image'])) {
    			$vol_ids[] = $item['id'];
    		}
    	}
    	if (!empty($vol_ids)) {
    		$map = array('vol_id'=>array('in',$vol_ids));
    		$vol_members = D('WeiVolMember')->where($map)->getField('id,vol_id,user_id,state');
    		if (!empty($vol_members)) {
    			$member_ids = array();
    			$vol_states = array();
    			foreach ($vol_members as $id => $item) {
    				$member_ids[$item['user_id']] = $item['user_id'];
    				$vol_states[$item['vol_id']] = array('state'=>$item['state'],'id'=>$id);
    			}
    			$this->assign('vol_state',$vol_states);
    			$map = array('id'=>array('in',$member_ids));
    			$members = D('Member')->where($map)->getField('id,name,avatar');
	    		$vol_member = array();
	    		foreach ($vol_members as $id=>$item) {
	    			$vol_member[$item['vol_id']] = $members[$item['user_id']];
	    		}
	    		$this->assign('vol_member',$vol_member);
    		}
    	}
    	
    	$this->display('volList');
    }
    
    /**
     * 审核用户脚本， 这里审核的是活动微图解下的图片
     * Enter description here ...
     */
    public function shenheVol(){
    	$rst = array('rst'=>0,'msg'=>'');
    	$vol_mem_id = I('id', NULL);
    	if (empty($vol_mem_id)) {
    		$rst['msg'] = '参数错误';
    		$this->ajaxReturn($rst);
    	}
    	
    	$state = I('state', NULL);
    	if ($state == 2) { // 审核不通过
    		$map = array('id'=>$vol_mem_id);
    		$one_vol_mem = D('WeiVolMember')->where($map)->field('vol_id,user_id')->find();
    		$data = array('image'=>'');
    		$map = array('id'=>$one_vol_mem['vol_id']);
    		$WeiVol = D('WeiVol');
    		$one_vol = $WeiVol->where($map)->find();
    		if (false === $WeiVol->where($map)->save($data)){
    			$rst['msg'] = '更新脚本表失败';
    			$this->ajaxReturn($rst);
    		}
    		$data = array(
    			'vol_id'=>$one_vol_mem['vol_id'],
    			'image'=>$one_vol['image'],
    			'intro'=>$one_vol['intro'],
    			'user_id'=>$one_vol_mem['user_id']
    		);
    		$WeiVolRecode = D('WeiVolRecode');
    		$data = $WeiVolRecode->create($data);
    		if (false === $WeiVolRecode->add($data)){
    			$rst['msg'] = '插入记录表失败';
    			$this->ajaxReturn($rst);
    		}
    		$map = array('id'=>$vol_mem_id);
    		if (false === D('WeiVolMember')->where($map)->delete()){
    			$rst['msg'] = '删除用户脚本关系失败';
    			$this->ajaxReturn($rst);
    		}
    	} else if ($state == 1) {
    		$data = array('id'=>$vol_mem_id,'state'=>1);
    		if (false === D('WeiVolMember')->save($data)){
    			$rst['msg'] = '更新脚本表失败';
    			$this->ajaxReturn($rst);
    		}
    	}
    	$rst['rst'] = 1;
    	$this->ajaxReturn($rst);
    }
    
    
    /**
     * 用户上传图片，脚本审核这里审核图片是否违规
     */
    public function checkVol(){
    	$map = array('admin_id'=>$_SESSION[C('USER_AUTH_KEY')], 'state'=>0);
    	$wei_id_list = D('WeiCheck')->where($map)->getField('wei_id',true);
    	if (!empty($wei_id_list)) {
    		$rst = array();
    		$WeiVol = D('WeiVol');
    		foreach ($wei_id_list as $wei_id) {
    			$map = array('wei_id'=>$wei_id);
    			$rst[$wei_id] = $WeiVol->where($map)->field('id,image,intro,pindex')->order('pindex asc')->select();
    		}
    		$this->assign('list',$rst);
    		
    		$map = array('id'=>array('in',$wei_id_list));
    		$wei_list = D('Wei')->where($map)->getField('id,title,user_id,add_time');
    		$this->assign('wlist',$wei_list);
    		$mem_id = array();
    		foreach ($wei_list as $item) {
    			$mem_id[] = $item['user_id'];
    		}
    		$map = array('id'=>array('in',$mem_id));
    		$user_list = D('Member')->where($map)->getField('id,name,avatar');
    		$this->assign('ulist',$user_list);
    	}
    	$this->display();
    }
    
    /**
     * 审核脚本
     */
    public function doCheckVol(){
    	$wei = I('wei');
    	$rst = array('rst'=>0,'msg'=>'');
    	if (!empty($wei)) {
    		$wei_ids = array();
    		foreach ($wei as $key=>$nums) {
    			if ($nums > 0) {
    				$wei_ids[] = $key;
    			}
    		}
    		if (!empty($wei_ids)) {
    			$data = array('state'=>2,'update_time'=>toDate(NOW_TIME));
    			$map = array(
    				'wei_ids'=>array('in',$wei_ids), 
    				'admin_id'=>$_SESSION[C('USER_AUTH_KEY')]
    			);
    			if (false === D('WeiCheck')->where($map)->save($data)) {
    				$rst['msg'] = '修改审核表失败';
    				$this->ajaxReturn($rst);
    			}
    			$data = array('open'=>0);
    			$map = array('id'=>array('in',$wei_ids));
    			if (false === D('Wei')->where($map)->save($data)) {
    				$rst['msg'] = '关闭微图解失败';
    				$this->ajaxReturn($rst);
    			}
    		} 
    	}
        $data = array('state'=>1,'update_time'=>toDate(NOW_TIME)); // 审核通过
    	$map = array(
    		'state'=>0,
    		'admin_id'=>$_SESSION[C('USER_AUTH_KEY')]
    	);
    	if (false === D('WeiCheck')->where($map)->save($data)) {
    		$rst['msg'] = '修改审核表失败';
    		$this->ajaxReturn($rst);
    	}
    	$rst['rst']=1;
    	$this->ajaxReturn($rst);
    }
    
    /**
     * 拉取审核微图解 
     */
    public function getCheckVol(){
    	// step1, 查找自己是否存在未完成的图解
    	$WieCheck = D('WeiCheck');
    	$map = array('admin_id'=>$_SESSION[C('USER_AUTH_KEY')], 'state'=>0);
    	$wei_id_list = $WieCheck->where($map)->getField('wei_id',true);
    	if (!empty($wei_id_list)) {
    		$this->error('你还有没审核完成的微图解呢！');
    	}
		
    	$Wei = D('Wei');
    	$map = array('admin_id'=>0, 'open'=>1);
    	$list = $Wei->where($map)->field('id,total_page')->order('id asc')->limit(0,40)->select();
    	if (!empty($list)) {
	    	$today = toDate(NOW_TIME);
	    	$total_page = 0; // 审核的张数
	    	$max_page = C('CHECK_IMG_MAX_COUNT');
	    	$dataList = array();
    		$WeiVol = D('WeiVol');
    		$img_serv = C('CHECK_IMG_SERVER');
    		if (empty($img_serv)) {$this->error('配置文件损坏');}
    		foreach ($list as $item) {
    			$break = false;
    			$map = array('wei_id'=>$item['id']);
    			// 2015年2月6日18:24:23，因为采用官方微图解，不能只看图，也应该看解说。
//     			$images = $WeiVol->where($map)->getField('image',true);
//     			foreach ($images as $img) {
//     				// 如果存在用户上传的图片，则需要审核
//     				foreach ($img_serv as $serv) {
// 	    				if (strpos($img,$serv) !== FALSE) {
// 	    					$break = true; break;
// 	    				} 
//     				}
//     				if ($break) break;
//     			}
    			$break = true; // 这里手动设置为true
    			if ($break) {
	    			$total_page += $item['total_page'];
	    			if ($total_page>$max_page) break;
	    			$dataList[] = array(
	    				'admin_id' => $_SESSION[C('USER_AUTH_KEY')],
	    				'wei_id' => $item['id'],
	    				'state' => 0,
	    				'update_time' => $today,
	    				'add_time' => $today
	    			);
    			}
    			$data = array('id'=>$item['id'],'admin_id'=>$_SESSION[C('USER_AUTH_KEY')]);
    			if (false === $Wei->save($data)){
    				$this->error('更新 微图解信息发生错误');
    			}
    		}
    		if (empty($dataList)) { // 没找到一部微图解
    			$this->error('40条微图解中，没有发现可以审核的微图解，请再拉取一次');
    		} else { // 
    			if (false === $WieCheck->addAll($dataList)) {
    				$this->error('插入拉取表失败');
    			} else {
    				$this->success('拉取成功',U('Wei/checkVol'));
    			}
    		}
    	} else {
    		$this->error('没有未审核的微图解了');
    	}
    }
    
    /**
     * 添加脚本
     */
    public function doAddVol(){
    	$intro = I('intro',null);
    	$wei_id = I('wei_id',null);
    	$data = array();
    	if (is_array($intro)) {
    		for ($i=0, $len = count($intro); $i<$len; $i++) {
    			if (!isset($intro[$i])) {
    				$this->error('参数错误');
    			}
    			if ($_FILES['Fileimages-'.$i]['error'] != 4) {
    				$this->_uploadImg('Fileimages-'.$i, $i);
    			}
    			$data[] = array(
    				'wei_id' => $wei_id,
    				'image' => isset($_POST['image'][$i]) ? $_POST['image'][$i] : '',
    				'intro' => $intro[$i],
    				'pindex' => $i,
    				'add_time' => toDate(NOW_TIME)
    			);
    		}
    	}
    	if (false === D('WeiVol')->addAll($data)) {
    		$this->error('插入数据库失败');
    	} else {
    		$this->success('添加成功');
    	}
    }
    
	/**
     * 上传封面图片
     * Enter description here ...
     * @param unknown_type $id
     */
	private function _uploadImg($post_file, $pindex){
    	if (!empty($_FILES[$post_file])) {
    		import("@.ORG.Util.Image");
            import("@.ORG.UploadFile");
            //导入上传类
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize = 2097152;
            //设置上传文件类型
            $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
            $dir = '../active_wei';
          
    		if (!is_dir($dir)) { 
                mkdir($dir, 0777);
            }
            
            $dir .= '/';
            $upload->savePath = $dir;
            
            // 设置引用图片类库包路径
            $upload->imageClassPath = '@.ORG.Util.Image';
            
            $saveName = $post_file.'_'.date('YmdHis').'_';
            //删除原图
            $upload->thumbRemoveOrigin = true;
            
            $files = array();
            $server_pre = getImgServerURL(0)."/";
    		if ($_FILES[$post_file]['error'] != 4) {
            	$upload->saveRule = $saveName;
            	$fileInfo = $upload->uploadOne($_FILES[$post_file]);
            	if ($fileInfo) {
            		$_POST ['image'][$pindex] = $server_pre.'active_wei/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>$pindex,'file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            	$nofile = false;
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (!empty($files)) {
            	$rst = sendFileToImgSevr('active_wei',0,$files);
            	if (is_array($rst)) {
            		foreach ($files as $file) {
            			if (isset($rst['succ'][$file['key']])) {
            				$_POST['image'][$file['key']] = $rst['succ'][$file['key']]['url'];
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
     * 推荐一部微图解，进片场首页
     */
    public function doTuijian(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('error');
    	}
    	$wei = D('Wei')->getById($id);
    	if (empty($wei) || $wei['open'] == 0) {
    		$this->error ('微图解不存在或者被关闭了');
    	}
    	$model = D('JianMap');
    	$data = array('online_id'=>$id, 'online_type'=>JianMapModel::$TYPE_WEI, 'online_time'=>I('online_time'), 'pic'=>$wei['pic'], 'intro'=>$wei['intro']);
    	$model->where(array('online_id'=>$id,'online_type'=>JianMapModel::$TYPE_WEI))->delete();
    	if ($model->create($data) === false || $model->add() === false) {
    		$this->error($model->getError());
    	}
    	$this->success('推荐成功');
    }
    
    /**
     * 查看推荐列表
     */
    public function tuijianList(){
    	$map = $this->_search('JianMap');
    	$this->_list(D('JianMap'), $map,'online_time',false, false);
    	$list = $this->get('list');
    	if (!empty($list)) {
    		foreach ($list as $key=>$item) {
    			if ($item['online_type'] == JianMapModel::$TYPE_WEI){
    				$tmp = D('Wei')->where(array('id'=>$item['online_id']))->field('title')->find();
    				$list[$key]['name'] = $tmp['title'];
    			} else if ($item['online_type'] == JianMapModel::$TYPE_PAPER){
    				$tmp = D('News')->where(array('id'=>$item['online_id']))->field('name')->find();
    				$list[$key]['name'] = $tmp['name'];
    			}
    		}
    	}
    	$this->assign('list',$list);
    	$this->display();
    }
    
    /**
     * 取消推荐操作
     */
    public function cancelTuijian(){
    	$id = I('id');
    	if (empty($id)) {
    		$this->error('error');
    	}
    	if (D('JianMap')->where(array('id'=>$id))->delete() === false) {
    		$this->error('取消失败');
    	}
    	$this->success('取消成功！');
    }
}