<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
    	$user = session('user');
    	if (!empty($user)) {
    		$url=I('url','');
    		if (!empty($url)) {
    			redirect(urldecode($url));
    		} else {
    			redirect(U('Index/index'));
    		}
    	}
        $this->display('index');
    }
	
    public function login(){
    	if (IS_POST) {
    		$rst = array('rst'=>0, 'msg'=>'');
    		$email = I('username');
    		$pwd = I('password');
    		$code = I('code');
    		if (strtolower($code) != session('ck')){
				$rst ['msg'] = '验证码错误呢！';
    			$this->ajaxReturn($rst); exit();
			}
			if (empty($email) || empty($pwd) || empty($code)) {
				$rst ['msg'] = '信息木油填满哦。';
    			$this->ajaxReturn($rst); exit();
			}
			
			$md5_pwd = md5($pwd);
			$map = array('email'=>$email);
			$one = M('ClientUser')->where($map)->field('id,email,avatar,name,secure_pwd_md5')->select();
			if (empty($one)) {
				$map = array('account'=>$email);
				$preOne = M('preRegisterUser')->where($map)->find();
				if (empty($preOne)) {
					$rst ['msg'] = '用户名错误';
    				$this->ajaxReturn($rst); exit();
				} else {
					if ($preOne['pwd'] != $md5_pwd) {
						$rst ['msg'] = '密码错误';
    					$this->ajaxReturn($rst); exit();	
					} else if ($preOne['state'] == 0){ //未激活
						$rst['rst'] = 2;
						$rst['msg'] = '账户未激活哟！<a href="javascript:void(0);" id="send_mail_now">点我激活GO</a>';
						$rst['dom_id'] = 'send_mail_now';
						$this->ajaxReturn($rst); exit();
					} else { // 已经激活
						$rst['msg'] = '登录成功';
						$rst['rst'] = 3; // 需要跳转
						$rst['url'] = U('Public/initInfo');
						session('email', $email);
						$this->ajaxReturn($rst); exit();
					}
				}
			}
			
			if (count($one) > 1 ) {
				$rst ['msg'] = '快截图，告诉管理员，你发现BUG了！ '.C('BUG_B_CODE.TOO_MANY_USER');
    			$this->ajaxReturn($rst); exit();
			} else {
				if ($one[0]['secure_pwd_md5'] != $md5_pwd){
					$rst ['msg'] = '密码错误';
    				$this->ajaxReturn($rst); exit();
				}
			}
			unset($one[0]['secure_pwd_md5']); // 把密码注销掉
			
			$pit_info = $this->_getUserPit($one[0]);
			if (!is_array($pit_info)) {
				$rst ['msg'] = '快截图，告诉管理员，你发现BUG了！ '.$pit_info;
    			$this->ajaxReturn($rst); exit();
			}
			
			$one[0] = array_merge($pit_info,$one[0]);
			
			// 记录是否是PCM用户
			$one[0]['gms'] = D('PcmakerWork')->where(array('user_id'=>$one[0]['id']))->count();
			
			session('user',$one[0]); // 记录session
			$rst['rst'] = 1;
			$rst['msg'] = '登录成功';
			$this->ajaxReturn($rst); exit();
    	}
    	redirect(U('Index/index'));
    }
    
    public function logout(){
    	$user = session('user');
    	if (!empty($user)) {
    		session('user', NULL);
    	}
    	redirect(U('Index/index'));
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