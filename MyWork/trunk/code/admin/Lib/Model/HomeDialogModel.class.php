<?php
// 首页弹窗配置
class HomeDialogModel extends CommonModel {
	
	protected $trueTableName = 'home_dialog'; 
	
	public $_validate=array(
		array('name','require','名称不能为空'),
		array('image','require','图片不能为空'),
		array('btn_txt','require','名称不能为空')
	);
	
	public function getScript($data) {
		if (empty($data)) {
			$data = array('v'=>'1','a'=>'65','p'=>array('t'=>'今日茶点'));
			return json_encode($data);
		} else {
			return $data;
		}
	}
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('script','getScript',self::MODEL_INSERT,'callback')
	);
}
?>