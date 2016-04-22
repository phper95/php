<?php
return array(
 		'log' => array(
 				'url' => true,
 				'error' => true,
 				'path' => 'log/', 'filename' => 'check_url.txt'
 		),
 		'socket' => array (
 				'host' => '121.199.9.233',
 				// 				'host' => '10.132.6.154',
 				'user' => 'admin',
 				'port' => 6000,
 				'time' => 35, // 等待时间
 				'times'=> 2, // 容错次数
 				'socket_time'=> 30,
 				'read_length' => 2000
 		),
 		'fxIp' => array(
 				'121.199.9.233' => '10.132.6.154',
 				'121.199.39.31' => '10.132.50.185',
 				'121.199.39.32' => '10.132.50.184',
 				'121.199.54.126' => '10.132.27.57',
 				'121.199.5.146' => '10.132.12.124'
 		),
		'serverAuth' => array(
				'121.199.9.233' => 1,
				'121.199.5.146' => 1,
				'115.29.12.51'  => 2
		)
 );

