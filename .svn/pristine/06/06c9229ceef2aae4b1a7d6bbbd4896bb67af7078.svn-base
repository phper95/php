<?php
/*
 * socket客户端类
 * 与服务器进行socket通讯
 *
 */
class SocketClient {
//	private $address = "127.0.0.1";
//	private $service_port = 1935;
//	private $address = "58.61.160.95";
//	private $service_port = 6000;
// 	private $address = "119.147.24.211";
	private $address = "121.199.9.233";
	private $service_port = 6000;
	protected $socket = NULL;
	public function connect($protocol){
		$protocol = strtolower($protocol);
		$type = $protocol == "udp"?SOCK_DGRAM:SOCK_STREAM;
		$this->socket = socket_create(AF_INET, $type, getprotobyname($protocol));
		if ($this->socket === FALSE) {
			$this->error("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
		}
		if (!socket_connect($this->socket, $this->address, $this->service_port)){
			$this->error("socket_connect() failed: reason: " . socket_strerror(socket_last_error()) . "\n");
		}
		return $this->socket;
	}
	
	public function submit($in){
		socket_write($this->socket, $in, strlen($in));
		$out = socket_read($this->socket, 2048);
		return $out;
	}
	
	public function write($in){
		socket_write($this->socket, $in, strlen($in));
		return true;
	}
	
	public function closeConnect(){
		socket_close($this->socket);
		$this->socket = NULL;
	}
	
	public function error($msg){
		exit();
	}
}