<?php
// 剧集相关操作
class MovieSeasonAction extends CommonAction {
	function getNameList(){
		$map = array();
		$NameList = D('MovieSeason')->where($map)->getField('id,name,name_tag');
//		$this->display('Index/index');
		$this->ajaxReturn($NameList);
	}
	
	function refreshMovieCount(){
		$rst = array('rst'=>0, 'msg'=>'');
		$id = I('id');
		$map = array('season_id'=>$id);
		$num = D('Movie')->where($map)->count();
		$data = array('id'=>$id, 'movie_count'=>$num);
		if (false === D('MovieSeason')->save($data)) {
			$rst['msg'] = '';
			$this->ajaxReturn($rst);
		} else {
			$rst['data'] = $num;
			$rst['rst'] = 1;
			$this->ajaxReturn($rst);
		}
	}
	
	function getByName(){
		$name = I('name');
		$list = array();
		if (!empty($name)) {
			$map = array('name'=>array('like',"%$name%"));
			$list = D('MovieSeason')->where($map)->select();
		}
		$this->ajaxReturn($list);
	}
}