<?php
namespace Home\Controller;
class IndexController extends CommonController {
    public function index(){
    	// 判断是否有接收到userid参数
    	$userid = I('userid', NULL);
    	$md = I('md', null);
    	if (!empty($userid) && !empty($md)) {
    		if ($md == md5($userid.C('USER_ID_KEY'))) {
    			load('@.caesar');
    			$userid = userIdKeyDecode($userid);
    			$map = array('id'=>$userid);
    			$one = M('ClientUser')->where($map)->field('id,email,avatar,name')->find();
    			
    			$pit_info = $this->_getUserPit($one);
				if (!is_array($pit_info)) {
					$this->error('快截图，告诉管理员，你发现BUG了！');
				}
				
				$one = array_merge($pit_info,$one);
    			session('user',$one);
    			redirect(U('Index/index'));
    		}
    	}
    	if (isset($_GET['look'])) {
    		$this->assign('menu',array(array('首页','javascript:void(0);')));
    	} else {
    		$this->assign('menu',array(array('首页',U('Index/index'))));
    	}
//    	$map = array('state'=>array('between','1,9'));
		$map = array('state'=>1);
		$list = M('UserVPit')->where($map)->order('add_time desc')->limit(0,20)->select();
		$list = getPitInfo($list);
		$this->assign('list',$list);
        $this->display('index');
    }
    
    public function getMore(){
    	$add_time = I('time');
    	$map = array('state'=>1);
    	if (!empty($add_time)) {
    		$map['add_time'] = array('LT', $add_time);
    	}
		$list = M('UserVPit')->where($map)->field('id,user_name,pit_name,pit_img,add_time,deadline,state')->order('add_time desc')->limit(0,20)->select();
		$list = getPitInfo($list);
		$this->ajaxReturn($list);
    }
    
    public function search(){
    	$search_words = I('search','');
    	if (!empty($search_words)) {
    		$this->assign('menu',array(array('首页',U('Index/index')),array('搜索','#')));
    		//2014年7月28日18:50:59 修改为模糊搜索
    		$search_words = addslashes($search_words);
    		$map = array('pit_name'=>array('like','%'.$search_words.'%')); // 找到不在结束状态的坑
//    		$map = array('pit_name'=>$search_words); // 找到不在结束状态的坑
    		$list = M('UserVPit')->where($map)->order('id desc')->select();
    		if (!empty($list)){ // 坑已被占据
    			foreach ($list as $one){
	    			$deadline = strtotime($one['deadline']);
	    			if ($one['state'] < 10) {
	    				if ( NOW_TIME > $deadline){
	    					deadlinePit($one['id']);
	    				}
	    			}
    			}
   				$list = getPitInfo($list);
   				$this->assign('list', $list);
    		}
    		
    		$map = array('pit_name' => $search_words);
    		$one = M('UserVPit')->where($map)->find();
    		if (!empty($one)) {
    			$this->assign('one',$one['state']);
    		}
    		
    		$this->display('search');
    	} else {
    		$this->redirect(U('Index/index'));
    	}
    }
    
	/**
     * 获取用户坑情况
     * Enter description here ...
     */
    private function _getUserPit($user){
    	if (!empty($user)) {
    		$model = M('PitUser');
    		$one = $model->where(array('user_id'=>$user['id']))->find();
    		if (empty($one)) { // 如果不是占坑用户，则插入
    			$one = array(
    				'user_id' => $user['id'],
    				'user_name' => $user['name'],
    				'avatar' => $user['avatar'],
    				'allow_pit' => C('ALLOW_USER_PIT_NUM'),
    				'doing_pit' => 0,
    				'done_pit' => 0,
    				'undone_pit' => 0,
    				'add_time' => getFomartDate()
    			);
    			if (false === $model->add($one)){
    				return C('BUG_B_CODE.INSERT_PIT_USER');
    			}
    		}
    		unset($one['user_id'], $one['user_name'], $one['add_time'], $one['avatar']);
    		return $one;
    	}
    	return null;
    }
}