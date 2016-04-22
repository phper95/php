<?php
class PublicAction extends Action {
	function look(){
		$key = I('k');
		$url = 'http://www.graphmovie.com';
		if (empty($key)) {$this->error('error',$url);}
		$map = array('goods_id'=>$key);
		$goods = D('Goods')->where($map)->find();
		if (empty($goods)) {$this->error('商品找不到哦',$url);}
		$map = array('g_id'=>$goods['id']);
		$vo = $goods;
		$vo['intro'] = D('GoodsIntro')->where($map)->getField('intro');
		$vo['img_list'] = D('GoodsImages')->where($map)->select();
		$lottery = D('Lottery')->where($map)->find();
		$vo['lottery'] = $lottery;
		$this->assign('vo',$vo);
		$this->display();
	}
}