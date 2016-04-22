<?php
class My_RSS{
	private $_pub_platform = array('web');
	private $_pub_channel = array('zaker','163','toutiao','sohunew','qqnew','xianguo');
	private $_default_parm = array(
		'zaker' => array(
			'rss_file' => 'graphmovie_zaker.xml',
			'show_readmore' => '[查看原文]',
			'show_adv' => 0,
			'show_contact' => 0,
			'desc_output' => 1
		),
		'163' => array(
			'rss_file' => 'graphmovie_163.xml',
			'show_readmore' => '阅读原文',
			'show_adv' => 0,
			'show_contact' => 0
		),
		'toutiao' => array(
			'rss_file' => 'graphmovie_toutiao.xml',
			'show_readmore' => '阅读原文',
			'show_adv' => 0,
			'show_contact' => 0
		),
		'sohunew' => array(
			'rss_file' => 'graphmovie_sohunew.xml',
			'show_readmore' => '阅读原文',
			'show_adv' => 0,
			'show_contact' => 0
		),
		'qqnew' => array(
			'rss_file' => 'graphmovie_qqnew.xml',
			'show_readmore' => '阅读原文',
			'show_adv' => 0,
			'show_contact' => 0
		),
		'xianguo' => array(
			'rss_file' => 'graphmovie_xianguo.xml',
			'show_readmore' => '阅读原文',
			'show_adv' => 0,
			'show_contact' => 0
		)
	);
	private $_parm = array('zone','showtime','tag','sort','skip','base_time','limit','jingall','show_readmore','show_adv','show_contact');
	public function create($option){
		$content = '';
		foreach ($option as $opt){
			if (in_array($opt['pub_platform'], $this->_pub_platform) && in_array($opt['pub_channel'], $this->_pub_channel)){
				$data = array(
					'pub_platform' => $opt['pub_platform'],
					'pub_channel'  => $opt['pub_channel'],
					'ver'          => isset($opt['ver']) ? $opt['ver'] : 0,
//					'zone'         => isset($opt['zone']) ? $opt['zone'] : 0,
//					'showtime'     => isset($opt['showtime']) ? $opt['showtime'] : 0,
//					'tag'          => isset($opt['tag']) ? $opt['tag'] : 0,
//					'sort'         => isset($opt['sort']) ? $opt['sort'] : 0,
//					'skip'         => isset($opt['skip']) ? $opt['skip'] : 0,
//					'base_time'    => isset($opt['base_time']) ? $opt['base_time'] : '',
//					'limit'        => isset
				);
				
				$data = array_merge($this->_default_parm[$opt['pub_channel']], $data);
				
				foreach ($this->_parm as $parm) {
					if (isset($opt[$parm])){
						$data [$parm] = $opt[$parm];
					}
				}
				
				$url = C('GET_API.create_rss');
    			$request = getHttpClient($url,$data,'get');
    			if (empty($request)) {
    				$content .= $opt['pub_platform'] . ' 平台，'. $opt['pub_channel'].' 渠道'.'生成RSS 接口调取失败<br />';
    			} else {
    				$content .= '<br />'.strip_tags(trim($request->getContent()));
    			}
			}
		}
		return $content;
	}
}