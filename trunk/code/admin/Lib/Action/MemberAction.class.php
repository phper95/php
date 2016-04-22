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
    }
    
    function info(){
    	$this->edit();
    }
    
    function index() {
    	$this->_getList('Member');
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
    	
    	if (false !== $memberNew->addAll($data_all)) {
    		$member_id = $data['to_user_id'];
    		$data = array('id'=>$member_id,'stat_user_new_unread' => array('exp', ' stat_user_new_unread+1 '));
    		$member = D('Member');
    		$member->create($data);
			if (false === $member->save()){  // 更新用户信息
				$this->error('更新用户信息出错');
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
           
           $this->_list($model, $map);
        }
        $this->display();
        return;
    }
    
}
?>