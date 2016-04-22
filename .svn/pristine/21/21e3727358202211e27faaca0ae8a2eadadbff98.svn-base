<?php
namespace Home\Controller;
class PitController extends CommonController {
	
	public function _initialize(){
		$this->checkLogin();
//		parent::_initialize();
	}
	
	/**
	 * 占坑操作
	 * Enter description here ...
	 */
	public function pitting(){
		$name = I('pit');
		$this->assign('menu',array(array('首页',U('Index/index')),array('搜索','#')));
		if (empty($name)){
			$this->error('额。。。你又调皮了。。。');
		}
		$user = session('user');
		$one1 = M('PitUser')->where(array('user_id'=>$user['id']))->field('allow_pit,doing_pit')->find();
		if ($one1['doing_pit']>= $one1['allow_pit']){
			$this->outPutError('额偶。。。你的可占坑已经用完咧。。');
		}
		
		$model = M('UserVPit');
		$map = array('pit_name'=>$name, 'state'=>array('LT',10) ); // 找到坑是否存在未完成不能占用的情况
		$one = $model->where($map)->find();
		if (!empty($one)) {
			$this->outPutError('施主，一步之遥，坑已被人捷足先登，可惜，可惜哉。');
		}
		
		// 找到名字相同的电影，复制期图片地址
		$map = array('pit_name'=>$name, 'pit_img'=>array('neq',''));
		$one = $model->where($map)->find();
		
		$data = array(
			'user_id' => $user['id'],
			'user_name' => $user['name'],
			'avatar' => $user['avatar'],
			'pit_name' => $name,
			'deadline' => getFomartDate(strtotime(date('Y-m-d'))+(864000*3)),
			'state' => 0,
			'pit_img' => isset($one['pit_img']) ? $one['pit_img'] : '',
			'add_time' => getFomartDate()
		);
		$bool = $model->add($data);
		if ($bool === FALSE) {
			$this->outPutError('占坑失败鸟');
		} else {
//			$user['allow_pit'] = $one1['allow_pit'] + 1;
			$user['doing_pit'] = $one1['doing_pit'] + 1;
			session('user', $user);
			M('PitUser')->where(array('user_id'=>$user['id']))->setInc('doing_pit');
			$this->success('申请成功', U('Pit/mypit'));
		}
	}
	
	/**
	 * 我的坑页面
	 * Enter description here ...
	 */
	public function mypit(){
		parent::_initialize();
		$user = session('user');
		$map = array('user_id'=>$user['id']);
		$pit_list = M('UserVPit')->where($map)->field('id,pit_name,pit_img,deadline,state,add_time,remark')->select();
		$pit_list = getPitInfo($pit_list);
		$doing_list = $his_list = array();
		foreach ($pit_list as $val) {
			if ($val['state'] < 10) { // 当前坑
				$doing_list[] = $val; 
			}else {
				$his_list[] = $val;
			}
		}
		$this->assign('menu', array(array('首页',U('Index/index')), array('我的坑','#')));
		$this->assign('doing_list', $doing_list);
		$this->assign('his_list', $his_list);
		$this->display('mypit');
	}
	
	/**
	 * 坑详情
	 * Enter description here ...
	 */
	public function pitInfo(){
		parent::_initialize();
		$id = I('id');
		if (empty($id)) {
			$this->outPutError('参数错误');
		}
		$one = M('UserVPit')->where(array('id'=>$id))->find();
		if (empty($one)) {
			$this->outPutError('参数错误');
		}
		
		$arr = getPitInfo(array($one));
		$one = $arr[0];
		$this->assign('one',$one);
		if ($this->_isUndo($one)) {
			$this->assign('undo',true);
		}
		$map = array('user_id'=>$one['user_id'],'state'=>array('LT',10));
		$pit_list = M('UserVPit')->where($map)->field('id,pit_name,pit_img,deadline,state,add_time')->select();
		$his_list = getPitInfo($pit_list);
		foreach ($his_list as $key=>$v) {
			if ($v['id'] == $id) {unset($his_list[$key]);break;}
		}
		$this->assign('his_list', $his_list);
		
		
		$this->assign('menu', array(array('首页',U('Index/index')), array('我的坑',U('Pit/mypit')), array('坑详情','#')));
		$this->display('pitInfo');
	}
	
	/**
	 * 弃坑
	 * Enter description here ...
	 */
	public  function undo(){
		$id = I('id');
		$remark = I('remark');
		if (empty($id) || empty($remark)){
			$this->outPutError('参数错误');
		}
		$model = M('UserVPit');
		$one = $model->where(array('id'=>$id))->find();
		if (empty($one)) {
			$this->outPutError('参数错误');
		}
		if ($this->_isUndo($one)) {
			$one['state'] = C('PIT_STATE.APPLY_UNDO');
			$one['remark'] = $remark;
			if (FALSE === $model->save($one)){
				$this->outPutError('申请失败了。这是一个BUG ['.C('BUG_B_CODE.UPDATE_USER_V_PIT').']');
			} else {
				$this->success('申请成功', U('Pit/mypit'));
			}
		} else {
			$this->outPutError('参数错误');
		}
	}
	
	
	/**
	 * 是否满足弃坑条件
	 * Enter description here ...
	 * @param unknown_type $one
	 */
	private function _isUndo($one){
		$user = session('user');
		if ($one['state'] == 1 && $user['id'] == $one['user_id']) {
			return true;
		}
		return false;
	}
	
	private function outPutError($msg){
		$url = session('_pre_url_');
		if (!empty($url)) {
			$this->error($msg,$url);
		} else {
			$this->error($msg);
		}
	}
	
}