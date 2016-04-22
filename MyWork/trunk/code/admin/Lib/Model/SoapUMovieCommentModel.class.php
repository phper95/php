<?php
/**
 * 电影解说获取肥皂条件模型
 * Enter description here ...
 * @author Administrator
 *
 */
class SoapUMovieCommentModel extends CommonModel {
	protected $trueTableName = 'soap_v_vol_pic'; 
	
	public $_validate=array(
		array('vol_id','require','解说图片必须'),
		array('key','require','关键字必须'),
		array('allow_count','require','允许获取数量必须'),
		array('deadline','require','过期时间必须'),
		
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('dis_count',0,self::MODEL_INSERT)
	);
}
