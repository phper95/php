<?php
class FilmAction extends CommonAction {
	function getByName(){
		$name = I('name');
		$list = array();
		if (!empty($name)) {
			$map = array('name'=>array('like',"%$name%"));
			$list = D('Film')->where($map)->field('id,name,face,showtime')->select();
		}
		$this->ajaxReturn($list);
	}
}