<?php
/**
 * 广告解说模型
 * Enter description here ...
 * @author Administrator
 *
 */
class AdvCommentModel extends Model {
	protected $trueTableName = 'adv_v_pic'; 
	
	public $_validate=array(
		array('adv_id','require','广告ID必须'),
		array('pindex','require','页码必须'),
		array('image','require','图片必须'),
		array('intro','require','说明必须'),
	);
}
