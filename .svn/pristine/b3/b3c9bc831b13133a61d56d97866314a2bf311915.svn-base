<?php
// 后台用户模块
class WantseeAction extends CommonAction {
    function _filter(&$map){
//        $map['open'] = array('gt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
    function _before_index(){
    	$_REQUEST ['_order'] = 'want_times';
    }
    
    // 查看关键字用户列表
    function members(){
    	$id = I('id');
    	if (empty($id) || !is_numeric($id) || $id <1) {
    		$this->error('擦');
    	}
    	$map = array('id'=>$id);
    	$keyword = D('Wantsee')->where($map)->getField('keyword');
    	
    	$model = D('WantseeUMember');
    	$map = array('keyword_id'=>$id);
    	$this->_list($model, $map,'add_time',false,false);
    	$tmp_list = $this->get('list');
    	$member_ids = array();
    	foreach ($tmp_list as $item) {
    		$member_ids[] = $item['user_id'];
    	}
    	
    	$member = D('Member');
    	$map = array('id'=>array('in',$member_ids));
    	$nameArr = $member->where($map)->getField('id,name,avatar');
    	$this->assign('nameArr', $nameArr);
    	$this->assign('keyword', $keyword);
    	$this->display();
    }
    
	//	恢复 状态1
    function resume() {
        //恢复指定记录
        $model = D('Wantsee');
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition,$field='open')) {
            $this->assign("jumpUrl", Cookie::get('_currentUrl_'));
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }
    
	//	禁用操作 状态0
    public function forbid() {
        $model = D('Wantsee');
        $pk = $model->getPk();
        $id = $_REQUEST [$pk];
        $condition = array($pk => array('in', $id));
        $list = $model->forbid($condition,$field='open');
        if ($list !== false) {
            $this->assign("jumpUrl", Cookie::get('_currentUrl_'));
            $this->success('操作成功');
        } else {
            $this->error('操作失败！');
        }
    }
    
}
?>