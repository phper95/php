<?php
// 用户反馈模块
class MemberAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    	$name = I('name');
    	if (!empty($name)) {
    		$map['name'] = array('like', "%$name%");
    	}
    	$mi_id = I('mi_id');
    	if (!empty($mi_id)) {
    		$map['id'] = userIdKeyDecode($mi_id);
    	}
    }
    
    function info(){
    	$this->edit();
    	$this->display();
    }
    
    function index() {
    	$this->_getList('Member');
    }
    
    /**
     * 放出小黑屋页面
     * Enter description here ...
     */
    function outBlackRoom(){
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    	$map = array('user_id'=>$id,'open'=>1);
    	$one = D('BlackRoom')->where($map)->find();
    	if (empty($one)) {
    		$this->error('此人不在小黑屋中。。。');
    	}
    	$this->display();
    }
    
    function doOutBlackRoom(){
    	$member_id = I('id');
    	$imei = D('Member')->where(array('id'=>$member_id))->getField('imei');
    	if (empty($imei)) {
    		$this->error('此人的IMEI为空，不能放出小黑屋呢。');
    	}
    	
    	$map = array('user_id'=>$member_id);
    	$model = D('BlackRoom');
    	$one = $model->where($map)->find();
    	if (!empty($one)) { // 黑屋中存在了，则修改记录
    		$one['open'] = 0;
//    		$one['reason'] = I('reason');
    		if (FALSE === $model->save($one)){
    			$this->error('修改小黑屋数据错误');
    		}
    	} else { // 黑雾中木油，则报错
    		$this->error('此人不在小黑屋中');
    	}
    	// 插入用户动态
    	$memberNew = D('MemberNew');
    	$pre_str = I('pre_str','');
    	$pre_length = mb_strlen($pre_str,'utf8');
    	$data['user_id'] = 1;
    	$data['to_user_id'] = $member_id;
    	$data['comment_content'] = $pre_str.I('comment_content');
    	$data['reply_comment_id'] = 0;
    	$data['readed'] = 0;
    	$data['reply_from'] = 'tinyblackroom';
    	$data['reply_from_data'] = 1;
    	$data['pre_length'] = $pre_length;
    	$data['secret_send'] = 0;
    	$memberNew->create($data);
    	$data = $memberNew->data();
    	if (false === $memberNew->add($data)){
    		$this->error('下发动态错误');
    	}
    	
    	$backurl = I('backurl');
    	if (!empty($backurl)) {
    		$this->assign('jumpUrl', $backurl);
    	} else {
    		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    	}
		$this->success('放出小黑屋成功');
    }
    
    /**
     * 关小黑屋页面
     * Enter description here ...
     */
    function putBlackRoom(){
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    	$map = array('user_id'=>$id,'open'=>1);
    	$one = D('BlackRoom')->where($map)->find();
    	if (!empty($one)) {
    		$this->error('此人已在小黑屋中。。。');
    	}
    	$this->display();
    }
    
    /**
     * 关小黑屋操作
     * Enter description here ...
     */
    function doPutBlackRoom(){
    	$member_id = I('id');
    	$imei = D('Member')->where(array('id'=>$member_id))->getField('imei');
    	if (empty($imei)) {
    		$this->error('此人的IMEI为空，不能关小黑屋呢。');
    	}
    	
    	$map = array('user_id'=>$member_id);
    	$model = D('BlackRoom');
    	$one = $model->where($map)->find();
    	if (!empty($one)) { // 黑屋中存在了，则修改记录
    		$one['open'] = 1;
    		$one['reason'] = I('reason');
    		if (FALSE === $model->save($one)){
    			$this->error('修改小黑屋数据错误');
    		}
    	} else { // 黑雾中木油，则插入
    		$data = array();
    		$data ['user_id'] = $member_id;
    		$data ['imei'] = $imei;
    		$data ['reason'] = I('reason');
    		$data ['open'] = 1;
    		if (false === $model->create($data)){
    			$this->error($model->getError());
    		} else if(false === $model->add()){
    			$this->error('插入小黑屋错误');
    		}
    	}
    	// 插入用户动态
    	$memberNew = D('MemberNew');
    	$pre_str = I('pre_str','');
    	$pre_length = mb_strlen($pre_str,'utf8');
    	$data['user_id'] = 1;
    	$data['to_user_id'] = $member_id;
    	$data['comment_content'] = $pre_str.I('comment_content');
    	$data['reply_comment_id'] = 0;
    	$data['readed'] = 0;
    	$data['reply_from'] = 'tinyblackroom';
    	$data['reply_from_data'] = 1;
    	$data['pre_length'] = $pre_length;
    	$data['secret_send'] = 0;
    	$memberNew->create($data);
    	$data = $memberNew->data();
    	if (false === $memberNew->add($data)){
    		$this->error('下发动态错误');
    	}
    	
    	//更改该用户全部的发言,屏蔽掉
		//影评:user_v_comment_movie
		//广告评论:user_v_comment_adv
		//影片弹幕:user_v_poptxt_movie
		//广告弹幕:user_v_poptxt_adv
		//用户留言:user_v_comment_user
		//用户留言版:user_v_new
		//求片儿榜:求片儿榜另算
		$map = array('user_id'=>$member_id);
		D('CommentMovie')->where($map)->setField('in_blackroom',1);
		D('CommentAdv')->where($map)->setField('in_blackroom',1);
		D('PoptxtMovie')->where($map)->setField('in_blackroom',1);
		D('PoptxtAdv')->where($map)->setField('in_blackroom',1);
		D('MemberComment')->where($map)->setField('in_blackroom',1);
		D('MemberNew')->where($map)->setField('in_blackroom',1);
    	
    	$backurl = I('backurl');
    	if (!empty($backurl)) {
    		$this->assign('jumpUrl', $backurl);
    	} else {
    		$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    	}
		$this->success('关入小黑屋成功');
    }
    
    
    /**
     * 查看用户动态
     * Enter description here ...
     */
    function news(){
    	$id = I('id');
    	$type = I('type');
    	if (empty($id)) {$this->error('参数错误');}
    	$memberNew = D('MemberNew');
    	if ($type == 'acc') {
    		$field = 'to_user_id';
    		$tofield = 'user_id';
    	}elseif ($type == 'to') {
    		$field = 'user_id';
    		$tofield = 'to_user_id';
    	} else{
    		$this->error('参数错误');
    	}
    	$map = array($field=>$id);
    	if (I('refrom',null) == 'notice') {
    		$map['reply_from'] = 'notice';
    	}
    	
    	$this->_list($memberNew, $map, '',false, false);
    	$list = $this->get('list'); // 获取模板变量
		
		// 获取名字
		$nameArr = array ();
		foreach ( $list as $key => $val ) {
			$nameArr [$val [$tofield]] = $val [$tofield];
		}
		if (! empty ( $nameArr )) {
			$nameArr = D ( 'Member' )->where ( array ('id' => array ('in', $nameArr ) ) )->getField ( 'id,name,avatar' );
		}
		$this->assign('field',$tofield);
		$this->assign('members', $nameArr);
    	$this->display();
    }
    
    function addNews(){
    	$this->display();
    }
    
    /**
     * 实实在在下发动态
     * Enter description here ...
     */
    function doAddNews(){
    	$memberNew = D('MemberNew');
    	$memberNew->create();
    	$data = $memberNew->data();
    	if (empty($data['comment_content'])) {
    		$this->error('内容不能为空');
    	} else if (empty($data['reply_from_data'])){
    		$this->error('类型数据不能为空');
    	}
    	$data['user_id'] = 1;
    	$data['reply_comment_id'] = 0;
    	$data['readed'] = 0;
    	$data['pre_length'] = 0;
    	$data['secret_send'] = 0;
    	
    	$to_user_id = explode(',', trim($data['to_user_id']));
    	$count = count($to_user_id);
    	if ($count<1) {$this->error('用户君~为空');}
    	if ($count>100) {$this->error('用户君最多100个哟！');}
    	
    	// 验证用户ID是否非法
    	$xxids = array();
    	foreach ($to_user_id as $to_id) {
    		if (isset($xxids[$to_id])) {$this->error($to_id.' 用户君ID重复了，亲~');}
    		if (!is_numeric($to_id)) {$this->error($to_id." 附近的用户ID错误。");}
    		$data['to_user_id'] = $to_id;
    		$data_all[] = $data;
    		$xxids[$to_id] = true;
    	}
    	
    	$map = array('id'=> array('in', $to_user_id));
    	$mids = D('Member')->where($map)->getField('id',true);
    	$mids = empty($mids) ? array() : $mids;
    	
    	$xxids = array_diff($to_user_id, $mids);
    	if (!empty($xxids)) {
    		$this->error(implode(',', $xxids)." 这几个ID数据里没有啊。");
    	}
    	
    	$rst = $memberNew->addAll($data_all); 
    	if (false !== $rst) {
    		$member_id = $data['to_user_id'];
    		$data = array('id'=>$member_id,'stat_user_new_unread' => array('exp', ' stat_user_new_unread+1 '));
    		$member = D('Member');
    		$member->create($data);
			if (false === $member->save()){  // 更新用户信息
				$this->error('更新用户信息出错');
			}
			
			$feedback_id = I('feedback_id',null);
			if (!empty($feedback_id)) {
				$data = array('id'=>$feedback_id,'new_id'=>$rst);
				$feedback = D('Feedback');
				if (false === $feedback->save($data)) {
					$this->error('更新用户反馈表失败');
				}
			}
    		
    		$backurl = I('backurl');
    		if (!empty($backurl)) {
    			$this->assign('jumpUrl', $backurl);
    		} else {
    			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
    		}
    		$this->success('添加成功');
    	} else {
    		$this->success('添加失败');
    	}
    	
    }
    
	private function _getList($name) {
    	$map = $this->_search($name);
    	 $this->_filter($map);
    	
		$s_time = $_REQUEST['s_time'];
		$e_time = $_REQUEST['e_time'];
		
		if (!empty($s_time))
			$map['add_time'] = array('EGT',$s_time);
		if (!empty($e_time)){
			$e_time .= " 23:59:59";
			$map['add_time'] = empty($map['add_time']) ? array('ELT',$e_time) : array ('BETWEEN',array($s_time, $e_time));
		}

        $model = D($name);
        if (!empty($model)) {
        	$voList = $model->where($map)->limit(20)->select();
        	$this->assign('list', $voList);
        	if (empty($voList)) {
        		$this->assign("page", "没有记录");
        	} else {
        		$allow_users = array('吴沛','管理员','马笑','郑志友');
        		if (count($voList) == 20 && in_array($_SESSION['loginUserName'],$allow_users)) {
        			if (!isset($_GET['id'])) {
        				$rst = $model->query("show table status like 'client_user';");
        				$count = $rst[0]['Auto_increment'];
        				$this->assign("page", "总用户数为 ".$count."； 注意此值仅提供参考，与实际值略差一点。");
        			} else {
        				$count = $model->where($map)->count();
        				$this->assign("page", "满足条件的记录数为 ".$count." 条");
        			}
        		} else {
        			$this->assign("page", "抱歉！最多返回 20 条数据");
        		}
        	}
           //$this->_list($model, $map);
        }
        Cookie::set('_currentUrl_', __SELF__);
        $this->display();
        return;
    }
    
}
?>