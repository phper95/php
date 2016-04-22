<?php
// 用户反馈模块
class FeedbackAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
	public function index() {
		$map = $this->_search('Feedback');
		$s_time = $_REQUEST['s_time'];
		$e_time = $_REQUEST['e_time'];
		
		if (!empty($s_time))
			$map['add_time'] = array('EGT',$s_time);
		if (!empty($e_time)){
			$e_time .= " 23:59:59";
			$map['add_time'] = empty($map['add_time']) ? array('ELT',$e_time) : array ('BETWEEN',array($s_time, $e_time));
		}

        $model = D('Feedback');
        if (!empty($model)) {
           
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }
    
    
    
}
?>