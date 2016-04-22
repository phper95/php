<?php
namespace Home\Controller;
class AuthorController extends CommonController {
	
	public function _initialize(){
		$this->checkLogin();
//		parent::_initialize();
	}
	
	
	public function index(){
		parent::_initialize();
		$this->assign('menu',array(array('首页',U('Index/index')),array('蜀黍的钱柜','#')));
		$user = session('user');
		$this->assign('user', $user);
		$map = array('user_id'=>$user['id']);
		$one = D('ContractUser')->where($map)->find();
		if (empty($one)) {
			$this->display();
		} else {
			$state = array(0=>'信息正在审核中，请耐心等待',1=>'审核通过',2=>'审核不通过');
			$one['state_str'] = $state[$one['state']];
			$one['realname'] = cut_str($one['realname'],1).'*';
			$one['phone'] = substr($one['phone'], 0,3).'****'.substr($one['phone'], -4,4);
			$one['id_card'] = substr($one['id_card'], 0, 6).'********'.substr($one['id_card'], -4,4);
			$tmp = explode('@', $one['email']);
			$one['email'] = substr($one['email'], 0,3).'****'.substr($tmp[0], -3,3).'@'.$tmp[1];
			$tmp = explode(',', $one['yh_card']);
			$tmp [0] = substr($tmp[0],0,6)."******".substr($tmp[0], -6,6);
			$tmp [2] = '****';
			$one['yh_card'] = implode(',', $tmp); 
			$this->assign('vo',$one);
			$this->display('ok');
		}
	}
	
	public function _before_add(){
		$fiter = array('realname'=>'真实姓名','phone'=>'手机号','id_card'=>'身份证','email'=>'邮箱','address'=>'详细地址','yh_card'=>'银行卡信息');
		$rst = array('rst'=>0, 'msg'=>'');
		foreach ($fiter as $key=>$val) {
			$tmp = I($key);
			if (empty($tmp)){
				$rst ['msg'] = $val .' 不能为空哦';
				$this->ajaxReturn($rst); exit();
			} else if ($key == 'yh_card') {
				$tmp = str_replace('，', ',', $tmp);
				$_POST['yh_card'] = $tmp;
				$tmp_x = explode(',', $tmp);
				if (count($tmp_x) != 3 || empty($tmp_x[0]) || !is_numeric($tmp_x[0]) || empty($tmp_x[1]) || empty($tmp_x[2])) {
					$rst ['msg'] = '银行卡信息格式错误';
					$this->ajaxReturn($rst); exit();
				}
			}
		}
	}
	
	public function add(){
		$rst = array('rst'=>0, 'msg'=>'');
		$model = D('ContractUser');
		$model->create();
		$data = $model->data();
		$user = session('user');
		$data['user_id'] = $user['id'];
		$data['user_name'] = $user['name'];
		$data['avatar'] = $user['avatar'];
		$data['add_time'] = getFomartDate();
		if (FALSE === $model->add($data)){
			$rst ['msg'] = '申请失败，请联系客服。';
			$this->ajaxReturn($rst); exit();
		} else {
			$rst['rst'] = 1;
			$rst['msg'] = '申请成功，请等待审核';
			$this->ajaxReturn($rst); exit();
		}
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