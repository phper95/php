<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends CommonAction {
	function index(){
		//商品排序规则：正在进行->即将上架->已完成
		//正在进行: start_time<NOW_TIME and end_time>NOW_TIME,
		//即将上架：start_time-NOW_TIME>0
		//已完成： NOW_TIME-end_time>0
		//SELECT goods.*, lottery.end_time - lottery.start_time FROM shop_lottery AS lottery, shop_goods AS goods ORDER BY 13
		//SELECT shop_goods.*, NOW() - shop_lottery.start_time AS t1, shop_lottery.end_time - NOW() AS t2 FROM `shop_goods`, `shop_lottery` WHERE ( shop_goods.id = shop_lottery.g_id ) AND ( shop_goods.on_sale_time < NOW()) AND ( shop_goods.off_sale_time > NOW()) AND (shop_goods.cat_id = 1) ORDER BY 13, 14 LIMIT 20
		//$list = D('Goods')->where(array('on_sale_time'=>array('lt',NOW_DATE),'off_sale_time'=>array('gt',NOW_DATE)))->order('on_sale_time desc')->limit(20)->select();
		$Model=new Model();
		//$list = $Model->table('shop_goods,shop_lottery')->field("shop_goods.*,".'"'.NOW_DATE.'"'." - shop_lottery.start_time,shop_lottery.end_time-".'"'.NOW_DATE.'"')->where('shop_goods.id = shop_lottery.g_id' )->where(array('shop_goods.on_sale_time'=>array('lt',NOW_DATE),'shop_goods.off_sale_time'=>array('gt',NOW_DATE),'shop_goods.cat_id'=>array('eq',1)))->order('13,14 desc')->limit(20)->select();
		//$list = $Model->table('shop_goods,shop_lottery')->field("shop_goods.*,CASE WHEN NOW() - shop_lottery.start_time>0 and shop_lottery.end_time-NOW() >0 THEN 0 END")->where('shop_goods.id = shop_lottery.g_id' )->where(array('shop_goods.on_sale_time'=>array('lt',NOW_DATE),'shop_goods.off_sale_time'=>array('gt',NOW_DATE),'shop_goods.cat_id'=>array('eq',1)))->order('13')->limit(20)->select();
		$sql = "SELECT shop_goods.*,CASE WHEN shop_goods.ku_cun = 0 THEN 3 WHEN NOW() - shop_lottery.end_time > 0 THEN 4 WHEN shop_lottery.start_time - NOW() > 0 THEN 2 ELSE 1 END as tm FROM `shop_goods`, `shop_lottery` WHERE ( shop_goods.id = shop_lottery.g_id ) AND ( shop_goods.on_sale_time < NOW() ) AND ( shop_goods.off_sale_time > NOW() ) AND (shop_goods.cat_id = 1 OR shop_goods.cat_id = 2) ORDER BY 14 LIMIT 22";
		$list =$Model->query($sql);
		//dump($Model->getLastSql());
		//dump($list);exit;
		if (!empty($list)) {
			$GoodsImage = D('GoodsImages');
			$Lottery = D('Lottery');
			foreach ($list as $key=>$item) {
				$map = array('g_id'=>$item['id']);
				$list[$key]['img_list'] = $GoodsImage->where($map)->order('sort_num desc')->select();
				$list[$key]['lottery'] = $Lottery->where($map)->find();
			}
		}

		//dump($list);exit;
		$this->assign('list',$list);
		
		$user_id = userIdKeyDecode(I('userid'));
		$map = array('user_id'=>$user_id);
		$user_gold = D('UserVGold')->where($map)->getField('gold');
		if (empty($user_gold)) $user_gold = 0;
		$this->assign('gold',$user_gold);
		session(C('SESSION_AUTH_KEY'), $user_id);//SESSION_AUTH_KEY=>GM_SHOP_USER_ID
		$this->display();
	}
	
	function detail() {
		$key = I('k');
		if (empty($key)) {$this->error('参数错误呢');}
		$map = array('goods_id'=>$key);
		$goods = D('Goods')->where($map)->find();
		if (empty($goods)) {$this->error('商品找不到哦');}
		$map = array('g_id'=>$goods['id']);
		$vo = $goods;
		$vo['intro'] = D('GoodsIntro')->where($map)->getField('intro');
		$vo['img_list'] = D('GoodsImages')->where($map)->select();
		$lottery = D('Lottery')->where($map)->find();
		$vo['lottery'] = $lottery;
		$map = array('lot_id'=>$lottery['id'], 'user_id'=>session(C('SESSION_AUTH_KEY')));
		$lottery_times = D('UserLottery')->where($map)->find();
		$vo['lottery_times'] = $lottery['times'] - (empty($lottery_times)?0:$lottery_times['total_times']);

		//对抽奖结束的奖品进行后续处理
		if($goods['xuni_ku_cun']<=0 ||$goods['ku_cun']<=0 || $lottery['end_time'] < NOW_DATE){
			$this->lotteryFinished($goods,$lottery);
		}

		$this->assign('vo',$vo);
		$this->display();
	}
	
	
	/**
	 * 抽奖
	 */
	function lottery(){
		$key = I('k');
		if (empty($key)) {$this->error('参数错误呢');}
		$map = array('goods_id'=>$key);
		$goods = D('Goods')->where($map)->find();
		if (empty($goods)) {$this->error('商品找不到哦');}
		$map = array('g_id'=>$goods['id']);
		$lottery = D('Lottery')->where($map)->find();
		if (empty($lottery)) {$this->error('该商品未参与抽奖');}
		
		if ($lottery['start_time'] > NOW_DATE) {
			$this->error('抽奖还未开始');
		} else if ($lottery['end_time'] < NOW_DATE) {
			$this->error('抽奖已经结束了啊');
		}
		$user_id = session(C('SESSION_AUTH_KEY'));
		$map = array('lot_id'=>$lottery['id'], 'user_id'=>$user_id);
		$lottery_times = D('UserLottery')->where($map)->find();
		//判断同一用户是否多次中奖
		if($lottery_times['succ_times']>0){
			$isLotteried = 1;
		}else{
			$isLotteried = 0;
		}
		$times = $lottery['times'] - (empty($lottery_times)?0:$lottery_times['total_times']);
		if ($times>0) {
			$rst = array('times'=>$times-1);
			// step1. 获取用户金币
			$map = array('user_id'=>$user_id);
			$user_gold = D('UserVGold')->where($map)->find();
			if ($user_gold['gold']<$goods['price']) {
				$this->error(array('msg'=>'你金币不够哦，快去做任务赢取金币吧!', 'rst'=>0));
			}  else {
				D('UserVGold')->where($map)->setDec('gold', $goods['price']);
			}
			
			// step2. 抽奖概率生成器
			$yes = 0;
			if ($goods['ku_cun'] > 0 && $isLotteried ==0) {
				$lv = explode('/', $lottery['lv']);
				$rand = mt_rand(1, intval($lv[1]));
				if ($rand<=intval($lv[0])) { // 中奖了
					$yes = 1;
				}
			}

			//虚拟抽奖
			if($goods['xuni_ku_cun']>0 && $goods['xuni_ku_cun'] != $goods['ku_cun'] ){
				$agy = $this->analogLottery($goods['ku_cun'], $goods['xuni_ku_cun'],$lottery,$goods);
			}
			// step3. 抽奖结果分支处理
			
			// 用户抽奖表里插一条记录
			if (empty($lottery_times)) {
				$data = array(
						'user_id'=>$user_id,
						'lot_id' => $lottery['id'],
						'total_times' => 1,
						'succ_times' => $yes,
						'add_time' => NOW_DATE
				);
				D('UserLottery')->add($data);
			} else {
				D('UserLottery')->where(array('id'=>$lottery_times['id']))->setInc('total_times');
				if ($yes == 1) {
					D('UserLottery')->where(array('id'=>$lottery_times['id']))->setInc('succ_times');
				}
			}
			
			$remark = ''; // 记录表里的remark字段
			$rst['rst'] = $yes;
			if ($yes) { //
				// 库存量--
				D('Goods')->where(array('id'=>$goods['id']))->setDec('ku_cun');
				//虚拟库存--
				if($goods['xuni_ku_cun']>0){
					D('Goods')->where(array('id'=>$goods['id']))->setDec('xuni_ku_cun');
				}
				// 抽奖成功表里加一条数据
				$data = array(
						'order_id' => execOrderId(),
						'user_id'=>$user_id,
						'lot_id' => $lottery['id'],
						'cost' => $goods['price'],
						'add_time' => NOW_DATE
				);
				$succ_id = D('UserLotterySucc')->add($data);
				$rst['content'] = $goods['name'];
				//$addr = D('UserAddr')->where(array('user_id'=>$user_id))->field('id,name,phone,addr')->find();
				//$rst['addr'] = empty($addr) ? '' : $addr;
				$rst['addr'] = '';
				$rst['succ_k'] = $succ_id;
			} else { // 没有中奖 返回一条经典台词
				$wisdom = D('MdbWisdom')->where(array('id'=>rand(1,C('WISDOM_COUNT'))))->find();
				$rst['content'] = empty($wisdom) ? C('WISDOM_DEFAULT_CONTENT') : $wisdom['content'];
				$rst['movie'] = empty($wisdom) ? C('WISDOM_DEFAULT_TITLE') : $wisdom['from_title'];
				$remark = json_encode(array('wisdomId'=>empty($wisdom)?0:$wisdom['id']));
			}
			 
			// 记录表里插条数据
			$data = array(
					'user_id'=>$user_id,
					'lot_id' => $lottery['id'],
					'rand_num' => $rand,
					'rst' => $yes,
					'cost' => $goods['price'],
					'remark' => $remark,
					'add_time' => NOW_DATE
			);
			D('LotteryRecord')->add($data);
			$this->success($rst);
		} else {
			$this->error('你的剩余抽奖次数不够了哦。');
		}
	}


	/**
	 * 获取转盘抽奖商品信息
	 */
	function rotateLottery(){
		$map=array('cat_id'=>2);
		$map['ku_cun']=array('gt',0);
		$goods = D('Goods')->where($map)->limit(8)->select();
		if (empty($goods)) {$this->error('商品找不到哦');}
		/*$goodsNameStr='';
		foreach($goods as $k=>$v){
			var_dump($v['name']);
			$goodsNameStr .= $v['name'].',';
		}
		$goodsNameStr = rtrim($goodsNameStr, ",");
		//var_dump($goodsNameStr);exit;*/
		$this->assign('jsonGoods',json_encode($goods));
		$this->assign('goods',$goods);
		$this->display();
	}

	/**
	 * 转盘抽奖
	 * 思路：首先通过转盘奖品数量产生一个奖品的索引，根据索引得到奖品的id，再根据该奖品的中奖率抽取该奖品
	 */
	function doRotateLottery(){
		//$user_id=I('userid');
		//echo $user_id;
		$map=array('cat_id'=>2);
		$map['ku_cun']=array('gt',0);
		$goodsList = D('Goods')->where($map)->limit(8)->select();
		//var_dump($goodsList);
		if (empty($goodsList)) {
			//$this->error('商品找不到哦');
			$rst['rst'] = 0;
			$rst['msg'] = '商品找不到哦';
			$this->error($rst);
		}
		$max =  count($goodsList)-1;
		//随机产生商品
		$goods_index = rand(0,$max);
		//获取奖品id
		$goods_id = $goodsList[$goods_index][id];
		//var_dump($goods_id);
		$map = array('id'=>$goods_id);
		$goods = D('Goods')->where($map)->find();
		//var_dump($goods);exit;
		if (empty($goods)) {
			//$this->error('商品找不到哦');
			$rst['rst'] = 0;
			$rst['msg'] = '商品找不到哦';
			$this->error($rst);
		}
		$map = array('g_id'=>$goods['id']);
		$lottery = D('Lottery')->where($map)->find();
		if (empty($lottery)) {
			//$this->error('该商品未参与抽奖');
			$rst['rst'] = 0;
			$rst['msg'] = '该商品未参与抽奖';
			$this->error($rst);
		}

		if ($lottery['start_time'] > NOW_DATE) {
			$rst['rst'] = 0;
			$rst['msg'] = '抽奖还未开始';
			$this->error($rst);
			//$this->error('抽奖还未开始');
		} else if ($lottery['end_time'] < NOW_DATE) {
			$rst['rst'] = 0;
			$rst['msg'] = '抽奖已经结束了啊';
			$this->error($rst);
		}
		$user_id = session(C('SESSION_AUTH_KEY'));
		$map = array('lot_id'=>$lottery['id'], 'user_id'=>$user_id);
		$lottery_times = D('UserLottery')->where($map)->find();
		//判断同一用户是否多次中奖
		if($lottery_times['succ_times']>0){
			$isLotteried = 1;
		}else{
			$isLotteried = 0;
		}
		$times = $lottery['times'] - (empty($lottery_times)?0:$lottery_times['total_times']);
		if ($times>0) {
			$rst = array('times'=>$times-1);
			// step1. 获取用户金币
			$map = array('user_id'=>$user_id);
			$user_gold = D('UserVGold')->where($map)->find();
			//var_dump($map);exit;
			if ($user_gold['gold']<$goods['price']) {
				$this->error(array('msg'=>'你金币不够哦，快去做任务赢取金币吧!', 'rst'=>0));
			}  else {
				//扣除金币
				D('UserVGold')->where($map)->setDec('gold', $goods['price']);
			}

			// step2. 抽奖概率生成器
			$yes = 0;
			if ($goods['ku_cun'] > 0 && $isLotteried ==0) {
				$lv = explode('/', $lottery['lv']);
				$rand = mt_rand(1, intval($lv[1]));
				if ($rand<=intval($lv[0])) { // 中奖了
					$yes = 1;
					//$lottery_goods_id=$goods['id'];
					//$lottery_goods_index=$goods_index;
				}
			}


			// step3. 抽奖结果分支处理

			// 用户抽奖表里插一条记录
			if (empty($lottery_times)) {
				$data = array(
					'user_id'=>$user_id,
					'lot_id' => $lottery['id'],
					'total_times' => 1,
					'succ_times' => $yes,
					'add_time' => NOW_DATE
				);
				D('UserLottery')->add($data);
			} else {
				D('UserLottery')->where(array('id'=>$lottery_times['id']))->setInc('total_times');
				if ($yes == 1) {
					D('UserLottery')->where(array('id'=>$lottery_times['id']))->setInc('succ_times');
				}
			}

			$remark = ''; // 记录表里的remark字段
			$rst['rst'] = $yes;
			if ($yes) { //
				// 库存量--
				D('Goods')->where(array('id'=>$goods['id']))->setDec('ku_cun');
				// 抽奖成功表里加一条数据
				$data = array(
					'order_id' => execOrderId(),
					'user_id'=>$user_id,
					'lot_id' => $lottery['id'],
					'cost' => $goods['price'],
					'add_time' => NOW_DATE
				);
				$succ_id = D('UserLotterySucc')->add($data);
				$rst['content'] = $goods['name'];
				//$addr = D('UserAddr')->where(array('user_id'=>$user_id))->field('id,name,phone,addr')->find();
				//$rst['addr'] = empty($addr) ? '' : $addr;
				$rst['addr'] = '';
				$rst['succ_k'] = $succ_id;
				$rst['lottery_goods_index'] = $goods_index;
				$rst['lottery_goods_id'] = $goods['id'];
			} else { // 没有中奖 返回一条经典台词
				$wisdom = D('MdbWisdom')->where(array('id'=>rand(1,C('WISDOM_COUNT'))))->find();
				$rst['content'] = empty($wisdom) ? C('WISDOM_DEFAULT_CONTENT') : $wisdom['content'];
				$rst['movie'] = empty($wisdom) ? C('WISDOM_DEFAULT_TITLE') : $wisdom['from_title'];
				$remark = json_encode(array('wisdomId'=>empty($wisdom)?0:$wisdom['id']));
			}

			// 记录表里插条数据
			$data = array(
				'user_id'=>$user_id,
				'lot_id' => $lottery['id'],
				'rand_num' => $rand,
				'rst' => $yes,
				'cost' => $goods['price'],
				'remark' => $remark,
				'add_time' => NOW_DATE
			);
			D('LotteryRecord')->add($data);
			$this->success($rst);
		} else {
			$rst['rst'] = 0;
			$rst['msg'] = '你的剩余抽奖次数不够了哦';
			$this->error($rst);
		}


		
	}
	
	/**
	 * 抽中奖品后，绑定收货地址
	 */
	function bindAddr(){
		$succ_id = I('succ_k');
		$addr_id = I('addr_k');
		if (empty($succ_id) || empty($addr_id) || !is_numeric($succ_id) || !is_numeric($addr_id)) {
			$this->error('error');
		}
		$user_id = session(C('SESSION_AUTH_KEY'));
		
		$user_addr = D('UserAddr')->where(array('id'=>$addr_id))->find();
		if (empty($user_addr) || $user_id != $user_addr['user_id']) {$this->error('error');}
		$model = D('UserLotterySucc');
		$lot_succ = $model->where(array('id'=>$succ_id))->find();
		if (empty($lot_succ) || $user_id != $lot_succ['user_id']) {$this->error('error');}
		$lot_succ['addr_id'] = $addr_id;
		unset($user_addr['id'],$user_addr['add_time'],$user_addr['user_id']);
		$lot_succ['addr_info'] = json_encode($user_addr);
		$lot_succ['state'] = $lot_succ['state'] == '0' ? '1' : $lot_succ['state'];  
		if (false === $model->save($lot_succ)){
			$this->error('设置收货地址出错');
		} else {
			$this->success('设置收货地址成功');
		}
	}
	
	/**
	 * 设置地址
	 */
	function setAddr(){
		$user_id = session(C('SESSION_AUTH_KEY'));
		$addr = D('UserAddr')->where(array('user_id'=>$user_id))->find();
		//$addr = D('UserAddr')->where(array('user_id'=>$user_id))->select();
		//dump($addr);
		$this->assign('vo',$addr);
		$this->display();
	}
	
	function doSetAddr(){
		$user_id = session(C('SESSION_AUTH_KEY'));
		$phone = I('phone');
		$isMob="/^1[3-5,7,8]{1}[0-9]{9}$/";
		$isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
		if(!preg_match($isMob,$phone) && !preg_match($isTel,$phone)) {
			$this->error('电话号码格式错误');
		}
		$name = I('name');
		if (empty($name) || strlen($name) > 24 || strlen($name)<2) {
			$this->error('姓名格式不对');
		}
		$addr = I('addr');
		if (empty($addr) || strlen($addr) > 500 || strlen($addr)<8) {
			$this->error('地址格式不对');
		}
		$data = array('user_id'=>$user_id,'phone'=>$phone, 'addr'=>$addr, 'name'=>$name, 'add_time'=>NOW_DATE);
		$addr = D('UserAddr')->where(array('user_id'=>$user_id))->find();
		//var_dump($addr);
		//判断是新增地址还是选择已有地址
		if (!empty($addr)) {
			$data['id'] = $addr['id']; unset($data['add_time']);
			if (false === D('UserAddr')->save($data)){
				$id = FALSE;
			} else {
				$id = $data['id'];
			}
		} else {
			$id = D('UserAddr')->add($data);
		}
		if ($id === FALSE) {
			$this->error('地址设置失败');
		} else {
			$this->success(array('k'=>$id));
		}
	}
	
	/**
	 * 奖品列表
	 */
	function orderList (){
		$user_id = session(C('SSSION_AUTH_KEY'));
		$list = D('UserLotterySucc')->where(array('user_id'=>$user_id))->order('add_time desc')->select();
		$Goods = D('Goods');
		$Lottery = D('Lottery');
		$goodsList = array();
		foreach ($list as $key=>$vo) {
			if (!isset($goodsList [$vo['lot_id']])) {
				$g_id = $Lottery->where(array('id'=>$vo['lot_id']))->getField('g_id');
				$goodsList [$vo['lot_id']] = $Goods->where(array('id'=>$g_id))->field('goods_id,name,image,price')->find();
			}
			$list[$key]['goods'] = $goodsList [$vo['lot_id']];
		}
		$this->assign('state_txt',C('ORDER_LIST_STATE'));
		$this->assign('list',$list);
		$this->display();
	}
	
	function orderDetail(){
		$user_id = session(C('SESSION_AUTH_KEY'));
		$k = I('k');
		if (empty($k) || !is_numeric($k)) {
			$this->error('error');
		}
		$goods_id = I('gk');
		if (empty($goods_id)) {
			$this->error('error');
		}
		$one = D('Goods')->where(array('goods_id'=>$goods_id))->find();
		if (empty($one)) {$this->error('查询订单错误');}
		$this->assign('goods',$one);
		$one = D('UserLotterySucc')->where(array('id'=>$k))->find();
		if (empty($one)) {$this->error('查询订单错误');}
		if ($one['user_id'] != $user_id) {$this->error('error');}
		$one['addr'] = empty($one['addr_info']) ? '' : json_decode($one['addr_info'], true);
		$this->assign('state_txt',C('ORDER_LIST_STATE'));
		$this->assign('vo', $one);
		$this->display();
	}


	/**
	 * 金币直接兑换商品页
	 */
	function exchangeLottery(){
		$list = D('Goods')->where(array('on_sale_time'=>array('lt',NOW_DATE),'off_sale_time'=>array('gt',NOW_DATE),'cat_id'=>array('eq',3)))->order('on_sale_time desc')->limit(20)->select();
		if (!empty($list)) {
			$GoodsImage = D('GoodsImages');
			$Lottery = D('Lottery');
			foreach ($list as $key=>$item) {
				$map = array('g_id'=>$item['id']);
				$list[$key]['img_list'] = $GoodsImage->where($map)->order('sort_num desc')->select();
				$list[$key]['lottery'] = $Lottery->where($map)->find();
			}
		}
		$this->assign('list',$list);

		$user_id = userIdKeyDecode(I('userid'));
		$map = array('user_id'=>$user_id);
		$user_gold = D('UserVGold')->where($map)->getField('gold');
		if (empty($user_gold)) $user_gold = 0;
		$this->assign('gold',$user_gold);
		$this->display();
	}

	//奖品兑换详情页
	function exchangeDetail(){
		$key = I('k');
		if (empty($key)) {$this->error('参数错误呢');}
		$map = array('goods_id'=>$key);
		$goods = D('Goods')->where($map)->find();
		if (empty($goods)) {$this->error('商品找不到哦');}
		$map = array('g_id'=>$goods['id']);
		$vo = $goods;
		$vo['intro'] = D('GoodsIntro')->where($map)->getField('intro');
		$vo['img_list'] = D('GoodsImages')->where($map)->select();
		$lottery = D('Lottery')->where($map)->find();
		$vo['lottery'] = $lottery;
		$map = array('lot_id'=>$lottery['id'], 'user_id'=>session(C('SESSION_AUTH_KEY')));
		$lottery_times = D('UserLottery')->where($map)->find();
		$vo['lottery_times'] = $lottery['times'] - (empty($lottery_times)?0:$lottery_times['total_times']);
		$this->assign('vo',$vo);
		$this->display();
	}

	/**
	 * 金币直接兑换商品操作，一般用于ajax请求
	 */
	function doExchangeLottery(){
		$goods_id = I('k');
		if (empty($goods_id)) {
			$rst['rst'] = 0;
			$rst['msg'] = '缺少必要参数k';
			$this->error($rst);
		}
		$map=array('cat_id'=>3);
		$map['ku_cun']=array('gt',0);
		$map['goods_id']=array('eq',$goods_id);
		$goods = D('Goods')->where($map)->find();
		//var_dump($goods);
		if (empty($goods)) {
			//$this->error('商品找不到哦');
			$rst['rst'] = 0;
			$rst['msg'] = '来晚啦，奖品已经兑光啦';
			$this->error($rst);
		}

		$user_id = session(C('SESSION_AUTH_KEY'));

		$map = array('g_id'=>$goods['id']);
		$lottery = D('Lottery')->where($map)->find();
		if (empty($lottery)) {
			$rst['rst'] = 0;
			$rst['msg'] = '该商品未参与兑奖';
			$this->error($rst);
		}

		if ($lottery['start_time'] > NOW_DATE) {
			$rst['rst'] = 0;
			$rst['msg'] = '兑奖还未开始';
			$this->error($rst);
		} else if ($lottery['end_time'] < NOW_DATE) {
			$rst['rst'] = 0;
			$rst['msg'] = '兑奖已经结束了啊';
			$this->error($rst);
		}

		$map = array('lot_id'=>$lottery['id'], 'user_id'=>$user_id);
		$lottery_times = D('UserLottery')->where($map)->find();
		$times = $lottery['times'] - (empty($lottery_times)?0:$lottery_times['total_times']);
		if ($times>0) {
			$rst = array('times'=>$times-1);
			// step1. 获取用户金币
			$map = array('user_id'=>$user_id);
			$user_gold = D('UserVGold')->where($map)->find();
			if ($user_gold['gold']<$goods['price']) {
				$this->error(array('msg'=>'你金币不够哦，快去做任务赢取金币吧!', 'rst'=>0));
			}  else {
				D('UserVGold')->where($map)->setDec('gold', $goods['price']);
			}

			//是否允许兑奖标识
			$yes = 1;
			$rand = 3;//模拟随机码
			// step2. 兑奖结果分支处理

			// 用户抽奖表里插一条记录
			if (empty($lottery_times)) {
				$data = array(
					'user_id'=>$user_id,
					'lot_id' => $lottery['id'],
					'total_times' => 1,
					'succ_times' => $yes,
					'add_time' => NOW_DATE
				);
				D('UserLottery')->add($data);
			} else {
				D('UserLottery')->where(array('id'=>$lottery_times['id']))->setInc('total_times');
				if ($yes == 1) {
					D('UserLottery')->where(array('id'=>$lottery_times['id']))->setInc('succ_times');
				}
			}

			$remark = ''; // 记录表里的remark字段
			$rst['rst'] = $yes;
			if ($yes) { //
				// 库存量--
				D('Goods')->where(array('id'=>$goods['id']))->setDec('ku_cun');
				// 抽奖成功表里加一条数据
				$data = array(
					'order_id' => execOrderId(),
					'user_id'=>$user_id,
					'lot_id' => $lottery['id'],
					'cost' => $goods['price'],
					'add_time' => NOW_DATE
				);
				$succ_id = D('UserLotterySucc')->add($data);
				$rst['content'] = $goods['name'];
				//$addr = D('UserAddr')->where(array('user_id'=>$user_id))->field('id,name,phone,addr')->find();
				//$rst['addr'] = empty($addr) ? '' : $addr;
				$rst['addr'] = '';
				$rst['succ_k'] = $succ_id;
			} else { // 没有兑奖 返回一条经典台词
				$wisdom = D('MdbWisdom')->where(array('id'=>rand(1,C('WISDOM_COUNT'))))->find();
				$rst['content'] = empty($wisdom) ? C('WISDOM_DEFAULT_CONTENT') : $wisdom['content'];
				$rst['movie'] = empty($wisdom) ? C('WISDOM_DEFAULT_TITLE') : $wisdom['from_title'];
				$remark = json_encode(array('wisdomId'=>empty($wisdom)?0:$wisdom['id']));
			}

			// 记录表里插条数据
			$data = array(
				'user_id'=>$user_id,
				'lot_id' => $lottery['id'],
				'rand_num' => $rand,
				'rst' => $yes,
				'cost' => $goods['price'],
				'remark' => $remark,
				'add_time' => NOW_DATE
			);
			D('LotteryRecord')->add($data);
			$this->success($rst);
		} else {
			$this->error('你的剩余兑奖次数不够了哦。');
		}
	}

	//根据update_time获取robot_user的前五条记录
	function getRobotUser(){
		$num = C('VARTUAL_LIMIT')?C('VARTUAL_LIMIT'):5;
		$userList = M('user','robot_')->order('update_time desc')->limit($num)->select();
		return $userList;
	}

	//模拟抽奖过程,当1.奖品总数<5个，2.虚拟库存<=0,3.真实库存=虚拟库存，不做虚拟库存
	function analogLottery($zhen,$xu,$lottery,$goods){
		$cha = $xu - $zhen;
		$ci = ceil($cha)/pow($lottery['times'],0.4);
		//模拟抽奖次数
		for($i=0;$i<$ci;$i++){
			if($xu<=0){
				break;
			}

			$rst = $this->doAnalogLottery($goods,$lottery);
			//echo '第'.$i.'次是'.$rst.'<br/>';
			if($rst && $xu>$zhen && $xu>0){
				//虚拟库存--
					D('Goods')->where(array('id'=>$goods['id']))->setDec('xuni_ku_cun');

			}
		}
		$data['xu'] = $xu;
		$data['ci'] = $ci;
		return $data;

	}

	//单次虚拟抽奖
	function doAnalogLottery($goods,$lottery){
		$goodsInfo = D('Goods')->where(array('id'=>$goods['id']))->find();
		//抽奖概率生成器
		$yes = 0;
		if ($goodsInfo['xuni_ku_cun'] > 0) {
			$lv = explode('/', $lottery['lv']);
			$rand = mt_rand(1, intval($lv[1]));
			if ($rand<=intval($lv[0])) {
				// 中奖了
				$yes = 1;
			}
		}
		return $yes;

	}
	//抽奖结束后的操作:抽奖结束判断该商品中奖人数，若小于指定数量则向shop_virtual_user插入五条记录
	function lotteryFinished($goods,$lottery){
		$num = C('VARTUAL_LIMIT')?C('VARTUAL_LIMIT'):5;
		$map = array('g_id'=>$goods['id']);
		$success=D('UserLotterySucc')->where(array('lot_id'=>$lottery['id']))->select();
		$successNum = count($success);

		if($goods['total_num']>$num && $successNum<$num){
			$virtualUser = D('virtualUser')->where($map)->find();

			if(!$virtualUser){
				$virtualUsers =$this->getRobotUser();
				$virtualData = array();
				foreach($virtualUsers as $k=>$v){
					$virtualData['user_id'] = $v['user_id'];
					$virtualData['email'] = $v['email'];
					$virtualData['parent_id'] = $v['parent_id'];
					$virtualData['g_id'] = $goods['id'];
					$virtualData['add_time'] = NOW_DATE;
					D('virtualUser')->add($virtualData);

				}

			}
		}
	}




}