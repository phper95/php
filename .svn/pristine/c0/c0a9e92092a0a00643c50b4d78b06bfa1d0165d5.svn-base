<?php
namespace Home\Controller;
class UpWorksController extends CommonController {
	
	public function _initialize(){
		$this->checkLogin();
//		parent::_initialize();
	}
	
	public function up(){
		$id = I('id');
		if (empty($id)){
			$this->outPutError('参数错误');
		}
		$model = M('UserVPit');
		$one = $model->where(array('id'=>$id))->find();
		if (empty($one)) {
			$this->outPutError('参数错误');
		}
		if ($this->_isUp($one)) {
			$one['jiaogao'] = 1;
			if (FALSE === $model->save($one)){
				$this->outPutError('提交失败了。这是一个BUG ');
			} else {
				$this->success('申请成功', U('Pit/mypit'));
			}
		} else {
			$this->outPutError('参数错误');
		}
	}
	

	/**
	 * 是否满足提交作品条件
	 * Enter description here ...
	 * @param unknown_type $one
	 */
	private function _isUp($one){
		$user = session('user');
		if ($one['state'] == 1 && $user['id'] == $one['user_id'] && $one['jiaogao'] == 0) {
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