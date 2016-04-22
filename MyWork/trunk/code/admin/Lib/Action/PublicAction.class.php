<?php

class PublicAction extends Action {
    public function main() {
        $this->checkUser();
        
    }

    // 用户登录页面
    public function login() {
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            //如果系统已安装，则删除安装文件包install
            if(is_file("../install/install.lock")){
                import('@.ORG.Dir');
                Dir::delDir('../install');
            }
            $this->display();
        }else{
            $this->redirect('Index/index');
        }
    }

    public function index() {
        //如果通过认证跳转到首页
        redirect(__APP__);
    }

    // 用户登出
    public function logout() {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
            $this->success('登出成功！',U('Public/login'));
        }else {
            $this->error('已经登出！');
        }
    }

    // 登录检测
    public function checkLogin() {
        
        if(empty($_POST['account'])) {
            $this->error('用户名不能为空！');
        }elseif (empty($_POST['password'])){
            $this->error('密码不能为空！');
        }
        elseif (empty($_POST['verify'])){
            $this->error('验证码不能为空！');
        }
        if(!extension_loaded('curl')){    
            $this->error('抱歉，您的服务器，还不支持curl扩展，请配置后登录');
        }
        //生成认证条件
        $map=array();
        // 支持使用绑定帐号登录
        $map['account']	= $_POST['account'];
        $map["status"]=array('gt',0);
        if($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->error('验证码错误！');
        }
        import ( '@.ORG.Util.RBAC' );
        $authInfo = RBAC::authenticate($map);
        
        //使用用户名、密码和状态的方式进行认证
        if(false == $authInfo) {
            $this->error('用户名或密码错误！');
        }else {
            $error=D('Set')->find();
            $errorcount=$error['errorcount'];
            $errorinterval=$error['errorinterval'];

            $ip=get_client_ip();
            $time=time();
            $error_count=$authInfo['error_count'];
            //ip相同
            if($authInfo['last_login_ip']==$ip && ($authInfo['error_count']>$errorcount-1)){
               
                if(($time-$authInfo['error_login_time'])<$errorinterval){
                    $this->error('用户名或密码错误超过'.$errorcount.'次，请'.($errorinterval/60).'分钟后再试！');
                }  else {
                    D('User')->where($map)->setField('error_count',0);
                    $error_count=0;
                }
            }
            if($authInfo['password'] != md5($_POST['password'])) {
                D('User')->where($map)->setInc('error_count',1);//密码错误次数
                D('User')->where($map)->setField('error_login_time',$time);
                $this->error('用户名或密码错误，您还有'.($errorcount-$error_count).'次尝试机会！');
            }
            
            $_SESSION[C('USER_AUTH_KEY')]=$authInfo['id'];
            $_SESSION['email']=$authInfo['email'];
            $_SESSION['loginUserName']=$authInfo['nickname'];
            $_SESSION['lastLoginTime']=$authInfo['last_login_time'];
            $_SESSION['login_count']=$authInfo['login_count'];
            $_SESSION['headerImg'] = $authInfo['info'];
            if($authInfo['role_id']==0) {
                $_SESSION['administrator']=true;
            }
            //保存登录信息
            $User=M('User');
            $data = array();
            $data['id']=$authInfo['id'];
            $data['last_login_time']=$time;
            $data['login_count']=array('exp','login_count+1');
            $data['error_count']=0;
            $data['last_login_ip']=$ip;
            $User->save($data);

            // 缓存访问权限
            RBAC::saveAccessList();
            
            $this->success('登录成功！');
            
        }
    }
    // 检查用户是否登录
    protected function checkUser() {
        if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->assign('jumpUrl','Public/login');
            $this->error('没有登录');
        }
    }
    // 更换密码
    public function changePwd() {
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
        if(md5($_POST['verify'])!= $_SESSION['verify']) {
            $this->error('验证码错误！');
        }
        $oldpassword = I('oldpassword');
    	if (empty($oldpassword)) {
        	$this->error('旧密密码不能为空！');
        }
        
        $password = I('password');
        if (empty($password)) {
        	$this->error('新密码不能为空！');
        }
        $repassword = I('repassword');
        if ($password != $repassword) {
        	$this->error('两次密码不一样！');
        }
        
        $map=array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']=$_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']=$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User=M("User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
            $User->password=pwdHash($_POST['password']);
            $User->save();
            $this->success('密码修改成功！');
         }
    }
    
    public function profile() {
        $this->checkUser();
        $User=M("User");
        $vo=$User->getById($_SESSION[C('USER_AUTH_KEY')]);
        $this->assign('vo',$vo);
        $this->display();
    }
    // 修改资料
    public function change() {
        $this->checkUser();
        $User=D("User");
        if(!$User->create()) {
            $this->error($User->getError());
        }
        $result	=$User->save();
        if(false !== $result) {
            $this->success('资料修改成功！');
        }else{
            $this->error('资料修改失败!');
        }
    }
    //验证码
    public function verify() {
        $type=isset($_GET['type'])?$_GET['type']:'gif';
        import("@.ORG.Util.Image");
        Image::buildImageVerify(4,1,$type);
    }
    
	// 获取菜单
    public function menu() {
        $this->checkUser();
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $menu  = array();
            if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]])) {
                //如果已经缓存，直接读取缓存
                $menu=$_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];

            }else {
                //读取数据库模块列表生成菜单项
                $node=M("Node");
                $map['level']=2;
                $map['status']=1;
                $list=$node->where($map)->field('id,name,pid,title')->order('sort asc')->select();
                $accessList = $_SESSION['_ACCESS_LIST'];
                
                foreach($list as $key=>$module) {
                    if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator']) {
                        //设置模块访问权限
                        $module['access'] =   1;
                        $menu[$key]  = $module;
                    }
                }
                //缓存菜单访问
                $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=$menu;
            }
            
            $menu = outMenuNode($menu);
            $this->assign('menu',$menu);
        }
        
        
        
//        //显示站点栏目
//        $cate=new CategoryModel();
//        $this->cate=$list=$cate->getMyCategory();//加载栏目
//        $menu=$cate->getMyCategory1();//加载栏目
//        $menu=  arrToTree($menu, 0);
//        $tree=outMenuNode($menu);
//        $this->assign('tree', $tree); 
        
//        $this->display();
    }
    
}
?>