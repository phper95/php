<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function index(){
    }
    
    public function ck(){
		$_vc = new \Home\Lib\Util\ValidateCode(80,40);
		$_vc->doimg();
		session('ck',$_vc->getCode());
    }
    
    public function regist(){
    	if (IS_POST) {
    		$rst = array('rst'=>0,'msg'=>'');
    		$account = I('username');
    		$pwd = I('password');
    		$repwd = I('repassword');
    		$code = I('code');
    		if (empty($account) || empty($pwd) || empty($code)) {
    			$rst ['msg'] = '参数错误';
    			$this->ajaxReturn($rst); exit();
    		}
    		if ($pwd !== $repwd) {
    			$rst ['msg'] = '两次密码不一致哦';
    			$this->ajaxReturn($rst); exit();
    		}
    		
			if (strtolower($code) != session('ck')){
				$rst ['msg'] = '验证码错误呢！';
    			$this->ajaxReturn($rst); exit();
			}
			
			$map = array('email'=>$account);
			$clientUser = M('ClientUser')->where($map)->find();
			if (!empty($clientUser)) {
				$rst ['msg'] = '邮箱已经注册，<a href="http://ser3.graphmovie.com/appweb/pwd/forget.php">忘记密码咧？</a>';
    			$this->ajaxReturn($rst); exit();
			}
			
			$map = array('account'=>$account);
			$model = M('preRegisterUser');
			$registUser = $model->where($map)->find();
			if (!empty($registUser)) {
				$rst ['msg'] = '邮箱已经注册，<a href="javascript:void(0);" id="send_mail_now">立即激活？</a>';
    			$this->ajaxReturn($rst); exit();
			}
			
			$data = array('account'=>$account,'pwd'=>md5($pwd),'add_time'=>getFomartDate());
			$id = $model->add($data);
			if (false === $id){
				$rst ['msg'] = '错误鸟？联系管理员吧。';
    			$this->ajaxReturn($rst); exit();
			}
			
			$rst['rst'] = 1;
			$rst['msg'] = '注册成功！正在发送激活邮件。。。';
			$this->ajaxReturn($rst); exit();
			
//			$msg = $this->_sendJHMail($account);
//			if ($msg === true) {
//				$rst ['rst'] = 1;
//				$rst ['msg'] = '激活邮件已发送到邮箱，请登录邮箱进行激活，<a href="'.U('Index/index').'"> 再次发送？</a>';
//	    		$this->ajaxReturn($rst); exit();
//			} else {
//				$model->where ('id='.$id)->delete();
//				$rst ['msg'] = empty($msg) ? $msg : '邮件发送失败。';
//	    		$this->ajaxReturn($rst); exit();
//			}
    	}
    	$this->display('regist');
    }
    
    /**
     * 激活操作
     * Enter description here ...
     */
    public function actEmail(){
    	$key = I('k');
    	$account = authcode($key,'DECODE',C('EMAIL_KEY'));
    	if (empty($account)) {
    		$this->error('链接已经失效。。。',U('Index/index'),5);
    	} else {
    		$map = array('account'=>$account);
    		$model = M('preRegisterUser');
    		$one = $model->where($map)->find();
    		if (empty($one)) {
    			$this->error('链接已经失效。。。',U('Index/index'),5);
    		} else {
    			$one['state'] = 1;
    			if (false === $model->save($one)) {
    				$this->error('啊！！！激活失败鸟。。。。',U('Index/index'),5);
    			}
    			$this->success('激活成功。。。',U('Public/initInfo'));
    			session('email',$account);
    		}
    	}
    }
    
    /**
     * 修改自己昵称
     * Enter description here ...
     */
    public function initInfo(){
    	if (IS_POST) {
    		$rst = array('rst'=>0, 'msg'=>'');
    		$email = session('email');
    		if (empty($email)) {
    			$rst ['msg'] = '啊！！！';
    			$this->ajaxReturn($rst); exit();
    		}
    		
    		$map = array('account'=>$email);
			$preModel = M('preRegisterUser');
			$pwd = $preModel->where($map)->getField('pwd');
			if (empty($pwd)) {
				$rst ['msg'] = '啊！！！请重新注册';
    			$this->ajaxReturn($rst); exit();
			}
    		
    		$map = array('email'=>$email);
			$one = M('ClientUser')->where($map)->find();
    		if (!empty($one)) {
    			$rst['rst'] = 2;
				$rst['msg'] = '你已经登录。。。！';
				$this->ajaxReturn($rst); exit();
			}
    		
    		$name = I('name',null);
    		if (empty($name)) {
    			$rst ['msg'] = '昵称不能为空哦';
    			$this->ajaxReturn($rst); exit();
    		}
    		$name = trim($name);
			$msg = isBadword($name, C('NOT_ALLOW_NAME'));
			if ($msg === true) {
				// 检查数据库中是否已经存在
				$map = array('name'=>$name);
				$model = D('ClientUser');
				$one = $model->where($map)->find();
				if (!empty($one)) {
					$rst['msg'] = '昵称已存在啦，请改用其他昵称吧！';
					$this->ajaxReturn($rst); exit();
				}
				
				$data = array('email'=>$email, 'name'=>$name, 'secure_pwd_md5'=>$pwd);
				$model->create($data);
				$data = $model->data();
				$id = $model->add();
				if (false === $id) {
					$rst['msg'] = '啊！！！取名失败了。。。快联系管理员。';
					$this->ajaxReturn($rst); exit();
				}
				$user = array(
					'email' => $data['email'],
					'avatar' => $data['avatar'],
					'name' => $data['name'],
					'id' => $id
				);
				$pit_info = $this->_getUserPit($user);
				if (!is_array($pit_info)) {
					$rst ['msg'] = '快截图，告诉管理员，你发现BUG了！ '.$pit_info;
	    			$this->ajaxReturn($rst); exit();
				}
				
				$user = array_merge($pit_info,$user);
				session('user', $user);
			} else {
				$rst['msg'] = $msg;
				$this->ajaxReturn($rst); exit();
			}
			
			$map = array('account'=>$email);
			$preModel->where($map)->delete();
			session('email',NULL); // 注销email session
			
			$rst['rst'] = 1;
			$rst['msg'] = 'O(∩_∩)O~ 取名成功了哦！！！';
    		$this->ajaxReturn($rst); exit();
    	} 
    	
    	$email = session('email');
    	if (empty($email)) {
    		redirect(U('Index/index'));
    	}
    	$map = array('email'=>$email);
		$one = M('ClientUser')->where($map)->find();
    	if (!empty($one)) { 
    		redirect(U('Index/index'));
		}
    	$this->display('initInfo');
    }
    
    /**
     * 发送激活邮件
     * Enter description here ...
     */
    public function sendJHMail(){
    	$email = I('email');
    	$rst = array('rst'=>0,'msg'=>'错误');
    	if (!empty($email)) {
    		$map = array('account'=>$email);
    		$one = M('preRegisterUser')->where($map)->find();
    		if (!empty($one)) {
    			$msg = $this->_sendJHMail($email);
    			if ($msg === true) {
    				$rst['rst'] = 1; $rst['msg'] = '成功';
    			}else {
    				$rst['msg'] = empty($msg) ? '失败' : $msg;
    			}
    		}
    	}
    	$this->ajaxReturn($rst);
    }
    
    /**
     * 发送激活邮件到制定邮箱
     * Enter description here ...
     * @param unknown_type $email
     */
    private function _sendJHMail($email){
    	import('@.Lib.Util.Mail');
	    $mail = new \Mail();
		if (!$mail->validate_email($email)){
			return '邮箱错误';
		}
    	$key = authcode($email,'ENCODE',C('EMAIL_KEY'),7500);
		$url = U('Public/actEmail', array('k'=>($key)), true, true);
		$body = 'Hi~  '
			  . '<br />&nbsp;&nbsp;恭喜你注册【图解电影】账号成功，如非本人操作，请忽略此邮件。'
			  . '<br />&nbsp;&nbsp;请点击下面的链接进行激活，2小时内有效 （如果不能打开，请复制下面链接到浏览器中直接打开）'
			  . "<br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   "
			  . '<a href="'.$url.'">'.$url.'</a>'
			  . "<br /><br /><br />本邮件是系统自动发送的，请勿直接回复！感谢您的访问，祝您使用愉快！";
		
		if ($mail->send ( '【图解电影】', $email, '账户激活', $body )) {
			return true;
		} else {
			return $mail->getErrorInfo();
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