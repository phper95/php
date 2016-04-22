<?php
import ( "@.ORG.Baidu.Push3.PushSDK" );

class Push_Notice3 {
	function push(){
		// 创建SDK对象.
		$sdk = new PushSDK();
		
		$channelId = 'xxxxxxxxxx';
		
		// message content.
		$message = array (
				// 消息的标题.
				'title' => 'Hi!',
				// 消息内容
				'description' => "hello, this message from baidu push service."
		);
		
		// 设置消息类型为 通知类型.
		$opts = array (
				'msg_type' => 1
		);
		
		// 向目标设备发送一条消息
		$rs = $sdk -> pushMsgToSingleDevice($channelId, $message, $opts);
		
		// 判断返回值,当发送失败时, $rs的结果为false, 可以通过getError来获得错误信息.
		if($rs === false){
			print_r($sdk->getLastErrorCode());
			print_r($sdk->getLastErrorMsg());
		}else{
			// 将打印出消息的id,发送时间等相关信息.
			print_r($rs);
		}
		
		echo "done!";
	}
	
	/**
	 * 推送android消息
	 * -------------------------------
	 * @param unknown_type $message 通知类型的内容必须按指定内容发送
	 * @param unknown_type $id ID，channelId 或TagID，如果是TagID，则需要填Type
	 * @param unknown_type $type 推送类型，默认针对User，如果填1 则表示推送TagID
	 * @param unknown_type $msg_type 推送类型，0-消息，1-通知，默认是发通知
	 * {"errorCode":0,"appid":"1185357","requestId":"1992485376","channelId":"3640501907661607832","userId":"668260477530476789"}
	 */
	function pushMessage_android($message, $id = '', $type='', $msg_type=1){
		// 2016年2月16日17:24:20 由于遵循官方文档，直接打开APP 3 不起作用，临时调整为 2 自定义处理
		if (isset($message['open_type']) && $message['open_type'] == 3) {
			$message['open_type'] = 2;
		}
		// 创建SDK对象.
		$sdk = new PushSDK();
		
		$opts = array(
			'msg_type' => $msg_type,
			'msg_expires' => 86400 * 2 // 默认两天
		);
	
		$ret = false;
		if (!empty($type)) { //推送Tag用户
			echo '发送给Tag';
			$ret = $sdk -> pushMsgToTag($id, $message, $opts);
		} elseif (!empty($id)) { // 推送指定用户
			if (is_array($id)) {
				$ret = $sdk -> pushBatchUniMsg($id, $message, $opts);
			} else {
				echo '发送给指定人';
				$ret = $sdk -> pushMsgToSingleDevice($id, $message, $opts);
			}
		} else { // 所有用户推送
			echo '发送给所有人';
			$ret = $sdk -> pushMsgToAll($message, $opts);
		}
	
		if (false === $ret) {
			$this->error_output(print_r($sdk->getLastErrorCode(), true));
			$this->error_output(print_r($sdk->getLastErrorMsg(), true));
		} else {
			$this->right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' );
			$this->right_output ( 'result: ' . print_r ( $ret, true ) );
		}
		return $ret;
	}
	
	
	
	
	/**
	 * 推送ios消息
	 * Enter description here ...
	 * @param unknown_type $message 通知类型的内容必须按指定内容发送
	 * @param unknown_type $id ID，channelId 或TagID，如果是TagID，则需要填Type
	 * @param unknown_type $type 推送类型，默认针对User，如果填1 则表示推送TagID
	 * @param unknown_type $msg_type 推送类型，0-消息，1-通知，默认是发通知
	 * @param int $deploy IOS 推送状态，1开发状态，2生产状态 默认1
	 * {"errorCode":0,"appid":"1185357","requestId":"1992485376","channelId":"3640501907661607832","userId":"668260477530476789"}
	 */
	function pushMessage_ios($message, $id = '', $type='', $msg_type=1, $deploy=1){
		// 创建SDK对象.
		$sdk = new PushSDK();
		$ret = false;
		$opts = array(
				'msg_type' => $msg_type,
				'deploy_status' => $deploy
		);
		$sdk->setDeviceType(PushSDK::DEVICE_IOS);
		if (!empty($type)) { //推送Tag用户
			echo '发送给Tag';
			$ret = $sdk -> pushMsgToTag($id, $message, $opts);
		} elseif (!empty($id)) { // 推送指定用户
			if (is_array($id)) {
				$ret = $sdk -> pushBatchUniMsg($id, $message, $opts);
			} else {
				echo '发送给指定人';
				$ret = $sdk -> pushMsgToSingleDevice($id, $message, $opts);
			}
		} else { // 所有用户推送
			echo '发送给所有人';
			$ret = $sdk -> pushMsgToAll($message, $opts);
		}
	
		if (false === $ret) {
			$this->error_output(print_r($sdk->getLastErrorCode(), true));
			$this->error_output(print_r($sdk->getLastErrorMsg(), true));
		} else {
			$this->right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' );
			$this->right_output ( 'result: ' . print_r ( $ret, true ) );
		}
		return $ret;
	}
	
	function error_output($str) {
		$this->_errorMsg .= "" . $str . "" . "<br />";
	}
	
	function right_output($str) {
		$this->_succMsg .= "" . $str . "" . "<br />";
	}
	
	function getErrorInfo(){
		return $this->_errorMsg;
	}
	
	function getSuccInfo(){
		return $this->_succMsg;
	}

}

