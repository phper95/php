<?php
/**
 * 肥皂榜
 * Enter description here ...
 * @author Administrator
 *
 */
class SoapToptenModel extends CommonModel {
	protected $trueTableName = 'soap_topten'; 
	
	public $_validate=array(
		array('users','require','用户必须'),
		array('b_time','require','开始时间必须'),
		array('e_time','require','结束时间必须')
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback')
	);
}
