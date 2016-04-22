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
		if (empty($one) || ($one['pay_state'] != 0 || empty($one['pay_info']))) {
			if (!empty($one)) {
				$one['pay_info'] = json_decode($one['pay_info'],true);
				$one['pay_type'] = isset($one['pay_info']['zfb']);
				$this->assign('one',$one);
			}
			$this->assign('bank',C('BANK_LIST'));
			$this->display();
		} else {
			$state = array(0=>'信息正在审核中，请耐心等待',1=>'审核通过',2=>'审核不通过');
			$one['state_str'] = $state[$one['pay_state']];
			$one['realname'] = cut_str($one['realname'],1).'*';
			$one['phone'] = substr($one['phone'], 0,3).'****'.substr($one['phone'], -4,4);
			$one['id_card'] = substr($one['id_card'], 0, 6).'********'.substr($one['id_card'], -4,4);
			$tmp = explode('@', $one['email']);
			$one['email'] = substr($one['email'], 0,3).'****'.substr($tmp[0], -3,3).'@'.$tmp[1];
			$tmp = explode(',', $one['yh_card']);
			if (!empty($tmp)) {
				$tmp [0] = substr($tmp[0],0,6)."******".substr($tmp[0], -6,6);
				$tmp [2] = '****';
				$one['yh_card'] = implode(',', $tmp);
			} 
			$pay_info = json_decode($one['pay_info'],true);
			if (!empty($pay_info)) {
				if (!empty($pay_info['yhc'])){$pay_info['yhc'] = substr($pay_info['yhc'],0,6)."******".substr($pay_info['yhc'], -6,6);}
				if (!empty($pay_info['khh'])) {}
				if (!empty($pay_info['khzh'])) {}
				if (!empty($pay_info['zfb'])) {
					$tmp = explode('@', $pay_info['zfb']);
					$pay_info['zfb'] = substr($tmp[0], 0,3).'****'.substr($tmp[0], -3,3).(isset($tmp[1])? ('@'.$tmp[1]) :'');
				}
			}
			$one['pay_info'] = $pay_info;
			$one['pay_type'] = isset($pay_info['zfb']);
			$this->assign('vo',$one);
			$this->display('ok');
		}
	}
	
	public function _before_add(){
		$pay_type = I('pay_type',0);
		$rst = array('rst'=>0, 'msg'=>'');
		$fiter = array('realname'=>'真实姓名','phone'=>'手机号','id_card'=>'身份证','email'=>'邮箱','address'=>'详细地址');
		if ($pay_type == 0) { // 银行卡
			$fiter['yh_card_num'] = '银行卡号';
			$fiter['bank'] = '开户行';
			$fiter['yh_card_f_hang'] = '开户支行';
		} else if ($pay_type == 1) {
			$fiter['zfb'] = '支付宝账号';
		} else {
			$rst ['msg'] = 'error';
			$this->ajaxReturn($rst); exit();
		}
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
		
		if ($pay_type == 0) {
			$_POST['pay_info'] = JSON(array('yhc'=>$_POST['yh_card_num'],'khh'=>$_POST['bank'],'khzh'=>$_POST['yh_card_f_hang']));
		} else {
			$_POST['pay_info'] = JSON(array('zfb'=>$_POST['zfb']));
		}
		
		unset($_POST['pay_type'],$_POST['yh_card_num'],$_POST['bank'],$_POST['yh_card_f_hang'],$_POST['zfb']);
	}
	
	public function add(){
		$rst = array('rst'=>0, 'msg'=>'');
		$user = session('user');
		$model = D('ContractUser');
		$map = array('user_id'=>$user['id']);
		$id = $model->where($map)->getField('id');
		$model->create();
		$data = $model->data();
		$data['pay_info'] = $_POST['pay_info']; // 这里不对POST pay_info 处理，因为这个字段是自己生成的
		if (empty($id)) {
			$data['add_time'] = getFomartDate();
			$data['user_id'] = $user['id'];
			$data['user_name'] = $user['name'];
			$data['avatar'] = $user['avatar'];
			$bool = $model->add($data);
		} else {
			$data['id'] = $id;
			$data['pay_state'] = 0; // 回到审核状态
			$bool = $model->save($data);
		}
		
		if (FALSE === $bool){
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