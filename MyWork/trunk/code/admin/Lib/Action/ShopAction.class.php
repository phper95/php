<?php
// 活动相关操作
class ShopAction extends CommonAction {
	function index(){
		$this->goodsList();
	}
	
	function goodsList(){
		$map = array();
		$this->_list(D('ShopGoods'), $map);
		$this->display('goodsList');
	}
	
	function addGoods(){
		$this->assign('category', D('ShopGoodsCategory')->select());
		$this->display();
	}
	
	function doAddGoods(){
		$goods = D('ShopGoods');
		$lottery = D('ShopLottery');
		$g_id = 0;
		if ($goods->create() === false) {
			$this->error($goods->getError());
		}
		if ($lottery->create() === false) {
			$this->error($lottery->getError());
		}
		$g_id = $goods->add();
		if ($g_id === false) {
			$this->error($goods->getError());
		}
		$lottery->g_id = $g_id;
		if ($lottery->add() === false) {
			$this->error($lottery->getError());
		}
		$this->success('添加成功');
	}
	
	function editGoods(){
		$id = I('id');
		$vo = D('ShopGoods')->where(array('id'=>$id))->find();
		$vo['lottery'] = D('ShopLottery')->where(array('g_id'=>$id))->find();
		$tmp = explode('/', $vo['lottery']['lv']);
		$vo['lottery']['min_num'] = $tmp[0];
		$vo['lottery']['max_num'] = $tmp[1];
		$this->assign('vo',$vo);
		$this->assign('category', D('ShopGoodsCategory')->select());
		$this->display();
	}
	
	function doEditGoods() {
		$goods = D('ShopGoods');
		$lottery = D('ShopLottery');
		$g_id = 0;
		if ($goods->create() === false) {
			$this->error($goods->getError());
		}
		$data = $lottery->create();
		if ($data === false) {
			$this->error($lottery->getError());
		}
		if (false === $goods->save()) {
			$this->error($goods->getError());
		}
		$data['id'] = I('lot_id');
		if ($lottery->save($data) === false) {
			$this->error($lottery->getError());
		}
		$this->success('编辑成功', getCurrentUrl());
	}
	
	/**
	 * 修改库存
	 */
	function doEditKucun (){
		$p_type = I('p_type',null);
		$num = I('num',null);
		$field = I('field', null);
		$id = I('id', null);
		if (!isset($p_type) || !isset($num) || !isset($field) || !isset($id) || !is_numeric($id) || !is_numeric($num) || !in_array($field, array('ku_cun','total_num'))){
			$this->error('参数错误');
		}
		
		$map = array('id'=>$id);
		$model = D('ShopGoods');
		$one = $model->where($map)->find();
		if ($p_type == '1') {
			$bool = $model->where($map)->setInc($field, $num);
		} else {
			if ($num > $one[$field]) {$this->error('量不够减了！');}
			$bool = $model->where($map)->setDec($field, $num);
		}
		if ($bool === false) {
			$this->error($model->getError());
		} else {
			$this->success('修改成功');
		}
		
	}
	
	function editGoodsInfo (){
		$id = I('id');
		$vo = D('ShopGoodsIntro')->where(array('g_id'=>$id))->find();
		$this->assign('vo',$vo);
		$this->display();
	}
	
	function doEditGoodsInfo (){
		$model = D('ShopGoodsIntro');
		$data = $model->create();
		if ($data === false){
			$this->error($model->getError());
		}
		$one = $model->where(array('g_id'=>$data['g_id']))->getField('g_id');
		if (empty($one)) {
			$data['add_time'] = toDate(NOW_TIME);
			$rst = $model->add($data);
		} else {
			$rst = $model->save($data);
		}
		if ($rst === false) {
			$this->error($model->getError());
		}
		$this->success('编辑成功', getCurrentUrl());
	}
	
	/**
	 * 用户中奖信息表
	 */
	function lotterySucc() {
		$map = $this->_search('ShopMemberLotterySucc');
		$model = D('ShopMemberLotterySucc');
		$this->_list($model, $map);
		$list = $this->get('list');
		foreach ($list as $key=>$vo) {
			$g_id = D('ShopLottery')->where(array('id'=>$vo['lot_id']))->getField('g_id');
			$tmp = D('ShopGoods')->where(array('id'=>$g_id))->field('name,image')->find();
			$list[$key]['member'] = D('Member')->where(array('id'=>$vo['user_id']))->field('name,avatar')->find();
			$list[$key]['goods'] = $tmp;
		}
		$this->assign('list',$list);
		$this->assign('state',$model::$STATE_TXT);
		$this->display();
	}
	
	
	/**
	 * 中奖结果处理
	 */
	function doLotterySuccEdit(){
        $model = D('ShopMemberLotterySucc');
        $id = I('id');
        $vo = $model->where(array('id'=>$id))->find();
        $login_id = $_SESSION[C('USER_AUTH_KEY')];
        if($vo['state'] != $model::$STATE_WAITE_SEND && $vo['state'] != $model::$STATE_NO_ADDR && $login_id!=1) {
        	$this->error('只有状态是“等待发货” 或者 “未填写地址” 的订单才能处理');
        }
        if (false === $model->create()) {
        	$this->error($model->getError());
        }
        // 更新数据
        if (false !== $model->save()) {
            //成功提示
            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
            $this->success('处理成功!');
        } else {
            //错误提示
            $this->error('处理失败!');
        }
	}
	
	
	/**
	 * 用户中奖详细信息
	 */
	function lotterySuccDetail(){
		$id = I('id'); if (empty($id) || !is_numeric($id)) {$this->error('sb');}
		$model = D('ShopMemberLotterySucc');
		$vo = $model->where(array('id'=>$id))->find();
		$g_id = D('ShopLottery')->where(array('id'=>$vo['lot_id']))->getField('g_id');
		$tmp = D('ShopGoods')->where(array('id'=>$g_id))->field('name,image')->find();
		$vo['member'] = D('Member')->where(array('id'=>$vo['user_id']))->field('name,avatar')->find();
		$vo['goods'] = $tmp;
		$vo['addr'] = empty($vo['addr_info'])? '' : json_decode($vo['addr_info'], true); 
		$this->assign('vo',$vo);
		$this->assign('state',$model::$STATE_TXT);
		$this->display();
	}
	
	/**
	 * 删除图片
	 */
	function delGoodsImg(){
		$id = I('id',null);
		if (!is_numeric($id) || $id<=0) {
			$this->error('参数错误');
		}
		$model = D('ShopGoodsImage');
		$map = array('id'=>$id);
		if (false === $model->where($map)->delete()){
			$this->error($model->getError());
		} else {
			$this->success('删除成功');
		}
	}
	
	/**
	 *  上传商品图片
	 */
	function uploadGoodsImg(){
		$id = I('id',null);
		if (!is_numeric($id) || $id<=0) {
			$this->error('参数错误');
		}
		$map = array('g_id'=>$id);
		$image_list = D('ShopGoodsImage')->where($map)->order('sort_num desc')->select();
		$this->assign('list',$image_list);
		$this->display();
	}
	
	/**
	 *  Do 上传商品图片
	 */
	function doUploadGoodsImg(){
		$path = 'http://'.$this->_server('HTTP_HOST').__ROOT__;
		$sorts = I('sort');
		$up_sorts = I('up_sort');
    	$g_id = I('g_id');
    	if (empty($g_id) ||(!empty($up_sorts) && !is_array($up_sorts))|| (!empty($sorts) && !is_array($sorts))) {
    		$this->error('参数错误');
    	}
    	
    	if (!empty($up_sorts)) {
    		$model = D('ShopGoodsImage');
    		foreach ($up_sorts as $id=>$sort_num) {
    			if(is_numeric($id) && $id>0 && is_numeric($sort_num) && $sort_num<100){
	    			if (false === $model->save(array('id'=>$id, 'sort_num'=>$sort_num))){
	    				$this->error('更新排序数据失败');
	    			}
    			} else {
    				$this->error('排序值范围必须是在0~100 之间');
    			}
    		}
    	}
    	
    	//step1  获取该微图解下最大的pindex
    	$dataList = array();
    	if (!empty($sorts)) {
    		foreach ($sorts as $key=>$intro) {
    			$file_key = 'Fileimages-'.$key;
    			if (empty($_FILES[$file_key])) {
    				$this->error('Error 文件不能为空那');
    			} else {
    				if ($_FILES[$file_key]['error'] == 4) {
    					$this->error('上传图片不能为空');
    				}
    			}
    		}
    		
	    	$day_time = toDate(NOW_TIME);
	    	
	    	import("@.ORG.Util.Image");
	    	import("@.ORG.UploadFile");
	    	//导入上传类
	    	$upload = new UploadFile();
	    	//设置上传文件大小
	    	$upload->maxSize = 2097152;
	    	//设置上传文件类型
	    	$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
	    	$dir = '../goods/'.$g_id;
	    	
	    	if (!is_dir($dir)) {
	    		mkdir($dir, 0777);
	    	}
	    	
	    	$dir .= '/';
	    	$upload->savePath = $dir;
	    	
	    	// 设置引用图片类库包路径
	    	$upload->imageClassPath = '@.ORG.Util.Image';
	    	
	    	$saveName = $g_id.'_'.date('YmdHis').'_';
	    	//删除原图
	    	$upload->thumbRemoveOrigin = true;
	    	
	    	foreach ($sorts as $key=>$s) {
	    		$data = array(
	    				'sort_num'=>$s,
	    				'g_id'=>$g_id,
	    				'type' => 1, // 这里固定写死，大图用1
	    				'add_time'=>$day_time,
	    				'url' => 'http://imgs5.graphmovie.com/material/overdue.png'
	    		);
	    		$file_key = 'Fileimages-'.$key;
		    	if (!empty($_FILES[$file_key]) && $_FILES[$file_key]['error'] != 4) {
		    		$upload->saveRule = $saveName."b";
		    		$fileInfo = $upload->uploadOne($_FILES[$file_key]);
		    		if ($fileInfo) {
		    			$data['url'] = $path.'/goods/' . $g_id . '/' . $fileInfo[0]['savename'];
		    		} else {
		    			//捕获上传异常
		    			$strerror=$upload->getErrorMsg();
		    			$this->error($strerror);
		    		}
		    	}
		    	$dataList[] = $data;
	    	}
    	}
    	
    	if (!empty($dataList) && false === D('ShopGoodsImage')->addAll($dataList)) {
    		$this->error('写入数据库失败');
    	} else {
    		$one = D('ShopGoodsImage')->where(array('g_id'=>$g_id))->order('sort_num desc')->find();
    		$data = array('id'=>$g_id, 'image'=>$one['url']);
    		if (false === D('ShopGoods')->save($data)){
    			$this->error('图片编辑成功，更新商品图片失败',getCurrentUrl());
    		} else {
    			$this->success('编辑成功',getCurrentUrl());
    		}
    	}
    	
	}
}