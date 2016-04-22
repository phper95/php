<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Mail Class
 *
 * @package		
 * @subpackage	Libraries
 * @category	Sessions
 * @author		ExpressionEngine Dev Team
 * @link		
 */
require("mail/class.phpmailer.php"); //包含基类
class Mail {
	private $_mail;
	
	function Mail(){

	}
	
	/**
	 * 发送邮件
	 * @param unknown $name
	 * @param unknown $address
	 * @param unknown $title
	 * @param unknown $body
	 * @return boolean
	 */
	public function send($name, $address, $title, $body) {
		$this->_mail = new PHPMailer(); //建立邮件发送类
		$this->_mail->CharSet = 'utf8';
		$this->_mail->IsSMTP(); // 使用SMTP方式发送
		// 		$this->_mail->SMTPDebug  = 1; // 调试开启
		$this->_mail->Host = "smtp.qq.com"; // 您的企业邮局域名
		$this->_mail->SMTPAuth = true; // 启用SMTP验证功能
		$this->_mail->Username = "service@graphmovie.com"; // 邮局用户名(请填写完整的email地址)
		$this->_mail->Password = "graphmovie123"; // 邮局密码
		$this->_mail->Port=25;
		$this->_mail->From = "service@graphmovie.com"; //邮件发送者email地址
		$this->_mail->FromName = "图解电影";
		
		if (empty($title) || empty($body) || !$this->validate_email($address)) return false;
		if (empty($this->_mail)) return false;
		$this->_mail->AddAddress("$address", $name);//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
		//$this->_mail->AddReplyTo("", "");
		
		//$this->_mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
		$this->_mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
		
		$this->_mail->Subject = "=?utf-8?B?" . base64_encode("$title") . "?=";
		$this->_mail->Body = $body; //邮件内容
// 		$this->_mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
		
		return $this->_mail->Send();
	}
	
	/**
	 * 返回错误信息
	 * @return string
	 */
	public function getErrorInfo() {
		return $this->_mail->ErrorInfo;
	}
	
	/**
	 * 验证域名有效性
	 * @param unknown $email
	 * @return boolean
	 */
	public function validate_email($email) {
		$exp = "/^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$/";
		if (preg_match($exp, $email)) { // 先用正则表达式验证email格式的有效性
			if (checkdnsrr ( array_pop ( explode ( "@", $email ) ), "MX" )) { // 再用checkdnsrr验证email的域名部分的有效性
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
// END Session Class

/* End of file Session.php */
/* Location: ./system/libraries/Mail.php */