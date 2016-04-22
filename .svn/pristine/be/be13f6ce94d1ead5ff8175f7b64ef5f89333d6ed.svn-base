<?php
class PushnoticeAction extends CommonAction {
	private $_basic_stle = array(
		'ling' => 0x04, //响铃
		'zzz' => 0x02,  //震动
		'clean' => 0x01 //可清除
	);
	
	private $_open_type = array(
		'url' => 1,
		'app' => 3
	);
	
	private $_user_type = array(
		'all' => 3,
		'tag' => 2,
		'user' => 1
	);
	
	function index(){
		$model = D('PushNotice');
		$map = array();
		$this->_list($model, $map);
		$this->display();
	}
	
	/**
	 * 发送消息
	 * @see CommonAction::add()
	 */
	function add(){
		$this->display();
	}
	
	/**
	 * 更新(non-PHPdoc)
	 * @see CommonAction::update()
	 */
	function update() {
		$model = D('PushNotice');
		if (false === $model->create()) {
			$this->error($model->getError());
		}
		// 更新数据
		$list = $model->save();
		if (false !== $list) {
			//成功提示
			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
			$this->success('编辑成功!');
		} else {
			//错误提示
			$this->error('编辑失败!');
		}
	}
	
	
	/**
	 * 取消发送
	 */
	function cancelSend(){
		$id = I('id');
		if (empty($id) || !is_numeric($id)) {
			$this->error('参数错误');
		}
		$data = array('id'=>$id,'is_send'=>2);
		$model = D('PushNotice');
		if ($model->save($data) === false) {
			$this->error('取消失败');
		}
		$this->success('取消成功');
	}
	
	/**
	 * 恢复发送
	 */
	function huifuSend(){
		$id = I('id');
		if (empty($id) || !is_numeric($id)) {
			$this->error('参数错误');
		}
		$data = array('id'=>$id,'is_send'=>0);
		$model = D('PushNotice');
		if ($model->save($data) === false) {
			$this->error('恢复失败');
		}
		$this->success('恢复成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	function addBySelf(){
		$this->display();
	}
	
	
	function selectMembers(){
		$ver = I('ver');
		if (is_numeric($ver)) $this->error('参数错误');
		$map = array('ver' => $ver);
		for ($i=0; true; $i++) {
			$member = D('Member')->where($map)->field('id,sns_bdyts_data')->limit(0,1000);
		}
	}
	/*
	 * Array
(
    [title] => 
    [content] => 瑕嗙洊
    [os] => android
    [user_type] => all
    [bar_style] => base
    [ling] => 1
    [zzz] => 1
    [clean] => 1
    [after_opt] => app
    [open_url] => 
    [keys] => Array
        (
            [0] => 
        )

    [values] => Array
        (
            [0] => 
        )

)
	 */
	
	private function _getMessage(){
		$_fiter = array('content','bar_style','after_opt','os');
		foreach ($_fiter as $val) {
			$tmp = I($val);
			if (empty($tmp)) {
				return null;
			}
		}
		$other_info = array();;
		$other_keys = I('keys');
		$other_values = I('values');
		if (!empty($other_keys)) {
			foreach ($other_keys as $k=>$key) {
				if (empty($key)) continue;
				$other_info[$key] = $other_values[$k];
			}
		}
		
		$message['title'] = I('title');
		$message['description'] = I('content');
		$message['notification_basic_style'] = 7;
		$message['open_type'] = $this->_open_type[I('after_opt')];
		$message['url'] = I('open_url');
		$message['user_confirm'] = intval(isset($_POST['open_url_confirm']));
		
		$basic_style = I('basic_style');
		if (!empty($basic_style)) {
			$tmp = 0x00;
			foreach ($basic_style as $val) {
				$tmp = $tmp ^ ( isset($this->_basic_stle[$val]) ? $this->_basic_stle[$val] : 0x00 );
			}
			$message['notification_basic_style'] = $tmp;
		}
		return $message;
	}
	
	function sendBySelf(){
		$file_path = APP_PATH.'Logs/push/';
		$md = I('md');
		$file_name = $file_path.$md.'.txt';
		$file_all = $file_path.$md.'_all.txt';
		$file_message = $file_path.$md.'_message.php';
		$type = I('type','default');
		$times = I('t',null);
		$rst = array('rst'=>0, 'msg'=>'');
		if (empty($md) || !isset($times) || !file_exists($file_name)) {
			$rst['msg'] = '参数错误';
			$this->ajaxReturn($rst); exit();
		}
		
		$rst['rst'] = 1;
		if ($type == 'all' && $times==0){
			$members = file_get_contents($file_all);
		} else {
			$members = file_get_contents($file_name);
		}
		$members = json_decode($members, true);
		if (!is_array($members) || empty($members)) {
			$rst['data']['num'] = 0;
			@unlink($file_name); // 删除文件
			$this->ajaxReturn($rst); exit();
		}
		$i = 50;
		$message = include $file_message;
		$rst['data']['num'] = count($members);
		load('@.pushNotice');
		$pushNotice = new Push_Notice();
		foreach ($members as $id=>$bd_id) {
		if (empty($bd_id)) {continue;}
			if ($i>0){
//				$rst1 = rand(0, 1000) % 100 == 0 ? null : array();
				$rst1 = $pushNotice->pushMessage_android($message,$bd_id);
				if (!is_array($rst1)) {
					$rst['data']['error'][$id] = $bd_id;
				} else {
					$rst['data']['succ'][$id] = $bd_id;
				}
				unset($members[$id]);
			} else {
				break;
			}
			$i--;
		}
		
		// 把没发成功的用户装入待发列表
		file_put_contents($file_name, json_encode($members));
		$this->ajaxReturn($rst); exit;
	}
	
	function getMemberCount(){
		$message = $this->_getMessage();
		$rst = array('rst'=>0, 'msg'=>'');
		if (empty($message)) {
			$rst['msg'] = '参数错误';
			$this->ajaxReturn($rst); exit();
		}
		$sql = stripslashes(trim($_POST['sql']));
		//var_dump($sql);
		if (empty($sql)) {
			$rst['msg'] = 'sql错误';
			$this->ajaxReturn($rst); exit();
		}
//		$rst['meees'] = ($sql);
		$rst['rst'] = 1;
		
		$message_str = var_export($message,true);
		$md5 = md5($message_str.$sql);
		$rst['data']['md'] = $md5;
		
		$new_push = I('new_push', null);
		$file_name = APP_PATH.'Logs/push/'. $md5.'.txt';
    	if(file_exists($file_name)) {
    		$tmp = file_get_contents($file_name);
    		$rst['data']['file'] = count(json_decode($tmp,true));
    	} else {
    		$file_all = APP_PATH.'Logs/push/'. $md5.'_all.txt';
			$file_message = APP_PATH.'Logs/push/'. $md5.'_message.php';
			$members = D('Member')->where($sql)->getField('id,sns_bdyts_userId');
			$rst['data']['nums'] = count($members);
			$json = json_encode($members);
    		file_put_contents($file_name, $json);
    		file_put_contents($file_all, $json);
    		// 这里不存json 是因为含有中文
    		file_put_contents($file_message, '<?php return ' . $message_str .' ;?>');
    	}
		
		$this->ajaxReturn($rst); exit();
	}
	
	function send(){
		$_fiter = array('content','user_type','bar_style','after_opt','os');
		foreach ($_fiter as $val) {
			$tmp = I($val);
			if (empty($tmp)) {
				$this->error('参数错误');
			}
		}
		$os = I('os');
		
		$other_info = array();;
		$other_keys = I('keys');
		$other_values = I('values');
		if (!empty($other_keys)) {
			foreach ($other_keys as $k=>$key) {
				if (empty($key)) continue;
				$other_info[$key] = $other_values[$k];
			}
		}
		
		$message['title'] = I('title');
		$message['description'] = I('content');
		$message['notification_basic_style'] = 7;
		$message['open_type'] = $this->_open_type[I('after_opt')];
		if ($message['open_type'] == 1){ // 打开Url
			$message['url'] = I('open_url'); 
		} else if ($message['open_type'] == 2) { // 自定义行为
			$message['pkg_content'] = array();
		}
		
		$basic_style = I('basic_style');
		if (!empty($basic_style)) {
			$tmp = 0x00;
			foreach ($basic_style as $val) {
				$tmp = $tmp ^ ( isset($this->_basic_stle[$val]) ? $this->_basic_stle[$val] : 0x00 );
			}
			$message['notification_basic_style'] = $tmp;
		}
		
		$keys = I('keys');
		$values = I('values');
		foreach ($keys as $k=>$k1) {
			$k1 = trim($k1);
			if (!empty($k1) && isset($values[$k])) {
				if ($k1 == 'gm_opt') {
					$message['custom_content'][$k1] = intval(trim($values[$k]));
				} else {
					$message['custom_content'][$k1] = trim($values[$k]). '';
				}
			}
		}
		
		load('@.pushNotice3');

		$send_time = I('send_time');
		if (empty($send_time)) {
			$rst = array();
			$is_android = false;
			$pushNotice = new Push_Notice3();
			if (in_array('android', $os)) {
				$is_android = true;
				$rst = $pushNotice->pushMessage_android($message); // 针对所有人android 用户 PUSH
			
			}
			if (!is_array($rst)) {
				$this->error('推送安卓失败'.'<br />'.$rst.$pushNotice->getErrorInfo());
			}
			/*
			 * 2015年7月1日19:28:30 苹果推送新版暂时不推
			 * */
			if (in_array('ios', $os)) {
// 				$user_id = '762469648415135621'; // 志友IOS
// 				$rst = $pushNotice->pushMessage_ios($message, $user_id,'',1,2);
				$rst = $pushNotice->pushMessage_ios($message, '','',1,2); // 针对所有IOS用户 PUSH
			}
			if (!is_array($rst)) {
				if ($is_android) {
					$this->error('安卓已经推送了，但苹果推送失败。'.'<br />'.$rst.$pushNotice->getErrorInfo());
				} else {
					$this->error('苹果推送失败。'.'<br />'.$rst.$pushNotice->getErrorInfo());
				}
			}
			//*/
		}
		$data = array(
			'title' => $message['title'],
			'content' => $message['description'],
			'send_type' => $this->_user_type[I("user_type")],
			'send_id' => I('send_id',''),
			'bar_style' => json_encode(array(I('bar_style')=>$message['notification_basic_style'])),
			'after_click' => json_encode(array(I('after_opt')=>array($message['url'], $message['user_confirm']))),
			'ios_info' => json_encode(array(I('ios_mp3',''), I('ios_superscript',''))),
			'send_time' => empty($send_time) ? toDate(NOW_TIME) : $send_time,
			'is_send' => empty($send_time) ? 1 : 0,
			'os' => implode(',', $os),
			'os_mark' => implode(',', $os) // os_mark 属于os的克隆字段，因为当自动推送的时候，有可能会修改os字段，完全用于记录初始值
		);
		if (!empty($other_info)) {
			$data['other_info'] = json_encode($other_info);
		}
		
		$model = D('PushNotice');
		$model->create($data);
		if (false === $model->add()) { 
			$this->error('推送成功，但写入数据失败');
		} else {
			$this->success('推送成功');
		}
	}
	
	public function getOptInfo(){
		$type_model = array('1'=>'Adv','2'=>'Movie','3'=>'Topic','4'=>'Member');
		$id = I('gm_id');
		$type = I('gm_opt');
		if (empty($id) || empty($type) || !is_numeric($id) || !isset($type_model[$type])){
			$this->error('参数错误');
		}
		$rst = array('rst'=>0, 'msg'=>'');
		$map = array('id'=>$id);
		$model = D($type_model[$type]);
		$one = $model->where($map)->field('id,name')->find();
		if (!empty($one)) {
			$rst['rst'] = 1;
			$rst['data'] = $one;
		} else {
			$rst['msg'] = '数据库不存在该ID';
		}
		$this->ajaxReturn($rst);
	}
}