<?php
namespace Home\Controller;
class PcmakerController extends CommonController {
    public function index(){
    	$list = $this->_getList();
    	$this->assign('title','GMS投稿——图解电影') ;
    	$this->assign('menu',array(array('首页',U('Index/index')),array('GMS投稿','#')));
    	$this->assign('list',$list);
    	$this->display();
    }
    
    private  function _getList() {
    	$user = session('user');
    	$state = array(
    			'-3'=>'<span style="color:#900;">退稿：【{$reason}】</span>',
    			'-2'=>'<span style="color:#999;">回收站</span>',
    			'-1'=>'<span style="color:#333;">用户放弃</span>',
    			'0'=>'<span style="color:#999;">默认状态</span>',
    			'1'=>'<span style="color:#090;">创作Ing</span>',
    			'2'=>'<span style="color:#090;">上传成功，等待审核</span>',
    			'3'=>'<span style="color:#990;">已收录</span>',
    			'4'=>'<span style="color:#099;">被下线</span>',
    	);
    	$map = array('user_id'=>$user['id']);
    	$add_time = I('time');
    	if (!empty($add_time)) {
    		$map['add_time'] = array('LT', $add_time);
    	}
    	$list = M('PcmakerWork')->where($map)->field('id,progress,db_id,state,add_time,bpic_id,title,sub_title,we_score')->limit(20)->select();
    	if (!empty($list)) {
    		foreach ($list as $key=>$val) {
    			$list[$key]['right_str'] = $state[$val['state']];
    			if($val['state'] == '1') {
    				$list[$key]['right_str'] = '<span style="color:#333;">完成度：'.$val['progress']."%</span>";
    			} elseif ($val['state'] == '-3') {
    				$reason = M('pcmaker_work_admin_msg')->where(array('work_id'=>$val['id']))->order('add_time desc')->getField('msg');
    				$list[$key]['right_str'] = str_replace('{$reason}', $reason, $list[$key]['right_str']);
    			} elseif ($val['state'] == '2') {
    				if ($val['we_score'] > 0) {
    					$list[$key]['right_str'] = '<span style="color:#009;">审核通过，等待排期</span>';
    				}
    			}
    			$list[$key]['bpic'] = '';
    			if (!empty($val['bpic_id'])) {
    				$list[$key]['bpic'] = 'http://imgs4.graphmovie.com/gms_works'.M('PcmakerWorkImg')->where(array('id'=>$val['bpic_id']))->getField('url');
    			}
    		}
    	}
    	return $list;
    }
    public function getMore(){
    	$list = $this->_getList();
    	$this->assign('list',$list);
    	$this->ajaxReturn($list);
    }
}