<?php
class PushnoticeAction extends CommonAction {
	function selectMembers(){
		$ver = I('ver');
		if (is_numeric($ver)) $this->error('参数错误');
		$map = array('ver' => $ver);
		for ($i=0; true; $i++) {
			$member = D('Member')->where($map)->field('id,sns_bdyts_data')->limit(0,1000);
		}
	}
}