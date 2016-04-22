<?php
// 首页上线配置
class HomeTuijianModel extends CommonModel {
	
	protected $trueTableName = 'home_tuijian'; 
	
	public $_validate=array(
		array('t_id','require','上线ID不能为空'),
		array('tuijian_type', 'checkTuijianType', '类型错误呢', 1,'callback', 1),
	);
	
	public function checkTuijianType($t_type) {
		$type_arr = array('movie'=>1,'wei'=>3,'paper'=>4);
		$t_id = I('t_id');
		if (empty($t_type) || empty($t_id) || !is_numeric($t_id) || !isset($type_arr[$t_type])) {
			return false;
		}
	}
	
	public function getTType($data){
		$type_arr = array('movie'=>1,'wei'=>3,'paper'=>4);
		$t_type = I('tuijian_type');
		$t_id = I('t_id');
		if (empty($t_type) || empty($t_id) || !is_numeric($t_id) || !isset($type_arr[$t_type])) {
			return '';
		}
		if ($t_type == 'movie') {
			$vol_count = D('Movie')->where(array('id'=>$t_id))->getField('vol_count');
			if ($vol_count == 2) {
				return 2;
			}
		}
		return $type_arr[$t_type];
	}
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('t_type','getTType',self::MODEL_BOTH,'callback'),
	);
}
?>