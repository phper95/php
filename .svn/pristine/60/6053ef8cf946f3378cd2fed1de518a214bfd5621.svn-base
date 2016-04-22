<?php
// 用户反馈模块
class FeedbackAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
	public function index() {
		$map = $this->_search('Feedback');
		$s_time = $_REQUEST['s_time'];
		$e_time = $_REQUEST['e_time'];
		
		if (!empty($s_time))
			$map['add_time'] = array('EGT',$s_time);
		if (!empty($e_time)){
			$e_time .= " 23:59:59";
			$map['add_time'] = empty($map['add_time']) ? array('ELT',$e_time) : array ('BETWEEN',array($s_time, $e_time));
		}

        $model = D('Feedback');
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $list = $this->get('list');
        $Member = D('Member');
        $members = array();
        foreach ($list as $tmp) {
        	if (isset($members[$tmp['user_id']])) continue;
        	$map = array('id'=>$tmp['user_id']);
        	$rst = $Member->where($map)->getField('id,name,avatar');
        	if ($rst) {
        		$members [$tmp['user_id']] = $rst[$tmp['user_id']];
        	}
        }
        $this->assign('Member',$members);
        
        $this->assign('type',array('0'=>'未知','1'=>'BUG', '2'=>'意见'));
        
        $this->display();
        return;
    }
    
    /**
     * 获取反馈回复内容
     * Enter description here ...
     */
    function getFeedbackContent(){
    	$id = I('id');
    	$rst = array('rst'=>0,'msg'=>'获取失败');
    	if (!empty($id)) {
    		$map = array('id'=>$id);
    		$one = D('MemberNew')->where($map)->getField('id,comment_content,add_time');
    		if (!empty($one)) {
    			$rst['rst'] = 1;
    			$rst['data'] = $one[$id];
    		}
    	}
    	$this->ajaxReturn($rst);
    }
    
    
    
}
?>