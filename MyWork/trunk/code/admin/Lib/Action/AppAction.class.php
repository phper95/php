<?php
// 本类由系统自动生成，仅供测试用途
class AppAction extends CommonAction {
	
	/**
	 * APP版本信息
	 */
    public function version(){
    	$map = array();
    	$this->_list(D('AppVersion'),$map);
		$this->display();
    }
    
    /**
     * 添加APP版本信息
     */
    public function addVer(){
    	$this->display();
    }
    
    /**
     * 实实在在的添加操作
     */
    public function doAddVer (){
    	
    }
    
    /**
     * 编辑APP版本信息
     */
    public function editVer (){
    	$id = I('id');
    	if (empty($id) || !is_numeric($id)) {$this->error('参数错误');}
    	$map = array('id'=>$id);
    	$vo = D('AppVersion')->where($map)->find();
    	$this->assign('vo',$vo);
    	$this->display();
    }
    
    /**
     * 更新APP版本信息 
     */
    public function updateVer(){
    	
    }
}