<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller {
    public function index(){
    }
    
    
    /**
     * 发送激活邮件
     * Enter description here ...
     */
    public function sendMail(){
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
    	print_r($rst);
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
}