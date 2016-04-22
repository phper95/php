<?php
// 后台用户模块
class testAction extends Action {
	private $_basic_stle = array(
		'ling' => 0x04, //响铃
		'zzz' => 0x02,  //震动
		'clean' => 0x01 //可清除
	);
	
	private $_open_type = array(
		'url' => 1,
		'app' => 3
// 		'app' => 2,
// 		'url' => 1
	);
	
	private $_user_type = array(
		'all' => 3,
		'tag' => 2,
		'user' => 1
	);
	
	
	public function send (){
	$_fiter = array('content','after_opt','os');
		foreach ($_fiter as $val) {
			$tmp = I($val);
			if (empty($tmp)) {
				$this->error('参数错误');
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
		/*
		 * 
    title：通知标题，可以为空；如果为空则设为appid对应的应用名;
    description：通知文本内容，不能为空;
    notification_builder_id：android客户端自定义通知样式，如果没有设置默认为0;
    notification_basic_style：只有notification_builder_id为0时有效，可以设置通知的基本样式包括(响铃：0x04;振动：0x02;可清除：0x01;),这是一个flag整形，每一位代表一种样式;
    open_type：点击通知后的行为(1：打开Url; 2：自定义行为；3：默认打开应用;);
    url：需要打开的Url地址，open_type为1时才有效;
    pkg_content：open_type为2时才有效，Android端SDK会把pkg_content字符串转换成Android Intent,通过该Intent打开对应app组件，所以pkg_content字符串格式必须遵循Intent uri格式，最简单的方法可以通过Intent方法toURI()获取
    custom_content：自定义内容，键值对，Json对象形式(可选)；在android客户端，这些键值对将以Intent中的extra进行传递。

		 */
		$message = array();
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
		
//		$message['custom_content'] = array('gm_opt'=>2,'gm_id'=>'2090');
//		$message['custom_content'] = array('gm_opt'=>3,'gm_id'=>'21','gm_name'=>'六一の脑残儿童大联欢');
//		$message['custom_content'] = array('gm_opt'=>5,'gm_url'=>'http://www.baidu.com','gm_title'=>'百度一下，拥有一切');
		
		
		load('@.pushNotice3');
//		$user_id = '668260477530476789'; // bobo
//		$user_id = '1070532132643704363'; // zhiyou
//		$user_id = '1068012468941013868'; // 琳川 Ios
//		$user_id = '1118788572634297760'; // 琳川 Ios8
//		$user_id = '730585914083914021'; // 琳川 Ios
//		$user_id = '797151430153051833';
// 		$user_id = '688017157345766261';
		$user_id = '3601808786707293773'; //bobo 
// 		$user_id = '3962574802372845904'; //Summer
		$user_id = '4135451892627000273'; // 志友channelID
// 		$user_id = '4967272515644584062'; // 林川的channelID
// 		$user_id = '5521373665537213538';
// 		$user_id = '4473106570459462648'; //bobo
// 		$user_id = '5440684105020681095'; // 志友生产状态 channelID
// 		$user_id = '3901291954455898635'; // 李涛
// 		$user_id = '4280957773533654003'; // 测试：
		$user_id = '4967272515644584062';

		
//		$user_id = '614561575282324274'; // woo
//		$user_id = '692361033542731277'; //测试机
//		{"errorCode":0,"appid":"1185357","requestId":"1992485376","channelId":"3640501907661607832","userId":"668260477530476789"}
//		$user_id = '773544514678178477'; // MI 3
		$pushNotice = new Push_Notice3();
//		$rst = $pushNotice->pushMessage_android($message,'bobo','tag');
		
		
		
// 		$message = array();
// 		$message['title'] = 'Hi~';
// 		$message['description'] = 'Hi~~~';
// 		$message['notification_basic_style'] = 7;
// 		$message['open_type'] = 3;
// 		$message['url'] = '';
// 		$message['user_confirm'] = 0;
// 		$message['notification_basic_style'] = 0x07;
// 		$message['custom_content']['gm_opt'] = 2;
// 		$message['custom_content']['gm_id'] = "7766";
		
		var_dump($message);
// 		$rst = $pushNotice->pushMessage_android($message,$user_id);
// 		var_dump($message);
		$rst = $pushNotice->pushMessage_ios($message, $user_id,'',1,2); // 生产转态发送单条推送
// 		$rst = $pushNotice->pushMessage_ios($message, $user_id,'',1,1); // 开发转态发送单条推送
// 		$rst = $pushNotice->pushMessage_ios($message, '','',1,2);
//		$rst = $pushNotice->pushMessage_android($message,$user_id,'',0);

//		$rst = $pushNotice->pushMessage_android($message); // 针对所有人PUSH

//		$rst = $pushNotice->fetchTag('bobo');
//		$rst = $pushNotice->setTag('bobo', $user_id);
//		$rst = $pushNotice->fetchMessageCount($user_id);
//		$rst = $pushNotice->queryUserTags($user_id);
		if (is_array($rst)) {
			print_r($pushNotice->getSuccInfo());
			$this->success('推送成功');
		}else {
			echo ('推送失败'.'<br />'.$rst.$pushNotice->getErrorInfo()); exit();
		}
	}
	
	
	public function testsend(){
	}
	
}
?>