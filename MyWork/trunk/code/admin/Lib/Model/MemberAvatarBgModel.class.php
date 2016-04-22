<?php
/**
 * 用户个人中心背景图。
 * Enter description here ...
 * @author Administrator
 *
 */
class MemberAvatarBgModel extends CommonModel {
	protected $trueTableName = 'user_v_avatar_bg'; 
	
	public $_validate=array(
		array('name','require','名称必须'),
		array('subtitle','require','副标题必须'),
		array('img_url','require','图片必须'),
	);
	
	public $_auto=array(
		array('add_time','getNowTime',self::MODEL_INSERT,'callback'),
		array('user_count',0,self::MODEL_INSERT)
	);
}
