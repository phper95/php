<?php
// 占坑模块
class PitAction extends CommonAction {
	public function _filter(&$map)
    {
        $name = trim(I('pit_name'));
        if (!empty($name)) {
        	$map['pit_name'] = array('like',"%$name%");
        } else {
        	unset($map['pit_name']);
        }
                
        $name = I('user_name');
        if (!empty($name)) {
        	$map['user_name'] = array('like',"%$name%");
        }
    }
    
    public function _before_index(){
    	$email = I('email');
    	if (!empty($email)) {
    		$id = D('Member')->where(array('email'=>$email))->getField('id');
    		if (!empty($id)) {
    			$_POST['user_id'] = $id;
    		} else {
    			$_POST['user_id'] = 0;
    		}
    	}
    }
    
	
 	public function index(){
 		load('@.pit');
 		$pit = new Movie_Pit();
 		$map = $this->_search('MemberUPit');
 		$this->_filter($map);
 		$model = D('MemberUPit');
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        
        $this->assign('pitName', $pit->getStateList());
        $this->display();
        return;
 	}
 	
 	public function add(){
 		$pitMembers = D('PitMember')->getField('user_id,user_name,avatar'); 
 		$this->assign('PitMember',$pitMembers);
 		load('@.pit');
 		$pit = new Movie_Pit();
 		$state = $pit->getOptByState(0);
 		if (!empty($state)) {
 			$this->assign('state', $state);
 		}
 		$this->display();
 	}
 	
 	public function _before_insert(){
 		$member = I('user');
 		$pit_name = I('pit_name');
 		if (empty($member)){$this->error('请选择用户');}
 		if (empty($pit_name)){$this->error('坑不能为空');}
 		$tmp = explode(',', $member);
 		$_POST['user_id'] = $user_id = $tmp[0];
 		
 		$one1 = D('PitMember')->where(array('user_id'=>$user_id))->field('allow_pit,doing_pit')->find();
		if ($one1['doing_pit']>= $one1['allow_pit']){
			$this->error('他的坑用完了');
		}
 		$_POST['user_name'] = $tmp[1];
 		$_POST['avatar'] = $tmp[2];
 		$_POST['deadline'] = date ('Y-m-d H:i:s', strtotime(date('Y-m-d')) + (30 * 24 * 3600));
 		$_POST['add_time'] = date ('Y-m-d H:i:s', NOW_TIME);
 		$_POST['state'] = 0;
 		unset($_POST['user']);
 	} 
 	
 	public function insert(){
 		$model = D('MemberUPit');
		$map = array('pit_name'=>I('pit_name'), 'state'=>array('LT',10) ); // 找到坑是否存在未完成不能占用的情况
		$one = $model->where($map)->find();
		if (!empty($one)) {
			$this->error('坑已被人捷足先登。');
		}
		
		// 如果有同名的坑，则复制它的图片位置
		$map = array('pit_name'=>I('pit_name'), 'pit_img'=>array('neq',''));
		$one = $model->where($map)->find();
		if (!empty($one)) {
			$_POST['pit_img'] = $one['pit_img'];
		}
		
 		$model->create();
 		if (false === $model->add()) {
 			$this->error('添加失败');
 		} else {
 			$data = array();
 			$user_id = I('user_id');
 			$data['doing_pit'] = array('exp',' doing_pit+1 ');
	 		D('PitMember')->where(array('user_id'=>$user_id))->save($data);
 			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
 			$this->success('申请成功');
 		}
 	}
 	
 	public function _before_edit(){
 		$id = I('id');
 		if (empty($id)) {
 			$this->error('参数错误');
 		}
 	}
 	
 	public function edit(){
 		$id = I('id');
 		$vo = D('MemberUPit')->where(array('id'=>$id))->find();
 		$this->assign('vo', $vo);
 		
 		load('@.pit');
 		$pit = new Movie_Pit();
 		$state = $pit->getOptByState($vo['state']);
 		if (!empty($state)) {
 			$this->assign('state', $state);
 		}
 		$this->assign('pitName', $pit->getStateList());
 	}
 	
 	public function addPitUser(){
 		$id = I('id');
 		$one = D('Member')->where(array('id'=>$id))->field('id,name,avatar,email')->find();
 		if (empty($one['email'])) {
 			$this->error('邮箱为空的用户不能占坑');
 		}
 		$tmp_one = D('PitMember')->where(array('user_id'=>$id))->find();
 		if (!empty($tmp_one)) {
 			$this->error('该用户已经在占坑用户里了哟。');
 		}
 		$vo['user_id'] = $one['id'];
 		$vo['user_name'] = $one['name'];
 		$vo['avatar'] = $one['avatar'];
 		$vo['allow_pit'] = 1;
 		$vo['doing_pit'] = $vo['done_pit'] = $vo['undone_pit'] = 0;
 		$this->assign('vo', $vo);
 		$this->display('editPitUser');
 	}
 	
 	public function editPitUser(){
 		$id = I('id');
 		if (empty($id)) {
 			$this->error('参数错误');
 		}
 		$vo = D('PitMember')->where(array('id'=>$id))->find();
 		$this->assign('vo', $vo);
 		$this->display();
 	}
 	
 	public function updatePitUser(){
 		$id = I('id', NULL);
 		$model = D('PitMember');
 		$model->create();
 		if (isset($id)) {
 			$bool = $model->save();
 			$str = '编辑';
 		} else {
 			$str = '添加';
 			$bool = $model->add();
 		}
 		if (false === $bool) {
 			$this->error($str.'失败');
 		} else{
 			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
 			$this->success($str.'成功');
 		}
 	}
 	
 	public function _before_update(){
 		$state = I('state', NULL);
 		if (isset($state) && $state >= 10) { // 如果是到了一个结束状态，则过期时间默认为今天00点
 			$_POST['deadline'] = date('Y-m-d 00:00:00', NOW_TIME);
 		}
 	}
 	
 	public function update(){
 		$id = I('id');
 		if (empty($id)) {
 			$this->error('参数错误');
 		}
 		$this->_uploadImg($id);
 		$model = D('MemberUPit');
 		$model->create();
 		if (false === $model->save()) {
 			$this->error('编辑失败');
 		} else {
 			$state = I('state', NULL);
 			if (isset($state)){
 				if ($state >= 10) {
 					$data = array();
 					$user_id = $model->where(array('id'=>$id))->getField('user_id');
 					$data['doing_pit'] = array('exp',' doing_pit-1 ');
	 				if ($state == 20) {
	 					$data['done_pit'] = array('exp',' done_pit+1 ');
	 				} else {
	 					$data['undone_pit'] = array('exp',' undone_pit+1 ');
	 				}
	 				D('PitMember')->where(array('user_id'=>$user_id))->save($data);
 				}
 			}
 			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
 			$this->success('编辑成功');
 		}
 	}
 	
 	
 	public function member(){
 		$map = $this->_search('PitMember');
 		$this->_filter($map);
 		$model = D('PitMember');
        if (!empty($model)) {
            $this->_list($model, $map);
        }
 		$this->display();
 	}
 	
 	/**
 	 * 填写完成对账用户列表
 	 * Enter description here ...
 	 */
 	public function contract(){
 		$map = $this->_search('ContractMember');
 		$this->_filter($map);
 		$model = D('ContractMember');
 		if (!empty($model)) {
 			$this->_list($model, $map, 'update_time', false);
 		}
 		$this->assign('stateN',array(0=>'申请中','1'=>'通过','2'=>'不通过'));
 		$this->display();
 	}
 	
 	/**
 	 * 审核操作
 	 * Enter description here ...
 	 */
 	public function examine(){
 		$id = I('id');
 		if (!is_numeric($id)) {
 			$this->error('参数错误');
 		}
 		$allow_state = array('1'=>'通过','2'=>'不通过');
 		$state = I('state');
 		if (!in_array($state, array_keys($allow_state))) {
 			$this->error('参数错误');
 		}
 		$data = array('id'=>$id, 'state'=>$state);
 		$reason = I('reason',null);
 		if (isset($reason)) {
 			$data['reason'] = $reason;
 		}
 		$rst = array('rst'=>0,'msg'=>'');
 		if (false === D('ContractMember')->save($data)){
 			$rst['msg'] = '数据库写入失败，审核失败';
 		} else {
 			$rst['rst'] = 1;
 			$rst['data']['str'] = $allow_state[$state];
 		}
 		$this->ajaxReturn($rst);
 	}
 	
 	/**
 	 * 同步用户信息，用户名，用户头像信息
 	 * Enter description here ...
 	 */
 	public function refreshInfo(){
 		$member_id = I('member_id');
 		$id = I('id');
 		if (!is_numeric($member_id) || !is_numeric($id)){
 			$this->error('参数错误');
 		}
 		$one = D('Member')->where(array('id'=>$member_id))->getField('id,name,avatar');
 		if (empty($one)) {
 			$rst['msg'] = '没有该用户';
 		} else {
	 		$data = array('id'=>$id,'user_name'=>$one[$member_id]['name'],'avatar'=>$one[$member_id]['avatar']);
	 		$model = D('ContractMember');
	 		$rst = array('rst'=>0,'msg'=>'');
	 		if (false === $model->save($data)) {
	 			$rst['msg'] = '同步失败';
	 		} else {
	 			$rst['rst'] = 1;
	 			$rst['data'] = $one[$member_id];
	 		}
 		}
 		$this->ajaxReturn($rst);
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
            $dir='../Uploads/pit';
          
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
    		if (!empty($_FILES['pit_img']) && $_FILES['pit_img']['error'] != 4) { 
            	$upload->saveRule = $saveName;
            	$fileInfo = $upload->uploadOne($_FILES['pit_img']);
            	if ($fileInfo) {
            		$_POST ['pit_img'] = 'pit/' . $fileInfo[0]['savename'];
            	} else {
	            	//捕获上传异常
                	$strerror=$upload->getErrorMsg();
                   	$this->error($strerror);
            	}
            }
    	}
    }
}