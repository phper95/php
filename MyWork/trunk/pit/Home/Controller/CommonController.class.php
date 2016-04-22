<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
    public function _initialize(){
    	$email = session('email');
    	if (!empty($email)) { // 如果存在则代表，注册了，还没取名。
    		$this->redirect(U('Public/initInfo'));
    	}
    	$user = session('user');
    	if (!empty($user)) {
    		$this->assign('user', $user);
    	}
    	session('_pre_url_',__SELF__);
    }
    
    /**
     * 检测登录状态
     * Enter description here ...
     */
    protected function checkLogin(){
    	$user = session('user');
    	if (empty($user)) {
    		$url = session('_pre_url_');
    		$url = empty($url) ? __SELF__ : $url;
    		$this->redirect(U('Login/index',array('url'=>urlencode($url)))); exit();
    	}
    }
}