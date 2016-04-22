<?php
// 角色模块
class SiteConfigAction extends CommonAction {
	public function index(){
		$this->display();
	}
	
	/**
	 * 配置用户个人中心背景图
	 * Enter description here ...
	 */
	public function memberBg(){
		$model = D('MemberAvatarBg');
		$map = array();
		$this->_list($model, $map);
		$this->display();
	}
	
	public function editMemberBg (){
		$id = I('id',null);
		if (empty($id)) {
			$this->error('参数错误');
		}
		$map = array('id'=>$id);
		$vo = D('MemberAvatarBg')->where($map)->find();
		$this->assign('vo', $vo);
		$this->display();
	}
	
	public function doAddMemberBg(){
		$model = D('MemberAvatarBg');
		if (false === $model->create()) {
            $this->error($model->getError());
        }
		//保存当前数据对象
        $rst = $model->add();
        if ($rst !== false) { //保存成功
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('新增成功!');
        } else {
            //失败提示
            $this->error('新增失败!');
        }
	}
	
	public function doUpdateMemberBg(){
		$model = D('MemberAvatarBg');
		if (false === $model->create()) {
            $this->error($model->getError());
        }
		//	保存当前数据对象
        $rst = $model->save();
        if ($rst !== false) { //保存成功
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('编辑成功!');
        } else {
            //失败提示
            $this->error('编辑失败!');
        }
	}
	
	/**
	 * 肥皂君配置页面
	 * Enter description here ...
	 */
	public function soapVolPic(){
		$map = array();
		$model = D('SoapUMovieComment');
		$this->_list($model, $map);
		$list = $this->get('list');
		$now_day = toDate(NOW_TIME);
		foreach ($list as $key=>$item){
			$vol_ids [] = $item['vol_id'];
			if ($item['deadline'] < $now_day) {
				$list[$key]['is_deadline'] = true;
			}
		}
		$this->assign('list', $list);
		
		$map = array('id'=>array('in',$vol_ids));
		$tmp = D('MComment')->where($map)->field('id,movie_id,image,pindex')->select();
		foreach ($tmp as $item) {
			$vol_pic[$item['id']] = array('movie_id' => $item['movie_id'], 'image'=>$item['image'], 'pindex'=>$item['pindex'] + 1); 
			$movie_ids[$item['movie_id']] = true; 
		}
		
		$map = array('id' => array('in', array_keys($movie_ids)));
		$movie_info = D('Movie')->where($map)->getField('id,imgserver_use,name');
		
		foreach ($vol_pic as $key=>$item) {
			$vol_pic[$key] = array(
				'movie_id' => $item['movie_id'], 
				'image' => otherURL2ServerUrl($item['image'],$item['movie_id'],$movie_info[$item['movie_id']]['imgserver_use']),
				'movie_name' => $movie_info[$item['movie_id']]['name'],
				'pindex' => $item['pindex']
			);
		}
		
		$this->assign('vol_pic',$vol_pic);
		
		$this->display();
	}
	
	/**
	 * 添加肥皂君配置页面
	 * Enter description here ...
	 */
	public function addSoapVolPic(){
		$this->display();
	}
	
	/**
	 * 实实在在的添加操作
	 * Enter description here ...
	 */
	public function doAddSoapVolPic(){
		$keys = I('key');
		$vol_ids = I('vol_id');
		$allow_counts = I('allow_count');
		$deadlines = I('deadline');
		$movie_ids = I('movie_id');
		$add_time = toDate(NOW_TIME);
		$dataList = array();
		$model = D('SoapUMovieComment');
		$str = '';
		foreach ($keys as $k=>$key) {
			if (empty($key) || empty($vol_ids[$k]) || empty($allow_counts[$k]) || empty($deadlines[$k]) || empty($movie_ids[$k])) {
				$this->error('参数错误啊');
			}
			
			$map = array('vol_id' => $vol_ids[$k]);
			$one = $model->where($map)->getField('id');
			if (!empty($one)) {
				$str .= '电影ID '.$movie_ids[$k].' 页码ID '.$vol_ids[$k] .' 已经存在了<br />';
				continue;
			}
			
			$dataList[] = array(
				'key' => $key,
				'vol_id' => $vol_ids[$k],
				'allow_count' => $allow_counts[$k],
				'deadline' => $deadlines[$k],
//				'movie_id' => $movie_ids[$k],
				'add_time' => $add_time,
				'dis_count' => 0
			);
		}
		
		if (!empty($dataList)) {
			if (D('SoapUMovieComment')->addAll($dataList) === false) {
				$this->error('添加失败');
			} else {
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('添加成功<br >'.$str);
			}
		} else {
			$this->error('你提交的数据被猴子请去当逗比了么？');
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editSoapVolPic(){
		$id = I('id');
		if (empty($id) || !is_numeric($id)) {
			$this->error('参数错误');
		}
		$vo = D('SoapUMovieComment')->getById($id);
		if (empty($vo)) {
			$this->error('记录不存在');
		}
		$this->assign('vo', $vo);
		
		$this->display();
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function updateSoapVolPic(){
		$model = D('SoapUMovieComment');
		if ($model->create() === false) {
			$this->error($model->getError());
		}
		$id = I('id');
		$dis_count = $model->where(array('id'=>$id))->getField('dis_count');
		if ($dis_count > 0) {
			$this->error('已经有人因此获取了肥皂，不能修改了哦。');
		}
		if ($model->save() === false) {
			$this->error('编辑失败');
		} else {
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('编辑成功');
		}
	}
	
	
	/**
	 * 获取图解信息
	 * Enter description here ...
	 */
	public function getVolPicInfo(){
		$movie_id = I('movie_id');
		$pindex = I('pindex');
		$rst = array('rst'=>0,'msg'=>'');
		if (empty($movie_id) || empty($pindex) || !is_numeric($movie_id) || !is_numeric($pindex)) {
			$rst['msg'] = '参数错误';
			$this->ajaxReturn($rst); return;
		}
		
		$map = array('id'=>$movie_id);
		$movie_img_ser = D('Movie')->where($map)->field('imgserver_use,name')->find();
		if (empty($movie_img_ser)) {
			$rst['msg'] = '电影ID错误';
			$this->ajaxReturn($rst); return;
		}
		
		$map = array('movie_id'=>$movie_id,'pindex'=>$pindex-1);
		$vol_pic = D('MComment')->where($map)->find();
		
		if (empty($vol_pic)) {
			$rst['msg'] = '页码错误';
			$this->ajaxReturn($rst); return;
		}
		$rst['rst'] = 1;
		$rst['data']['movie_name'] = $movie_img_ser['name'];
		$rst['data']['url'] = otherURL2ServerUrl($vol_pic['image'],$movie_id,$movie_img_ser['imgserver_use']);
		$rst['data']['vol_id'] = $vol_pic['id'];
		$this->ajaxReturn($rst);
	}
	
	
	/*
	 * 热搜词语列表
	 */
	public function searchKeyword(){
		$map = array('add_time'=>array('lt',toDate(NOW_TIME)));
		$list = D('StatSKeyword')->where($map)->order('id desc')->limit(50)->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	 * 删除热词
	 */
	public function doDelStatSKeyword(){
		$id = I('id');
		if (is_numeric($id)) {
			if (false === D('StatSKeyword')->where(array('id'=>$id))->delete()) {
				$this->error('删除失败');
			} else {
				$this->success('删除成功');
			}
		}
	}
	
	/**
	 * 生成热搜词语列表
	 */
	public function execStatSKeyword(){
		$map = array('add_time'=>array('lt',toDate(NOW_TIME)));
		$old_list = D('StatSKeyword')->where($map)->order('id desc')->limit(50)->select();
		$old_keyword = array();
		foreach ($old_list as $key=>$item) {
			if (isset($old_keyword[$item['keyword']])) continue;
			$old_keyword[$item['keyword']] = count($old_keyword) + 1;
		}
		$this->assign('old_keywords', $old_keyword);
		$list = D('SearchKeyword')->order('search_times desc')->limit(50)->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	public function getSearchKeyword(){
		$map = array('keyword'=>I('keyword'));
		$one =  D('SearchKeyword')->where($map)->find();
		$rst = array('rst'=>0);
		if (!empty($one)){
			$rst['rst'] = 1;
			$rst['data'] = $one;
			$this->ajaxReturn($rst);
		} else {
			$rst['msg'] = '没有查询到关键词，请先在APP里搜索后，再添加吧！';
			$this->ajaxReturn($rst);
		}
	} 
	
	
	public function doAddStateSKeyword(){
		$sk_id = I('sk_id');
		$keyword = I('keyword');
		$top_num = I('top_num');
		if (!is_array($sk_id) || !is_array($keyword) || !is_array($top_num) || count($sk_id) != count($keyword) || count($keyword) != count($top_num)) {
			$this->error('参数错误');
		}
		$dataList = array();
		$time = toDate(NOW_TIME);
		foreach ($sk_id as $key=>$id) {
			$dataList[] = array(
				'sk_id'=>$id,
				'keyword' => $keyword[$key],
				'top_num' => $top_num[$key],
				'sort_num' => $key+1,
				'add_time' => $time
			);
		}
		
		$dataList = array_reverse($dataList);
		if(false === D('StatSKeyword')->addAll($dataList)) {
			$this->error('添加出错');
		} else {
			$this->success('发布成功',U('SiteConfig/searchKeyword'));
		}
	}
	
}