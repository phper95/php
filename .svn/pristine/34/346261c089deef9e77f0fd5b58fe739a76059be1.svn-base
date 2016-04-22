<?php
namespace Home\Model;
use Think\Model;
class ClientUserModel extends Model {
	protected $_auto = array (          
//		array('name','getName',1,'callback'), // 对name字段在新增和编辑的时候回调getName方法         
		array('update_time','getTime',3,'callback'), // 对update_time字段在更新的时候写入当前时间戳     
		array('add_time','getTime',1,'callback'), // 对update_time字段在更新的时候写入当前时间戳
		array('imei','getImei',1,'callback'), // 
		array('avatar','http://imgs4.graphmovie.com/appimage/app_default_avatar.jpg'),
	);
	
	protected function getTime(){
		return date('Y-m-d H:i:s', NOW_TIME);
	}
	
	protected function getName(){
		return C('RGST_NAME');
	}
	
	protected function getImei(){
		return 'web-'.(microtime(true)-strtotime('2014-05-22 00:00:00')).'-'.rand(0, 1000);
	}
}