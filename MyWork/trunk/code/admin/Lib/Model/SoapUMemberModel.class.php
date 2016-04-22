<?php
/**
 * 电影解说获取肥皂条件模型
 * Enter description here ...
 * @author Administrator
 *
 */
class SoapUMemberModel extends CommonModel {
	protected $trueTableName = 'soap_v_user'; 
	
	public $_validate=array(
		array('vol_id','require','解说图片必须'),
		array('user_id','require','用户ID必须')
		
	);
}
