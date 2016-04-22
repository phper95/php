<?php
// Me 个人首页修改
class MeAction extends CommonAction {
    function setInfo(){
    	$vo = D('User')->getById($_SESSION[C('USER_AUTH_KEY')]);
    	$this->assign('vo', $vo);
    	$this->display();
    }
    
    function _before_update(){
    	$_POST['id'] = $_SESSION[C('USER_AUTH_KEY')];
    }
    
    function update() {
        $this->_upload();
        $model = D('User');
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        $list = $model->save();
        if (false !== $list) {
        	if (isset($_POST['info'])) {
        		$_SESSION['headerImg'] = $_POST['info'];
        	}
            //成功提示
            $this->success('编辑成功!');
        } else {
            //错误提示
            $this->error('编辑失败!');
        }
    }
    
}