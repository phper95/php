<?php
/**
 * 创作团模块
 */
class CreateTeamAction extends CommonAction {
	public function index(){
		$this->display();
	}
	
	/**
	 * 创作团成员列表
	 */
	public function teamList (){
		$map = $this->_search('MemberWorkMap');
		$js = I('js');
		if (!empty($js)) {
			$this->_list(D('MemberWorkMap'), $map, 'jinji_score', false,false);
		} else {
			$this->_list(D('MemberWorkMap'), $map);
			$bianji_list = D('CTBianji')->where(array('open'=>1))->getField('user_id,qian_time');
			$this->assign('bianji',$bianji_list);
		}
		
		$list = $this->get('list');
		if (!empty($list)) {
			$member = D('Member');
			foreach ($list as &$item) {
				$map = array('id'=>$item['user_id']);
				$item = array_merge($member->where($map)->field('name,add_time as reg_time,stat_beplayed,stat_belike')->find(),$item);
			}
			$this->assign('list',$list);
		}
		if (!empty($js)) {
			$this->ajaxReturn($list);
		} else {
			$this->display();
		}
	}
	
	
	/**
	 * 编辑部成员列表
	 */
	public function bianjiList (){
		$map = array();
		$this->_list(D('CTBianji'), $map);
		$list = $this->get('list');
		if (!empty($list)) {
			$member = D('Member');
			$workMap = D('MemberWorkMap');
			foreach ($list as &$item) {
				$map = array('id'=>$item['user_id']);
				$item = array_merge($member->where($map)->field('name,add_time as reg_time,stat_beplayed,stat_belike')->find(),$item);
				$map = array('user_id'=>$item['user_id']);
				$item = array_merge($item,$workMap->where($map)->field('online_work_count,add_time as fb_time,update_time')->find());
			}
			$this->assign('list',$list);
			$this->assign('idcard_state',array('无状态','<span class="blue">提交审核Ing</span>','<span class="green">审核成功</span>','<span class="red">审核失败</span>'));
			$this->assign('qian_state',array('无状态','<span class="blue">提交审核Ing</span>','<span class="green">审核成功</span>','<span class="red">审核失败</span>'));
		}
		$this->display();
	}
	
	/**
	 * 添加编辑部成员
	 */
	public function doAddBianji() {
		$member_ids = I('user_id');
		if (empty($member_ids) || !is_array($member_ids)) {
			$this->error('用户ID为空哟');
		}
		$dataList = array();
		$model =  D('CTBianji');
		$map = array('user_id'=>array('in',$member_ids));
		$exists_user_ids = $model->where($map)->getField('user_id',true);
		$time = toDate(NOW_TIME);
		foreach ($member_ids as $member_id) {
			if (!empty($exists_user_ids) && in_array($member_id, $exists_user_ids)) {continue;}
			$dataList[] = array('user_id'=>$member_id,'add_time'=>$time,'open'=>1);
		}
		if (!empty($dataList)) {
			if (false === $model->addAll($dataList)) {
				$this->error('插入数据库失败');
			}
		}
		$this->success('添加成功');
	}
	
	/**
	 * 评分
	 */
	public function pingfen(){
		$p_type = I('p_type',array());
		if (!is_array($p_type)) {
			$this->error('参数错误了呢');
		}
		$this->assign('p_type',implode(',',$p_type));
		
		$member_id = I('user_id');
		$befor_month = strtotime('-1 month');
		$p_month = I('p_month',date('Y-m',$befor_month));
		$start_time = $p_month.'-00 00:00:00';
		$end_time = $p_month.'-'.date('t',$befor_month)." 23:59:59";
		if (empty($member_id)) {$this->error('参数错误了呢');}
		
		$this->assign('p_month',$p_month);
		unset($_GET['p_month'],$_GET['p_type']);
		$this->assign('get',$_GET);
		
		if (empty($p_type)) {
			$this->assign('xxx','请选择评分的条件');$this->display();return;
		}
		
		// step 0 判断是否已经评分过了
		$map = array('user_id'=>$member_id,'p_month'=>$start_time);
		$tmp_list = D('CTWorkPingfen')->where($map)->select();
		if (!empty($tmp_list)) {
			$p_type_info = array('1'=>'movie','2'=>'fan','3' => 'news', '4' => 'wei');
			$p_type_str = array('1'=>'电影','2'=>'剧集','3' => '资讯', '4' => '微图解');
			foreach ($tmp_list as $tmp) {
				if (in_array($p_type_info[$tmp['p_type']], $p_type)) {
					$this->assign('xxx','用户在该月份 '.$p_type_str[$tmp['p_type']].' 已经评分过了哟！');$this->display();return;
				}
			} 
		}

		if (in_array('movie', $p_type) || in_array('fan', $p_type)) {
			// 获取用户上一个月的图解数据
			$map = array('_string'=>"instr(CONCAT(',',grapher,','), ',$member_id,') > 0");
			$map['add_time'] = array('BETWEEN',array($start_time,$end_time));
			$movieList = D('Movie')->where($map)->field('id,name,played,ding,vol_count,total_page,add_time')->select();
			$rst_movie = array(); // 电影列表
			$rst_fan = array(); // 剧集列表
			foreach ($movieList as $movie) {
				if ($movie['vol_count'] == 1) {
					$rst_movie[] = $movie;
				}elseif($movie['vol_count'] == 2) {
					$rst_fan[] = $movie;
				}
			}
			if (in_array('movie', $p_type)) {
				$rst_movie = empty($rst_movie) ? array() : $rst_movie;
				$this->assign('movieList',$rst_movie);
			}
			if (in_array('fan', $p_type)) {
				$rst_fan = empty($rst_fan) ? array() : $rst_fan;
				$this->assign('fanList',$rst_fan);
			}
		}
		
		if (in_array('wei', $p_type)) {
			// 获取用户上一个月的微图解数据
			$map = array('user_id'=>$member_id);
			$map['add_time'] = array('BETWEEN',array($start_time,$end_time));
			$rst_wei = D('Wei')->where($map)->field('id,title,played,ding,add_time')->select();
			$rst_wei = empty($rst_wei) ? array() : $rst_wei;
			$this->assign('weiList',$rst_wei);
		}
		
		
		if (in_array('news', $p_type)) {
			// 获取用户上一个月的资讯数据
			$map = array('_string'=>"instr(CONCAT(',',grapher,','), ',$member_id,') > 0");
			$map['add_time'] = array('BETWEEN',array($start_time,$end_time));
			$rst_news = D('News')->where($map)->field('id,name,played,ding,add_time')->select();
			$rst_news = empty($rst_news) ? array() : $rst_news;
			$this->assign('newsList',$rst_news);
		}
		
		$this->display();
	}
	
	/**
	 * do 评分
	 */
	public function doAddPingfen(){
		$keys = I('keys');
		if (empty($keys) || !is_array($keys)) {
			$this->error('提交参数错误');
		}
		$p_fen = I('p_fen');
		$p_fen_plus = I('p_fen_plus');
		$member_id = I('user_id');
		$plus_fen = I('plus_fen');
		$p_month = I('p_month');
		$open = I('open');
		$remark = I('remark');
		if (empty($member_id) || !is_numeric($member_id) || empty($p_month)) {
			$this->error('用户信息为空');
		}
		$p_month = $p_month.'-00 00:00:00';
		$p_type = array('movie'=>1,'fan'=>2,'news'=>3,'wei'=>4);
		$dataList = array();
		$time = toDate(NOW_TIME);
		
// 		// step 1 判断用户这一个月是否评分过
// 		$map = array('user_id'=>$member_id,'p_month'=>$p_month);
// 		$tmp = D('CTPingfen')->where($map)->find();
// 		if (!empty($tmp)) {
// 			$this->error('用户在'.I('p_month').' 月份，已经评分过了');
// 		}
		
		foreach ($keys as $key) {
			if (!isset($p_fen[$key]) || !isset($p_fen_plus[$key])) {
				$this->error('提交参数错误');
			}
			$tmp = explode(',', $key);
			$dataList[] = array(
				'user_id' => $member_id,
				'p_month' => $p_month,
				'p_type' => $p_type[$tmp[0]],
				'p_id' => $tmp[1],
				'j_type' => $tmp[2],
				'fen' => $p_fen[$key],
				'plus_fen' => $p_fen_plus[$key],
				'add_time' => $time
			);
		}
		
		if (empty($dataList)) {$this->error('评分数据为空');}
		if (false === D('CTWorkPingfen')->addAll($dataList)) {$this->error('评分数据入库失败');}
		
		// 另外计算该用户的得分
		// step1 查出该用户该月所有评分的数据
		$map = array('user_id'=>$member_id,'p_month'=>$p_month);
		$tmp_list = D('CTWorkPingfen')->where($map)->select();
		$fan_fen = 0; // 这里用于计算剧集总得分，因为额外投稿剧集总得分超过15分，不记录得分记录
		$other_fen = 0;
		if (!empty($tmp_list)) {
			foreach ($tmp_list as $tmp) {
				$fen = $tmp['fen'] + $tmp['plus_fen'];
				if ($tmp['p_type']=='2' && $tmp['j_type'] == '1') { //额外交稿的剧集作品总分单独计算
					$fan_fen += $fen; 
				} else {
					$other_fen += $fen;
				}
			}
		}
		
		$total_fen = $other_fen + ($fan_fen > 15 ? 15 : $fan_fen); // 计算出总得分
		
		
		// 如果存在已经评分过的数据，则用户评分数据不可能为空
		$tmp_one = D('CTPingfen')->where($map)->find();
		if (empty($tmp_one)) {
			$data = array(
					'user_id'=>$member_id,
					'p_month' => $p_month,
					'fen' => $total_fen,
					'plus_fen'=> I('plus_fen'),
					'open' => $open,
					'remark' => $remark,
					'add_time' => $time
			);
			if (false === D('CTPingfen')->add($data)) {$this->error('用户评分数据入库失败');}
		} else {
			$data = $tmp_one;
			$data ['remark'] .= '<br />'.$remark;
			if ($data['open'] == '1' && $open == '0') {
				$data['open'] == 0; // 只要出现一次不参与排名，就代表不参与排名了
			}
			$data['fen'] = $total_fen;
			$data['plus_fen'] += I('plus_fen');
			if (false === D('CTPingfen')->save($data)) {$this->error('用户评分数据更新失败');}
		}
		
		$this->success('评分成功',getCurrentUrl());
	}
	
	/**
	 * 评分排序值
	 */
	public function pingfenList (){
		$befor_month = strtotime('-1 month');
		$p_month = I('p_month',date('Y-m',$befor_month));
		$start_time = $p_month.'-00 00:00:00';
		$map = array('p_month'=>$start_time);
		$list = D('CTPingfen')->where($map)->order('(fen+plus_fen) DESC')->select();
		$unList = array();
		if (!empty($list)) {
			$member = D('Member');
			foreach($list as $key=>$item) {
				$map = array('id'=>$item['user_id']);
				$list[$key] = array_merge($member->where($map)->field('name,add_time as reg_time,stat_beplayed,stat_belike')->find(),$item);
				
				if ($item['open'] == 0) {
					$unList[] = $list[$key];
					unset($list[$key]);
				}
			}
		}
		$this->assign('list',$list);
		$this->assign('unlist',$unList);
		$this->assign('p_month',$p_month);
		$this->display();
	}
	
	/**
	 * 查看评分详情
	 */
	public function pingfenInfo(){
		$rst = array('rst'=>0,'msg'=>'');
		$map = $this->_search('CTWorkPingfen');
		if (!empty($map)) {
			$list = D('CTWorkPingfen')->where($map)->select();
			if (!empty($list)) {
				$p_type = array('1'=>'Movie', '2'=>'Movie', '3'=>'News', '4'=>'Wei');
				$p_field = array('1'=>'name', '2'=>'name', '3'=>'name', '4'=>'title');
				$p_str = array('1'=>'电影', '2'=>'剧集', '3'=>'资讯', '4'=>'微图解');
				foreach($list as $key=>$item) {
					if (!isset($p_type[$item['p_type']])) {
						$rst['msg'] = '数据库评分类型错误';
						$this->ajaxReturn($rst);
					}
					$map = array('id'=>$item['p_id']);
					$tmp = D($p_type[$item['p_type']])->where($map)->field($p_field[$item['p_type']])->find();
					$list[$key]['name'] = $tmp[$p_field[$item['p_type']]];
					$list[$key]['type_str'] = $p_str[$item['p_type']];
				}
				$rst['rst'] = 1;
				$rst['data'] = $list;
// 				$this->display('index'); return;
				$this->ajaxReturn($rst);
			}
		}
		$rst['msg'] = '数据为空';
		$this->ajaxReturn($rst);
	}
	
	/**
	 * 删除评分记录
	 */
	public function deletePingfen(){
		$member_id = I('user_id');
		$p_month = I('p_month');
		$p_type = I('p_type');
		if (empty($member_id) || empty($p_month) || empty($p_type) || !is_numeric($member_id)) {
			$this->error('参数错误');
		}
		
		$map = array('user_id'=>$member_id, 'p_month'=>$p_month, 'p_type'=>$p_type);
		if (false === D('CTWorkPingfen')->where($map)->delete()) {
			$this->error('删除失败');
		} else {
			$map = array('user_id'=>$member_id,'p_month'=>$p_month);
			$tmp_list = D('CTWorkPingfen')->where($map)->select();
			$fan_fen = 0; // 这里用于计算剧集总得分，因为额外投稿剧集总得分超过15分，不记录得分记录
			$other_fen = 0;
			if (!empty($tmp_list)) {
				foreach ($tmp_list as $tmp) {
					$fen = $tmp['fen'] + $tmp['plus_fen'];
					if ($tmp['p_type']=='2' && $tmp['j_type'] == '1') { //额外交稿的剧集作品总分单独计算
						$fan_fen += $fen;
					} else {
						$other_fen += $fen;
					}
				}
			}
				
			$total_fen = $other_fen + ($fan_fen > 15 ? 15 : $fan_fen); // 计算出总得分
			$data = D('CTPingfen')->where($map)->field('id,fen')->find();
			if (!empty($data)) {
				$data['fen'] = $total_fen;
				if (false === D('CTPingfen')->save($data)) {$this->error('用户评分数据更新失败');}
			}
			$this->success('删除成功');
		}
	}
	
	/**
	 * 查询创作团人员
	 */
	public function getMemberList(){
		$rst = array('rst'=>0,'msg'=>'');
		$member_id = I('id');
		if (empty($member_id)) {
			$rst['msg'] = '参数错误';
			$this->ajaxReturn($rst);
		}
		$map = array('user_id'=>$member_id);
		$item = D('MemberWorkMap')->where($map)->find();
		if (empty($item)) {
			$rst['msg'] = 'id不是创作团的';
			$this->ajaxReturn($rst);
		}
		$map = array('id'=>$item['user_id']);
		$item = array_merge(D('Member')->where($map)->field('name,add_time as reg_time,stat_beplayed,stat_belike')->find(),$item);
		$rst['rst'] = 1;
		$rst['data'] = $item;
		$this->ajaxReturn($rst);
	}
	
	public function getInfo(){
		$rst = array('rst'=>0,'msg'=>'');
		$type = I('type');
		$member_id = I('user_id');
		$allow_type = array('movie'=>'Movie','fan'=>'Movie','news'=>'News','wei'=>'Wei');
		$field = array(
				'Movie' => 'id,name,played,ding,vol_count,total_page,add_time',
				'News' => 'id,name,played,ding,add_time',
				'Wei' => 'id,title,played,ding,total_page,add_time',
		);
		
		if (empty($type) || !isset($allow_type[$type])) {
			$rst['msg'] = '参数错误';
			$this->ajaxReturn($rst);
		}
		$model_name = $allow_type[$type];
		unset($_REQUEST['type'],$_REQUEST['user_id']);
		
		$map = $this->_search($model_name);
		$s_time = $_REQUEST['start_time'];
		$e_time = $_REQUEST['end_time'];
		
		if (!empty($s_time))
			$map['add_time'] = array('EGT',$s_time);
		if (!empty($e_time)){
			$e_time .= " 23:59:59";
			$map['add_time'] = empty($map['add_time']) ? array('ELT',$e_time) : array ('BETWEEN',array($s_time, $e_time));
		}
		
		if ($type!='wei') {
			$name = I('name');
			if (!empty($name)) {
				$map ['name'] = array('like',"%$name%");
			}
			$map['_string'] = "instr(CONCAT(',',grapher,','), ',$member_id,') > 0";
			
			if ($type == 'movie') {
				$map['vol_count'] = 1;
			}else if ($type == 'fan') {
				$map['vol_count'] = 2;
			}
		} else {
			$title = I('title');
			if (!empty($title)) {
				$map ['title'] = array('like',"%$title%");
			}
			$map['user_id'] = $member_id;
		}
		
		$list = D($model_name)->where($map)->field($field[$model_name])->limit(20)->select();
		$rst['data'] = $list;
		$rst['rst'] = 1;
// 		$this->display('index');
		$this->ajaxReturn($rst);
	}
	
	
	/**
	 * 稿费
	 * Enter description here ...
	 */
	public function fee(){
		$fields = array('contract'=>'qq,realname,phone,email,id_card,address,yh_card');
		$map = array('cellcover'=>array('GT',0),'grapher'=>array('NEQ','2'));
		$list = D('Movie')->where($map)->getField('id,name,grapher,cellcover,total_page');
		$movie_list = array();
		$map = array('cellcover'=>C('FEE.OVER_STATE'));
		$Fee = D('Fee');
		$over_movie_ids = $Fee->where($map)->getField('movie_id',TRUE);
		$c_fee = C('FEE.PAY');
		$Member = D('Member');
		$ContractMember = D('ContractMember');
		$graphers = array(); // 作者列表去重
		$cost_arr = array(); // 稿费列表 作者-》作品-》稿费
		foreach ($list as $movie_id => $movie) {
			if (!isset($c_fee[$movie['cellcover']])) {
				$this->error('sb');
			} else {
				$tmp = explode(',', $movie['grapher']);
				if (in_array($movie_id, $over_movie_ids)) {
					if (count(array_keys($over_movie_ids,$movie_id))>=count($tmp))
					continue;
				}
				// 查找已经结算
				$history_fee = $this->getFeeByMovieId($Fee, $movie_id);
				$movie_fee = $c_fee[$movie['cellcover']];
				$cost = $movie_fee['p'] * $movie['total_page'];
				$cost = min(array($cost,$movie_fee['max']));
				$movie['cost'] = $cost;
				$movie_list[$movie_id] = $movie;
				$grapher_cost = array(); // 作者应付稿费列表
				if(count($tmp) > 1) {
					$FeePercent = D('FeePercent');
					$map = array('movie_id'=>$movie_id);
					$tmp2 = $FeePercent->where($map)->getField('id,movie_id,user_id,percent');
					// 2014年9月12日21:21:51 
					if (empty($tmp2) || count($tmp) != count($tmp2)) {
						$url = U('CreateTeam/percent');
						$this->error($movie_id . ' - 《' .$movie['name'].'》 作者分配未设定',$url);
					}
					foreach ($tmp2 as $id=>$grapher_percent) {
						$grapher_cost[$grapher_percent['user_id']] = round($cost * $grapher_percent['percent'] / 100,2);
					}
				}
				foreach ($tmp as $grapher_id) {
					if (!isset($grapher_cost[$grapher_id])) {
						$grapher_cost[$grapher_id] = $cost; // 如果是一个作者，则付全款
					}
					if (!isset($graphers[$grapher_id])) { // 获取作者的姓名
						$map = array('user_id'=>$grapher_id);
						$arr1 = $ContractMember->where($map)->field($fields['contract'])->find();
						$map = array('id'=>$grapher_id);
						$arr2 = $Member->where($map)->field('name,avatar,email as email2')->find(); 
						$graphers[$grapher_id] = empty($arr1)? $arr2 : array_merge($arr1,$arr2);
					}
					if (isset($history_fee[$grapher_id]['cellcover']) && $history_fee[$grapher_id]['cellcover'] == $movie['cellcover']) {
						continue; // 如果已经在这个级已经结算过，则该阶段不需要结算了
					}
					
					$history_grapher_cost = isset($history_fee[$grapher_id]['cost']) ? $history_fee[$grapher_id]['cost'] : 0;
					$cost_arr[$grapher_id]['cellcover'][$movie['cellcover']][] = array('movie_id'=>$movie_id,'name'=>$movie['name'],'total_page'=>$movie['total_page'],'fee'=>$movie_fee['p'],'history_fee'=>$history_grapher_cost,'cost'=>$grapher_cost[$grapher_id]);
					if (isset($cost_arr[$grapher_id][$movie['cellcover']]['cost'])){
						$cost_arr[$grapher_id][$movie['cellcover']]['cost'] += $grapher_cost[$grapher_id] - $history_grapher_cost;
					} else {
						$cost_arr[$grapher_id][$movie['cellcover']]['cost'] = $grapher_cost[$grapher_id] - $history_grapher_cost;
					}
					if (isset($cost_arr[$grapher_id]['cost'])){
						$cost_arr[$grapher_id]['cost'] += $grapher_cost[$grapher_id] - $history_grapher_cost;
					} else {
						$cost_arr[$grapher_id]['cost'] = $grapher_cost[$grapher_id] - $history_grapher_cost;
					}
				}
			}
		}
		$this->assign('list',$cost_arr);
		$this->assign('graphers',$graphers);
		$this->assign('cellcover',array(
					1=>array('str'=>'略屌','color'=>'greenBack'),
					2=>array('str'=>'震精','color'=>'blueBack'),
					3=>array('str'=>'神作','color'=>'redBack')));
		$this->display();
	}
	
	/**
	 * 根据电影ID查找已经结算的金额
	 * Enter description here ...
	 * @param FeeModel $Fee
	 * @param int $movie_id
	 * @return float
	 */
	private function getFeeByMovieId(FeeModel $Fee,$movie_id){
		$map = array('movie_id'=>$movie_id);
		$list = $Fee->where($map)->getField('id,cost,user_id,cellcover');
		$arr = array();
		if (!empty($list)) {
			foreach ($list as $id=>$item) {
				$arr[$item['user_id']]['cost'] = isset($arr[$item['user_id']]['cost']) ? $item['cost'] : ($arr[$item['user_id']]['cost'] + $item['cost']);
				$arr[$item['user_id']]['cellcover'] = isset($arr[$item['user_id']]['cellcover']) ? max(array($arr[$item['user_id']]['cellcover'],$item['cellcover'])) : $item['cellcover'];
			}
		}
		return $arr;
	}
	
	/**
	 * 结算稿酬
	 * Enter description here ...
	 */
	public function doFee(){
		$fee_ckb = I('fee_ckb');
		$fee = I('fee');
		if (empty($fee_ckb) || empty($fee)) {
			$this->error('参数错误3');
		}
		$data = array('add_time'=>toDate(NOW_TIME),'fee_time'=>toDate(NOW_TIME));
		$data_list = array();
		foreach ($fee_ckb as $item) {
			if (!isset($fee[$item])) {
				$this->error('参数错误2');
			}
			$tmp = explode(',', $item);
			if (count($tmp) != 3) {
				$this->error('参数错误1');
			}
			$data['movie_id'] = $tmp[0];
			$data['user_id'] = $tmp[1];
			$data['cellcover'] = $tmp[2];
			$data['cost'] = $fee[$item];
			$data_list[] = $data;
		}
		if (empty($data_list)) {$this->error('数据为空');}
		if (D('Fee')->addAll($data_list) === false) {
			$this->error('添加时错误');
		} else {
			$this->success('结算成功');
		}
	}
	
	/**
	 * 生成对账信息表
	 */
	public function execFeeExcel (){
		$fee_ckb = I('fee_ckb');
		$fee = I('fee');
		if (empty($fee_ckb) || empty($fee)) {
			$this->error('参数错误3');
		}
		$data = array();
		$Movie = D('Movie');
		$ContractMember = D('ContractMember');
		foreach ($fee_ckb as $item) {
			if (!isset($fee[$item])) {
				$this->error('参数错误2');
			}
			$tmp = explode(',', $item);
			if (count($tmp) != 3) {
				$this->error('参数错误1');
			}
			
			// [user_id][movie_id] = [cost,cellcover]
			$map = array('id'=>$tmp[0]);
			$name = $Movie->where($map)->getField('name');
			$str = $tmp[2] == '1' ? '略屌' : ($tmp[2] == '2' ? '震精' : '神作');
			if (!isset($data[$tmp[1]])) {
				$data[$tmp[1]] = array('cost'=>$fee[$item],'remark'=> "《{$name}》- $str - {$fee[$item]}",'realname'=>'-','bank'=>'-','zh'=>'-','card'=>'-','province'=>'-','city'=>'-');
				$map = array('user_id'=>$tmp[1]);
				$one = $ContractMember->where($map)->field('user_name,realname,yh_card')->find();
				if (!empty($one)) {
					$tmp1 = explode('|', $one['yh_card']);
					foreach ($tmp1 as $tmp2) {
						$tmp3 = explode(',', $tmp2);
						if (count($tmp3) != 3 || trim($tmp3['1']) == '支付宝') {continue;}
						$tmp3[1] = str_replace('（', '(', $tmp3[1]);
						$tmp4 = explode('(', $tmp3[1]);
						if (count($tmp4) > 1) {
							$bank = $tmp4[0];
							unset($tmp4[0]);
							$tmp5 = implode('(', $tmp4);
							$tmp5 = str_replace('）', ')', $tmp5);
							$tmp5 = substr($tmp5, 0,(strlen($tmp5)-1));
							$tmp6 = explode('^', $tmp5);
							if (count($tmp6)>=3) {
								$data[$tmp[1]]['user_id'] = $tmp[1].','.$one['user_name'];
								$data[$tmp[1]]['card'] = trim($tmp3[0]);
								$data[$tmp[1]]['realname'] = trim($tmp3[2]);
								$data[$tmp[1]]['bank'] = $bank;
								$data[$tmp[1]]['province'] = trim($tmp6[0]);
								$data[$tmp[1]]['city'] = trim($tmp6[1]);
								unset($tmp6[0],$tmp6[1]);
								$data[$tmp[1]]['zh'] = implode('^', $tmp6);
							}
						}
					}
				}
				
			} else {
				$data[$tmp[1]]['cost'] += $fee[$item];
				$data[$tmp[1]]['remark'] .= ";  《{$name}》- $str - {$fee[$item]}";
			}
		}
		if (empty($data)) {$this->error('数据为空');}
		
		$date = date('m-d',NOW_TIME);
		$table = array();
		$table['th'] = array ('',array('收款账户列',20),array('收款户名列',20),array('转账金额列',23),array('备注列',23),array('收款银行列',20),array('收款银行支行列',25),array('收款省/直辖市列',20),array('收款市县列',20),array('用户ID，记得删除哦',20),array('对账信息列，需删除的哟',400));
		foreach ($data as $member_id=>$row) {
			$this->_exchageProvinceCity($row);
			$table['td'][] = array (
				1 => $row['card'],
				2 => $row['realname'],
				3 => floatval($row['cost']),
				4 => '稿费-'.$date,
				5 => $row['bank'],
				6 => $row['zh'],
				7 => $row['province'],
				8 => $row['city'],
				9 => $row['user_id'],
				10 => $row['remark']
			);
		}
		import('@.ORG.PHPExcel.DoExcel');
		DoExcel::Output(array('name'=>'稿费批量-'.$date), $table);
	}
	
	/**
	 * 针对招行修改省份
	 * Enter description here ...
	 */
	private function _exchageProvinceCity(&$row){
		if (empty($row['province']) || empty($row['city'])) return;
		$prov = array('市','省','自治区','藏族','壮族','回族','维吾尔');
		foreach ($prov as $item) {
			$row['province'] = str_replace($item, '', $row['province']);
		}
		$zhixia = array('北京','上海','天津','重庆');
		if( in_array($row['province'], $zhixia)) {
			$row['city'] = $row['province'];
		}
		if (mb_substr($row['city'],-1,1,"UTF-8") == '市') {
//			$row['city'] = mb_substr($row['city'],0, -1 );
			$row['city'] = mb_substr($row['city'], 0,(mb_strlen($row['city'],'UTF-8')-1),'UTF-8');
		}
		$city_other_name = array(
			'甘孜州'=>'甘孜自治州',
			'文山'=>'文山自治州'
		);
		if (isset($city_other_name[$row['city']])) {
			$row['city'] = $city_other_name[$row['city']];
		}
	}
	
	
	/**
	 * 历史已结稿费信息
	 * Enter description here ...
	 */
	public function historyFee(){
		$Fee = D('Fee');
		$list = $Fee->limit(5000)->order('movie_id desc,id desc')->select();
		$graphers = array(); // 作者列表，用来记录作者的名字
		$movies = array(); // 电影名称列表，用来记录电影名称
		$Member = D('Member');
		$Movie = D('Movie');
		foreach ($list as $key => $item) {
			if (!isset($graphers[$item['user_id']])) {
				$map = array('id'=>$item['user_id']);
				$graphers[$item['user_id']] = $Member->where($map)->getField('name'); 
			}
			if (!isset($movies[$item['movie_id']])) {
				$map = array('id'=>$item['movie_id']);
				$movies[$item['movie_id']] = $Movie->where($map)->getField('name'); 
			}
			$list[$key]['user_name'] = $graphers[$item['user_id']];
			$list[$key]['movie_name'] = $movies[$item['movie_id']];
		}
		$this->assign('list',$list);
		$this->display();
//		$list = $Fee->Distinct(true)->field('fee_time')->order('fee_time desc')->select();
//		print_r($list);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addHistoryFee(){
		$Fee = D('Fee');
		$map = array();
		$this->_list($Fee, $map);
		$graphers = array(); // 作者列表，用来记录作者的名字
		$movies = array(); // 电影名称列表，用来记录电影名称
		$Member = D('Member');
		$Movie = D('Movie');
		$map = array('cellcover'=>array('gt',0),'grapher'=>array('neq','2'));
		$movie_list = D('Movie')->where($map)->getField('id,grapher,name');
		foreach ($movie_list as $movie_id=> $movie_item) {
			$tmp = explode(',', $movie_item['grapher']);
			foreach ($tmp as $member_id) {
				if (!isset($graphers[$member_id])) {
					$map = array('id'=>$member_id);
					$graphers[$member_id] = $Member->where($map)->getField('name');
				}
				$movie_item['graphers'][$member_id] = $graphers[$member_id];
			}
			$movie_list[$movie_id] = $movie_item;
		}
		$this->assign('movie_list',$movie_list);
		$list = $this->get('list');
		foreach ($list as $key => $item) {
			if (!isset($graphers[$item['user_id']])) {
				$map = array('id'=>$item['user_id']);
				$graphers[$item['user_id']] = $Member->where($map)->getField('name'); 
			}
			if (!isset($movies[$item['movie_id']])) {
				$map = array('id'=>$item['movie_id']);
				$movies[$item['movie_id']] = $Movie->where($map)->getField('name'); 
			}
			$list[$key]['user_name'] = $graphers[$item['user_id']];
			$list[$key]['movie_name'] = $movies[$item['movie_id']];
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	 * 添加历史录入
	 * Enter description here ...
	 */
	public function doAddHistoryFee(){
		$cost = I('cost');
		$cellcover = I('cellcover');
		$fee_time = I('fee_time');
		if (empty($cost) || empty($cellcover) || empty($fee_time)) {
			$this->error('参数错误');
		}
		$data = array('add_time'=>toDate(NOW_TIME));
		$data_list = array();
		foreach ($cost as $key=>$cost_one) {
			if (!isset($cellcover[$key]) || !isset($fee_time[$key])) {$this->error('参数错误');}
			$tmp = explode(',', $key);
			$data['movie_id'] = $tmp[0];
			$data['user_id'] = $tmp[1];
			$data['cost'] = $cost_one;
			$data['cellcover'] = $cellcover[$key];
			$data['fee_time'] = $fee_time[$key];
			$data_list[] = $data;
		}
		if (empty($data_list)) {$this->error('数据为空');}
		if (D('Fee')->addAll($data_list) === false) {
			$this->error('添加时错误');
		} else {
			$this->success('结算成功');
		}
	}
	
	/**
	 * 影片合作稿费分配规则
	 * Enter description here ...
	 */
	public function percent(){
		$map = array('grapher'=>array('like','%,%'));
		$movie_list = D('Movie')->where($map)->getField('id,name,grapher');
		$grapher_list = array();
		$Member = D('Member');
		$list = array();
		$FeePercent = D('FeePercent');
		$movie_ids = $FeePercent->getField('movie_id',true);
		foreach ($movie_list as $movie_id=> $movie_item) {
			if (in_array($movie_id, $movie_ids)) continue;
			$tmp = explode(',', $movie_item['grapher']);
			foreach ($tmp as $member_id) {
				if (!isset($grapher_list[$member_id])) {
					$map = array('id'=>$member_id);
					$grapher_list[$member_id] = $Member->where($map)->getField('name');
				}
				$movie_item['graphers'][$member_id] = $grapher_list[$member_id];
			}
			$list[$movie_id] = $movie_item;
		}
		$this->assign('movie_list',$list);
		$map = array();
		$this->_list($FeePercent, $map);
		$list = $this->get('list');
		foreach ($list as $key=>$percent_info) {
			if (!isset($grapher_list[$percent_info['user_id']])) {
				$map = array('id'=>$percent_info['user_id']);
				$grapher_list[$percent_info['user_id']] = $Member->where($map)->getField('name');
			}
			$list[$key]['movie_name'] = $movie_list[$percent_info['movie_id']]['name'];
			$list[$key]['user_name'] = $grapher_list[$percent_info['user_id']];
		}
		$this->assign('list',$list);
		$this->display();
	}
	
	/**
	 * 添加影片合作稿费分配规则
	 * Enter description here ...
	 */
	public  function doAddPercent(){
		$percent = I('percent');
		$movie_id = I('movie_id','');
		if (empty($percent) || empty($movie_id) || !is_numeric($movie_id)) {
			$this->error('参数错误啊1');
		}
		$grapher = D('Movie')->where(array('id'=>$movie_id))->getField('grapher');
		$tmp = explode(',', $grapher);
		if (empty($grapher) || count($tmp) != count($percent)) {
			$this->error('参数错误啊2');
		}
		
		$data = array('movie_id'=>$movie_id);
		$data['add_time'] = toDate(NOW_TIME);
		$total = 100;
		foreach ($percent as $member_id=>$per) {
			if (!is_numeric($per) || !is_numeric($member_id)) {$this->error('参数错误啊3');}
			$data['user_id'] = $member_id;
			$data['percent'] = $per;
			$data_list[] = $data;
			$total -= $per;
		}
		if ($total != 0) {
			$this->error('参数错误啊4');
		}
		
		// 判断是否已经存在
		$FeePercent = D('FeePercent');
		$map = array('movie_id'=>$movie_id);
		$tmp = $FeePercent->where($map)->getField('id');
		if (!empty($tmp)) {
			$this->error('该电影已经存在了');
		}
		
		if ($FeePercent->addAll($data_list) === FALSE) {
			$this->error('插入数据库错误');
		} else {
			$this->success('添加成功哦');
		}
	}
	
	
	public function getContractMember(){
		$id = I('id');
		$data = D('ContractMember')->where(array('user_id'=>$id))->find();
		$this->ajaxReturn($data);
	}
	
	/**
	 * 审核身份证信息
	 */
	public function examineIdCard(){
		$model = D('CTBianji');
		$data = $model->create();
		if ($data === false) {
			$this->error($model->getError());
		}
		if ($data['state'] == 2) { // 审核通过的话，并且要把open 置为1 并且需要把续签状态也置为正常状态
			$data['open'] = 1;
			$data['qi_state'] = 2;
		} else {
			$data['open'] = 0;
		}
		if($model->save($data) === false) {
			$this->error($model->getError());
		}
		$this->success('审核成功');
	}
	
	/**
	 * 签约续签操作
	 */
	public function examineXuQian(){
		$model = D('CTBianji');
		$xu = I('xu', null);
		$data = $model->create();
		if ($data === false) {
			$this->error($model->getError());
		}
		if ($data['qi_state'] == 2) { // 审核通过的话，并且要把open 置为1
			$data['open'] = 1;
		} else {
			$data['open'] = 0;
		}
		if($model->save($data) === false) {
			$this->error($model->getError());
		}
		if ($xu){
			$this->success('续签成功');
		} else {
			$this->success('审核成功');
		}
	}
}