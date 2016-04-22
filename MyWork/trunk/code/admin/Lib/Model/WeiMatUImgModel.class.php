<?php
// 微图解官方图册图片关系模型
class WeiMatUImgModel extends CommonModel {
	
	protected $trueTableName = 'wei_mat_v_img'; 
	
	public $_validate=array(
		
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>