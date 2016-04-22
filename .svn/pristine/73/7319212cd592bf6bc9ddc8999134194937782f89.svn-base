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
		if (empty($one) || ($one['state'] != 0 || empty($one['yh_card']))) {
			if (!empty($one)) {
				$this->assign('one',$one);
			}
			// 2015年10月26日11:59:55 只有审核状态为通过的时候，才有编辑部签约信息
			if ($one['state'] == '1') {
				$jinji_score = D('UserWorkMap')->where(array('user_id'=>$user['id']))->getField('jinji_score');
				if (!empty($jinji_score) && $jinji_score>=60) {
					$bianji = D('CteamBianji')->where(array('user_id'=>$user['id']))->find();
					if (!empty($bianji)) {
						if ($bianji['qi_state'] != 0){
							$this->assign('qian',$bianji['qi_state']);
							$tmp = explode(' ', $bianji['qian_time']);
							$this->assign('qian_time', $tmp[0]);
							$this->assign('reason',$bianji['reason']);
							$this->assign('qi_state', array('未知','申请中，等待审核','正常','审核失败'));
						} else if($bianji['state'] != 0) {
							$this->assign('idcard',$bianji['state']);
							$this->assign('reason',$bianji['reason']);
							$this->assign('state', array('未知','申请中，等待审核','正常','审核失败'));
						}
					}
				}
			}
			$this->display();
		} else {
			$state = array(0=>'信息正在审核中，请耐心等待',1=>'审核通过',2=>'审核不通过');
			$one['state_str'] = $state[$one['state']];
			$one['realname'] = cut_str($one['realname'],1).'*';
			$one['phone'] = substr($one['phone'], 0,3).'****'.substr($one['phone'], -4,4);
			$one['id_card'] = substr($one['id_card'], 0, 6).'********'.substr($one['id_card'], -4,4);
			$tmp = explode('@', $one['email']);
			$one['email'] = substr($one['email'], 0,3).'****'.substr($tmp[0], -3,3).'@'.$tmp[1];
			$yh_card = explode('|', $one['yh_card']);
			$yh_card_str = array();
			foreach ($yh_card as $item) {
				$tmp = explode(',', $item);
				$tmp_x = explode('@', $tmp[0]);
				if (isset($tmp_x[1])) {
					$tmp [0] = substr($tmp_x[0],0,3)."***".substr($tmp_x[0], -3)."@".$tmp_x[1];
				} else {
					$tmp [0] = substr($tmp[0],0,6)."******".substr($tmp[0], -6,6);
				}
				$tmp [2] = '****';
				$yh_card_str[] = implode(',', $tmp);
			}
			$one['yh_card'] = implode('|', $yh_card_str); 
			$this->assign('vo',$one);
			$this->display('ok');
			
		}
	}
	
	public function _before_add(){
		$fiter = array('realname'=>'真实姓名','phone'=>'手机号','id_card'=>'身份证','email'=>'邮箱','address'=>'详细地址','yh_card'=>'支付信息');
		$rst = array('rst'=>0, 'msg'=>'');
		foreach ($fiter as $key=>$val) {
			$tmp = I($key);
			if (empty($tmp)){
				$rst ['msg'] = $val .' 不能为空哦';
				$this->ajaxReturn($rst); exit();
			} else if ($key == 'yh_card') {
				$tmp = str_replace('，', ',', $tmp);
				$_POST['yh_card'] = $tmp;
				$yh_card = explode('|', $tmp);
				foreach ($yh_card as $item) {
					$tmp_x = explode(',', $item);
					if (count($tmp_x) != 3 || empty($tmp_x[0]) || empty($tmp_x[1]) || empty($tmp_x[2])) {
						$rst ['msg'] = '支付信息格式错误';
						$this->ajaxReturn($rst); exit();
					}
				}
			}
		}
	}
	
	public function add(){
		$user = session('user');
		$rst = array('rst'=>0, 'msg'=>'');
		$model = D('ContractUser');
		$map = array('user_id'=>$user['id']);
		$one = $model->where($map)->field('id,state')->find();
		$model->create();
		$data = $model->data();
		if (empty($one)) {
			$data['add_time'] = getFomartDate();
			$data['user_id'] = $user['id'];
			$data['user_name'] = $user['name'];
			$data['avatar'] = $user['avatar'];
			$data['update_time'] = getFomartDate(); 
			$bool = $model->add($data);
		} else {
			if ($one['state'] == '1') {
				$rst['msg'] = '非法操作';
				$this->ajaxReturn($rst); return;
			}
			$data['id'] = $one['id'];
			$data['state'] = 0; // 回到审核状态
			$data['update_time'] = getFomartDate();
			$bool = $model->save($data);
		}
		
		if (FALSE === $bool){
			$rst ['msg'] = '申请失败，请联系客服。';
			$this->ajaxReturn($rst); return;
		} else {
			$rst['rst'] = 1;
			$rst['msg'] = '申请成功，请等待审核';
			$this->ajaxReturn($rst); return;
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