<?php
// 角色模块
class SoapAction extends CommonAction {
	public function index(){
		$model = D('SoapUMember');
		$map = array();
		$this->_list($model, $map,'add_time');
		$this->display();
	}
	
	public function execPoptxt(){
		$soapUMComment = D('SoapUMovieComment');
		$map = array('deadline'=>array('gt',toDate(NOW_TIME)), 'dis_count'=>array('exp','<`allow_count`'));
		$list = $soapUMComment->where($map)->select();
		$poptxtMovie = D('PoptxtMovie');
		$soapUMember = D('SoapUMember');
		foreach ($list as $item) {
			$cha = $item['allow_count'] - $item['dis_count'];
			$map = array('page_index'=>$item['vol_id']);
			$poptxtList = $poptxtMovie->where($map)->order('id asc')->select();
			foreach ($poptxtList as $poptxt) {
				if ($cha>0) {
					$keys = explode(',', $item['key']);
					foreach ($keys as $key) {
						if (FALSE !== strstr($poptxt['comment_content'], $key)) { // 找到满足条件的弹幕了
							$cha --;
							$data = array(
								'user_id'=>$poptxt['user_id'],
								'vol_id' =>$item['vol_id'],
								'add_time'=>$poptxt['add_time']
							);
							if (false === $soapUMember->add($data)) {
								$this->error('插入肥皂用户列表时出错');
							}
							$map = array('id'=>$item['id']);
							$soapUMComment->where($map)->setInc('dis_count');
							break; // 找到关键字了，跳出
						}
					}
				} else { // 满足最大数了，跳出
					break;
				}
			} 
		}
		$this->success('识别成功');
	}
	
	/**
	 * 查看肥皂榜
	 */
	public function topten(){
		$model = D('SoapTopten');
		$map = array();
		$this->_list($model, $map, 'b_time');
		$list = $this->get('list');
		$user_arr = array(); // 记录所有出现的用户,防止多次查询数据库
		foreach ($list as $key=>$item) {
			$users = json_decode($item['users'],true);
			$str = '';
			foreach ($users as $user_id=>$item2) {
				if (!isset ($user_arr[$user_id])){
					$user_arr[$user_id] = D('Member')->where(array('id'=>$user_id))->getField('name');
				}
				$str .= $user_arr[$user_id].' - '.count($item2).' 块；';
			}
			$list[$key]['info'] = $str;
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	 * 生成肥皂榜
	 * Enter description here ...
	 */
	public function execTopten(){
		$s_time = I('s_time');
		$e_time = I('e_time');
		$nums = I('nums');
		if (empty($s_time) || empty($e_time) || $e_time < $s_time || empty($nums) || !is_numeric($nums)) {
			$this->error('参数错误');
		}
		$model = D('SoapUMember');
		$map = array('add_time'=>array('BETWEEN',array($s_time,$e_time)));
		$list = $model->where($map)->order('add_time asc')->select();
		if (empty($list)) { $this->error('这段时间，木油人捡到肥皂啊'); }
		$topten = array();
		$lastTime = array(); // 记录用户达到该数量的日期
		foreach ($list as $item) { // 这段时间 user_id 获取的肥皂ID数
			$topten[$item['user_id']][] = $item['id'];
			$lastTime[$item['user_id']] = $item['add_time'];
		}
		
		$sort = array();
		$datajs = strtotime('2014-05-01');// 基数，拉低小数点值
		foreach ($topten as $user_id=>$item) { // 这里加上时间这个参数，然后再排序
			$xiaoshu = doubleval('0.'. (strtotime($lastTime[$user_id]) - $datajs));
			$sort[$user_id] = count($item) + (1-$xiaoshu);
			// 最早得到相同肥皂数的人，应该排在前面，所以，应该用1-
//			$sort[$user_id] = doubleval(count($item) .'.'. (strtotime($lastTime[$user_id]) - $datajs));
		}
		arsort($sort);
		
		$rst = array(); // 排序后的数组
		$i = 1;
		foreach ($sort as $key=>$value) {
			if ($i>$nums) break;
			$rst[$key] = $topten[$key];
			$i++;
		}
		
		$data = array(
			'users'=>json_encode($rst),
			'b_time'=>$s_time,
			'e_time'=>$e_time
		);
		
		$model = D('SoapTopten');
		if (false === $model -> create($data)){
			$this->error($model->getError());
		}
		
		if (false === $model->add()){
			$this->error('生成肥皂榜失败');
		} else {
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('肥皂榜生成成功!');
		}
	}
}