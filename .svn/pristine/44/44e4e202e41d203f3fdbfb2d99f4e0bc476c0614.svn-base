<?php
// 活动相关操作
class ActiveAction extends CommonAction {
	
	private $_rewardType = array('1'=>'金币奖励','2'=>'经验奖励','10'=>'线下奖励','11'=>'自定义奖励');
	
	public function _filter(&$map)
    {
        $name = I('name');
        if (!empty($name)) {
        	$map['name'] = array('like',"%$name%");
        }
    }
    
	public function _before_index(){
		$cat_list = D('ActiveCategory')->getField('id,name');
		foreach ($cat_list as $key=>$name) {
			$cat_list[$key] = str_replace('<br/>','', $name);
		}
		$this->assign('catList',$cat_list);	
	}
	
	public function _before_add(){
		$cat_list = D('ActiveCategory')->getField('id,name');
		$this->assign('catList',$cat_list);	
    }
    
	public function _before_edit(){
		$cat_list = D('ActiveCategory')->getField('id,name');
		$this->assign('catList',$cat_list);
    }
    
    public function _after_edit(){
    	$vo = $this->get('vo');
    	if (!empty($vo)) {
    		$state = $vo['state'];
    	}
    	$this->display();
    }
	
	public function add(){
		$this->display();
	}
	
	public function _before_insert(){
		$state = I('state');
		if (is_array($state)) {
			unset($_POST['state']);
			$_POST['state'] = array_sum($state);
		}
		$_POST['open'] = 0;
		$this->_fiter_time(array('start_time','end_time'));
	}
	
	public function _before_update(){
		$state = I('state');
		if (is_array($state)) {
			unset($_POST['state']);
			$_POST['state'] = array_sum($state);
		}
		$this->_fiter_time(array('start_time','end_time'));
	}
	
	public function uploadImg (){
		$id = I('id');
    	if (empty($id)) {
    		$this->error('参数错误');
    	}
    	
    	$vo = D('Active')->field('id,name,pic')->getById($id);
    	if (empty($vo)) {
    		$this->error('参数错误');
    	}
    	$this->assign('vo', $vo);
    	$this->display();
	}
	
	/**
	 * 打开活动
	 */
	public function openActive(){
		$act_id = I('id');
    	if (empty($act_id)) {$this->error('error');}
    	$data = array('id'=>$act_id,'open'=>1);
    	if (false === D('Active')->save($data)) {
    		$this->error('更新失败');
    	} else {
    		$this->success('关闭成功');
    	}
	}
	/**
	 * 关闭活动 
	 */
	public function closeActive(){
		$act_id = I('id');
    	if (empty($act_id)) {$this->error('error');}
    	$data = array('id'=>$act_id,'open'=>0);
    	if (false === D('Active')->save($data)) {
    		$this->error('更新失败');
    	} else {
    		$this->success('关闭成功');
    	}
	}
	
	/**
	 * 活动奖励列表
	 */
	function reward(){
		$active_id = I('act_id');
		if (empty($active_id)) {
			$this->error('失败');
		}
		$map = array('activ_id'=>$active_id);
		$list = D('ActiveReward')->where($map)->select();
		$this->assign('list',$list);
		$this->assign('typeList', $this->_rewardType);
		$this->display();
	}
	
	/**
	 * 添加活动奖励
	 */
	function addReward(){
		$this->assign('typeList', $this->_rewardType);
		$this->display();
	}
	
	/**
	 * Do添加活动奖励
	 */
	function doAddReward(){
		$model = D('ActiveReward');
		if ($model->create() === FALSE) {
			$this->error($model->getError());
		}
		if ($model->add() === FALSE) {
			$this->error($model->getError());
		}
		$this->success('添加成功');
	}
	
	/**
	 * 添加活动奖励
	 */
	function editReward(){
		$id = I('id');
		if (empty($id)) {$this->error('参数错误');}
		$map = array('id'=>$id);
		$vo = D('ActiveReward')->where($map)->find();
		$this->assign('vo',$vo);
		$this->assign('typeList', $this->_rewardType);
		$this->display();
	}
	
	/**
	 * 编辑奖励
	 */
	function doEditReward(){
		$backurl = I('backurl');
		$model = D('ActiveReward');
		if ($model->create() === FALSE) {
			$this->error($model->getError());
		}
		if ($model->save() === FALSE) {
			$this->error($model->getError());
		}
		$this->success('编辑成功',$backurl);
	}
	
	/**
	 * 删除奖励
	 */
	function doDeleteReward(){
		$id = I('id');
		if (empty($id)) {$this->error('参数错误');}
		$map = array('id'=>$id);
		if(false  === D('ActiveReward')->where($map)->delete()){
			$this->success('删除失败');
		}else {
			$this->success('删除成功');
		}
	}
	
	/**
	 * 管理题库
	 */
	function manageExam(){
		$model = D('GameQuestion');
		$this->_list($model, array());
		$list = $this->get('list');
		$script = array();
		foreach ($list as $key=>$item) {
			$list[$key]['fonts'] = explode('#@#', $item['answer_fonts']);
			$list[$key]['names'] = explode('#@#', $item['answer_names']);
			$tmp_fonts = $list[$key]['fonts'];
			$count = count($tmp_fonts); 
			if ($count > 24) {
				asort($tmp_fonts);
				foreach ($tmp_fonts as $key2 => $font) {
					if ($count>24 && false === strpos($font, $item['answer'])){
						$count--;
						unset($tmp_fonts[$key2]);
					}
				} 
				ksort($tmp_fonts);
			}
			$list[$key]['script'] = array(
				"a"=>$item['answer'],
				'l'=>mb_strlen($item['answer'],'utf-8'),
				'f'=>"'".implode("','", $tmp_fonts)."'"
			);
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	
	function doChooseExam(){
		$ids = I('ids');
		if (empty($ids)) {$this->error('参数错误');}
		$model = D('GameQuestion');
		foreach ($ids as $id) {
			$data = array('id'=>$id, 'open'=>0);
			$check = I('check-'.$id,null);
			if (isset($check)){
				$data['open'] = 1;
			}
			if (FALSE === $model->save($data)){
				$this->error('修改ID '.$id." 失败");
			}
		}
		$this->success('成功');
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
    	$model = D('Active');
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
            $dir = '../active/'.$id;
          
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
    		if (!empty($_FILES['pic']) && $_FILES['pic']['error'] != 4) { 
            	$upload->saveRule = $saveName;
            	$fileInfo = $upload->uploadOne($_FILES['pic']);
            	if ($fileInfo) {
            		$_POST ['pic'] = $server_pre.'active/' . $id . '/' . $fileInfo[0]['savename'];
            		$files[] = array('key'=>'pic','file'=>$fileInfo[0]['savepath'].$fileInfo[0]['savename']);
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            	$nofile = false;
            }
            
    		// 2014年8月28日16:40:32 文件上传至图片服务器
            if (empty($files)) {
            	$this->error('没选择文件，就不要提交了嘛~~~');
            } else {
            	$rst = sendFileToImgSevr('active',$id,$files);
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
}