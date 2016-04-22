<?php
// 推荐应用模型
class AppwallModel extends CommonModel {
	
	protected $trueTableName = 'apkwall'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('subtitle','require','说明必须'),
		array('icon','require','图标必须'),
		array('btn_title','require','下载按钮文字必须'),
		array('url','require','下载地址必须')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
	);
}
?>