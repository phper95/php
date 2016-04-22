<?php
namespace Home\Controller;
class BianjiController extends CommonController{
	static $STATE_DEFAULT = 0;  // 审核默认状态
	static $STATE_TIJIAO = 1;  // 审核提交状态
	static $STATE_SUCCESS = 2; // 审核通过状态
	static $STATE_FAILURE = 3; // 审核失败状态
	
	static $QI_STATE_JIJIAO = 1; // 签约状态，提交审核状态

	public function _initialize(){
		$this->checkLogin();
	}
	
	public function xuqian(){
		$user = session('user');
		array('user_id'=>$user['id']);
		$model = D('CteamBianji');
		$bianji = $model->where(array('user_id'=>$user['id']))->field('id,qi_state')->find();
		if (!empty($bianji)) {
			$bianji['qi_state'] = self::$QI_STATE_JIJIAO;
			if ($model->save($bianji) === false) {
				$this->error('申请失败，请稍后重试');
			} else {
				$this->success('申请成功，等待审核');
			}
		} else {
			$this->error('你不是编辑部的呀。');
		}
	}
	
	public function index(){
		parent::_initialize();
		$user = session('user');
		$this->assign('title','蜀黍科技——图解电影进基签约');
		$this->assign('menu',array(array('首页',U('Index/index')),array('进基签约','#')));
		$jinji_score = D('UserWorkMap')->where(array('user_id'=>$user['id']))->getField('jinji_score');
		if (empty($jinji_score) || $jinji_score<60) {
			$this->error('你未达到进基要求哦！', U('Index/index'));
		}
		$bianji = D('CteamBianji')->where(array('user_id'=>$user['id']))->find();
		if (!empty($bianji)) {
			$state = $this->_checkUploadImgState($bianji['state']);
			if ($state !== true) {
				$this->error($state, U('Index/index'));
			}
		}
		$this->display();
	}
	
	/**
	 * 获取协议内容
	 */
	public function getYx(){
		echo file_get_contents('ID_CARD/authorV2.0.html');
	}
	
	public function uploadImg(){
		$user = session('user');
		array('user_id'=>$user['id']);
		$this->_uploadImg($user['id']);
		$jinji_score = D('UserWorkMap')->where(array('user_id'=>$user['id']))->getField('jinji_score');
		if (empty($jinji_score) || $jinji_score<60) {
			$this->error('你未达到进基要求哦！', U('Index/index'));
		}
		$model = D('CteamBianji');
		$bianji = $model->where(array('user_id'=>$user['id']))->find();
		if (empty($bianji)) { // 不在编辑部，则插入数据
			$data = array('state'=>self::$STATE_TIJIAO, 'user_id'=>$user['id'], 'add_time'=>getFomartDate(),
					'img_1'=>I('img_1'), 'img_2'=>I('img_2'), 'open'=>0
			);
			if (false === $model->add($data)){
				$this->success('上传失败请联系我们。');
			} else {
				$this->success('上传成功，等待审核！');
			}
		} else { // 更新数据
			$state = $this->_checkUploadImgState($bianji['state']);
			if ($state === true) {
				$data = array('id'=>$bianji['id'], 'state'=>self::$STATE_TIJIAO, 'img_1'=>I('img_1'), 'img_2'=>I('img_2'));
				if (false === $model->save($data)){
					$this->success('上传失败请联系我们。');
				} else {
					$this->success('上传成功，等待审核！', U('Author/index'));
				}
			} else {
				$this->error($state);
			}
			
		}
	}
	
	private function _checkUploadImgState ($state) {
		if ($state == self::$STATE_TIJIAO) {
			return ('你已经提交过了，签约信息正在审核中。。。');
		} else if ($state == self::$STATE_SUCCESS) {
			return ('签约信息已经审核通过，无需再次提交');
		} else if ($state == self::$STATE_DEFAULT){
			return true;
		} else if ($state == self::$STATE_FAILURE) {
			return true;
		}
		return '未知状态，不可上传图片';
	}
	
	/**
	 * 上传身份证图片
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	private function _uploadImg($id = 0){
		if (!empty($_FILES)) {
			import('@.Lib.Util.Image');
			import('@.Lib.Util.UploadFile');
			//导入上传类
			$upload = new \UploadFile();
			//设置上传文件大小
			$upload->maxSize = 2097152;
			//设置上传文件类型
			$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
			$dir = 'ID_CARD/'.$id;
	
			if (!is_dir($dir)) {
				mkdir($dir, 0777);
			}
	
			$dir .= '/';
			$upload->savePath = $dir;
	
			// 设置引用图片类库包路径
			$upload->imageClassPath = '@.Lib.Util.Image';
	
			$saveName = $id.'_'.date('YmdHis').'_';
			//删除原图
			$upload->thumbRemoveOrigin = true;
	
			for ($i=1; $i<=2; $i++) {
				if (!empty($_FILES['file'.$i]) && $_FILES['$file'.$i]['error'] != 4) {
					$upload->saveRule = $saveName."_".$i;
					$fileInfo = $upload->uploadOne($_FILES['file'.$i]);
					if ($fileInfo) {
						$_POST ['img_'.$i] = 'ID_CARD/' . $id . '/' . $fileInfo[0]['savename'];
					} else {
						//捕获上传异常
						$strerror=$upload->getErrorMsg();
						$this->error($strerror);
					}
				} else {
					$this->error('文件上传错误');
				}
			}
		}
	}
}
