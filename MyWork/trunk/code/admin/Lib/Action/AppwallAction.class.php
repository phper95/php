<?php
class AppwallAction extends CommonAction {
	public function openApp(){
		$app_id = I('id');
    	if (empty($app_id)) {$this->error('error');}
    	$data = array('id'=>$app_id,'open'=>1);
    	if (false === D('Appwall')->save($data)) {
    		$this->error('更新失败');
    	} else {
    		$this->success('开启成功');
    	}
	}
	
	public function closeApp(){
		$app_id = I('id');
    	if (empty($app_id)) {$this->error('error');}
    	$data = array('id'=>$app_id,'open'=>0);
    	if (false === D('Appwall')->save($data)) {
    		$this->error('更新失败');
    	} else {
    		$this->success('关闭成功');
    	}
	}
}