<?php
// 自动执行调用模块
class StaticFileAction extends CommonAction {
	private $_rank_file = '../../appweb/rank.html';
	private $_soap_file = '../../appweb/soaptop.php';
	private $_soap_user_file = '../../appweb/base/config/soap_users.php';
	
	
	function index(){
		$this->display();
	}
	
	/**
	 * 生成评级静态页面
	 * Enter description here ...
	 */
	function rank(){
		$model = D('MovieRankRecord');
		$last_time = date('Y-m-d 00:00:00', strtotime('-3month'));
		$map = array('add_time'=>array('gt',$last_time));
		$list = $model->where($map)->order('id desc')->select();
		$rst = array();
		$rank_type = array('0'=>'影片','1'=>'电影','2'=>'剧集');
		if (!empty($list)){
			$tpl = file_get_contents('static/rank.tpl');
			$html = '';
			$i = 0;
			foreach ($list as $key=>$val1) {
				if ($val1['add_time']<$last_time) break; 
				$a = json_decode($val1['rank_a'], true); // 神作
				$b = json_decode($val1['rank_b'], true); // 震精
				$c = json_decode($val1['rank_c'], true); // 略屌
				$movie_a_id = $this->_getMovieInfo($a);
				$movie_b_id = $this->_getMovieInfo($b);
				$movie_c_id = $this->_getMovieInfo($c);
				
				$all_movie_id = array_merge($movie_a_id, $movie_b_id, $movie_c_id);
				if (empty($all_movie_id)) continue;
				$i++;
				$movies = D('Movie')->where(array('id'=>array('in',$all_movie_id)))->getField('id,name,grapher');
				if (empty($movies)) { 
					$this->error('数据库错误');
				} else {
					$grapher_ids = array();
					foreach ($movies as $val) {
						$grapher_ids[$val['grapher']] = true;
					}
					$graphers = D('Member')->where(array('id'=>array('in', array_keys($grapher_ids))))->getField('id,name',true);
				}
				
				$html = $html 
					  . '<div class="pjjg" id="pjjg_'.$i.'"><article>'
					  . '<a class="ui-link"><h4 class="noImage" style="padding-bottom:2px;"><strong>【'.date('Y 年m 月d 日',strtotime($val1['add_time'])).'】 <span style="color:#00b9ff;font-size:1em;">'.$rank_type[$val1['rank_type']].'</span> の评级结果：</strong></h4></a>';
				$html = $html . '<span >【神作】阅读数 > '.floor($val1['rank_a_mr']).',点赞比 > '.toPercent($val1['rank_a_mlr'],4).'</span>';
				$html .= "<div class='clear'></div>";
				$html = $html . '<span >【震精】阅读数 > '.floor($val1['rank_b_mr']).',点赞比 > '.toPercent($val1['rank_b_mlr'],4).'</span>';
				$html .= "<div class='clear'></div>";
				$html = $html . '<span >【略屌】阅读数 > '.floor($val1['rank_c_mr']).',点赞比 > '.toPercent($val1['rank_c_mlr'],4).'</span>';
				$html .= "<div class='clear'></div><hr></hr>";
				
//				$html .= '<span ><strong>被评为【神作】的影片有</strong></span>';
				$html .= '<div  style="padding-top:10px;">';
				if (!empty($a)) {
					foreach ($a as $val) {
						$m_id = $val['m'];
						$html = $html
							  . '<div class="movie_list" boo="'.$m_id.'">'
							  . '<div class="movie">' . (strstr($movies[$m_id]['name'],"《") ? $movies[$m_id]['name'] : ("《".$movies[$m_id]['name']."》") ).'  </div><div class="shen">神作</div>'
							  . '<div class="clear"></div>' 
							  . '<div class="member">'.$graphers[$movies[$m_id]['grapher']].'</div>'
							  . '<div class="like"> '. (empty($val['l']) ? '-' : $val['l']) .'</div><div class="played">' .(empty($val['p']) ? '-' : getFriendlyNum($val['p'])) .'</div><div class="hr"></div>'
							  . '</div>'
							  ;
					}
				}
				$html .= '</div>';
				
//				$html = $html . '<br /><span ><strong>被评为【震精】的影片有</strong></span>';
				$html .= '<div  >';
				if (!empty($b)) {
					foreach ($b as $val) {
						$m_id = $val['m'];
						$html = $html
							  . '<div class="movie_list" boo="'.$m_id.'">'
							  . '<div class="movie">' . (strstr($movies[$m_id]['name'],"《") ? $movies[$m_id]['name'] : ("《".$movies[$m_id]['name']."》") ).'  </div><div class="jin">震精</div>'
							  . '<div class="clear"></div>'
							  . '<div class="member">'.$graphers[$movies[$m_id]['grapher']].'</div>'
							  . '<div class="like"> '. (empty($val['l']) ? '-' : $val['l']) .'</div><div class="played">' .(empty($val['p']) ? '-' : getFriendlyNum($val['p'])) .'</div><div class="hr"></div>'
							  . '</div>'
							  ;
					}
				}
				$html .= '</div>';
				
//				$html = $html . '<br /><span ><strong>被评为【略屌】的影片有</strong></span>';
				$html .= '<div  >';
				if (!empty($c)) {
					foreach ($c as $val) {
						$m_id = $val['m'];
						$html = $html
							  . '<div class="movie_list" boo="'.$m_id.'">'
							  . '<div class="movie">' . (strstr($movies[$m_id]['name'],"《") ? $movies[$m_id]['name'] : ("《".$movies[$m_id]['name']."》") ).'  </div><div class="diao">略屌</div>'
							  . '<div class="clear"></div>'
							  . '<div class="member">'.$graphers[$movies[$m_id]['grapher']].'</div>'
							  . '<div class="like"> '. (empty($val['l']) ? '-' : $val['l']) .'</div><div class="played">' .(empty($val['p']) ? '-' : getFriendlyNum($val['p'])) .'</div><div class="hr"></div>'
							  . '</div>'
							  ;
					}
				}
				$html .= '</div>';
				
				$html .= '</article></div>';
			}
			$tpl = str_replace('{$html}', $html, $tpl);
			$tpl = str_replace('{$title}', '图解の封神榜', $tpl);
			
//			file_put_contents('../appweb/pjjg.php', $tpl);
			file_put_contents($this->_rank_file, $tpl);
		}
		$this->success("生成OK");
	}
	
	/**
	 * 生成肥皂榜
	 * Enter description here ...
	 */
	public function soapTopten(){
		$model = D('SoapTopten');
		$list = $model->order('id desc')->select();
		$rst = array();
		if (!empty($list)){
			$tpl = file_get_contents('static/soap.tpl');
			$html = '';
			$i = 0;
			$top_m = $top_w = $top_y = array();
			foreach ($list as $item) {
				$b_time = strtotime($item['b_time']);
				$s_time = strtotime($item['e_time']);
				$cha = $s_time - $b_time;
				if ($cha > 15552000){ // 如果大于180 天，算年榜
					$top_y[] = $item;
				} else if ($cha > 1728000) { // 如果大于20天，算月榜
					$top_m[] = $item;
				} else { //否则算周榜
					$top_w[] = $item;
				}
			}
			$memeber = D('Member');
			$soapUMember = D('SoapUMember');
			$mComment = D('MComment');
			$movie = D('Movie');
			$movieArr = array();
			$memArr = array();
			if (!empty($top_w)) {
				foreach ($top_w as $i=>$value){
					$tmp = explode(' ', $value['b_time']);
					$b_day = $tmp[0];
					$tmp = explode(' ', $value['e_time']);
					$e_day = $tmp[0];
					$users = json_decode($value['users'], true);
					$item_html = '<div class="soap_week" id="soap_week_'.($i+1).'">'
								. '<article><a class="ui-link">'
								. '<h4 class="noImage" style="padding-bottom: 2px;"><strong>【'.$b_day.' 至 '. $e_day . '】捡肥皂次数排行</strong></h4>'
								. '<h4 class="noImage" style="padding-bottom: 2px;"><strong>愿君多采撷，此物最相思。</strong></h4>'
								. '<h4 style="padding-bottom: 2px;" class="noImage"><strong>恭喜{$users}获得本周肥皂榜冠军！</strong></h4>'
								. '</a>'
								. '<hr/>'
								. '<div>'
									 . '<div style="float:left;width:74%;color:#cb5432;font-size:12px;">'
									    . '<div style="padding:20px;">本期冠军奖品：<br />腾讯动漫《尸兄》官方提供肥皂一枚</div>'
									  . '</div>'
									  . '<div style="float:left;width:25%;">'
									    . '<img src="images/prize_w.png" width="100%" />'
									  . '</div>'
								. '</div>'
								. '<div class="clear"></div>'
								. '<div class="hr"></div>'
								. '<div style="padding-top: 10px;">';
					$top_index = $i*10 + 1;
					$topUsers = array(); //获奖得主
					$max_nums = 0;
					foreach ($users as $user_id=>$soap_v_user_ids) {
						if (!isset ($memArr[$user_id])){
							$map = array('id'=>$user_id);
							$one = $memeber->where($map)->field('avatar,name,email')->find();
							$memArr[$user_id] = $one;
						}
						$nums = count($soap_v_user_ids);
						if (($top_index%10)==1) {
							$topUsers[] = $memArr[$user_id]['name'];
							$max_nums = $nums;
						} else if ($nums == $max_nums) {
							$topUsers[] = $memArr[$user_id]['name'];
						}
						$user_index = $top_index%10;
						$user_index = $user_index == 0 ? 10 : $user_index;
						$item_html .= '<div class="item" data-pindex="'.$top_index.'">'
									. '<div class="nums">'.$nums.'<span>块</span></div>'
									. '<div class="index" '.($user_index>3?'style="color:#666;"':'').'>'.$user_index.'</div>'
									. '<div class="user">'
									. '<img class="avatar" src="'.$memArr[$user_id]['avatar'].'"/>'
									. '<div class="name" style="position:relative;">'.(empty($memArr[$user_id]['email'])?($memArr[$user_id]['name'].'<div style="position:absolute;border:3px solid #ff00a0;border-radius:4px;height:0px;width:100%;top:18px;left:0px;transform:rotate(6deg);"></div>'):$memArr[$user_id]['name']).'</div>'
									. '</div>'
									. '<div class="clear"></div>'
									. '<div id="pick_info_'.$top_index.'" class="pick_info_item">';
						foreach ($soap_v_user_ids as $soap_v_user_id) {
							$map = array('id'=>$soap_v_user_id);
							$one = $soapUMember->where($map)->field('vol_id,add_time')->find();
							$item_html .= '<div class="pick_info">'
										. '<div class="pick_time">'.date('m-d H:i', strtotime($one['add_time'])).'</div>'
										. '<div class="time_ge"></div>';
							if (empty($one['vol_id'])){
								$item_html .= '<div class="pick_reason">&nbsp;&nbsp;捡起了蜀黍的肥皂</div>';
							} else {
								$map = array('id'=>$one['vol_id']);
								$one = $mComment->where($map)->field('movie_id,pindex')->find();
								if (!isset($movieArr[$one['movie_id']])){
									$map = array('id'=>$one['movie_id']);
									$movieArr[$one['movie_id']] = $movie->where($map)->getField('name');
								}
								$item_html .= '<div class="pick_reason">《'.$movieArr[$one['movie_id']].'》 - '.($one['pindex'] + 1).' 页</div>';
							}
							$item_html .= '<div class="clear"></div>'
										. '</div>';
							
						}
						$top_index ++;
						$item_html .= '</div>'
									. '<hr /></div>';
					}
					$item_html .= '</article>'
								. '</div>';
					
					$html .= $item_html;
					$html = str_replace('{$users}',' “'.implode('” 与 “', $topUsers).'” ', $html);
				}
				$tpl = str_replace('{$week}', $html, $tpl);
			}
			
			if (!empty($top_m)) { // 如果月榜不为空的话，则生成月榜
				$html = '';
				$first = true;
				foreach ($top_m as $i=>$value) {
					$tmp = explode(' ', $value['b_time']);
					$b_day = $tmp[0];
					$tmp = explode(' ', $value['e_time']);
					$e_day = $tmp[0];
					$users = json_decode($value['users'], true);
					
					$top_index = $i*10 + 1;
					$tmp_users = array(); // 记录名次信息
					foreach ($users as $user_id =>$soap_v_user_ids) {
						$index = $top_index%10;
						$tmp_users[$index] = $memArr[$user_id];
						$tmp_users[$index]['nums'] = count($soap_v_user_ids);
						$pick_inf_html = '';
						foreach ($soap_v_user_ids as $soap_v_user_id) {
							$map = array('id'=>$soap_v_user_id);
							$one = $soapUMember->where($map)->field('vol_id,add_time')->find();
							$pick_inf_html .= '<div class="pick_info">'
											. '<div class="pick_time">'.date('m-d H:i', strtotime($one['add_time'])).'</div>'
											. '<div class="time_ge"></div>';
							if (empty($one['vol_id'])){
								$pick_inf_html .= '<div class="pick_reason">&nbsp;&nbsp;捡起了蜀黍的肥皂</div>';
							} else {
								$map = array('id'=>$one['vol_id']);
								$one = $mComment->where($map)->field('movie_id,pindex')->find();
								if (!isset($movieArr[$one['movie_id']])){
									$map = array('id'=>$one['movie_id']);
									$movieArr[$one['movie_id']] = $movie->where($map)->getField('name');
								}
								$pick_inf_html .= '<div class="pick_reason">《'.$movieArr[$one['movie_id']].'》 - '.($one['pindex'] + 1).' 页</div>';
							}
							$pick_inf_html .= '<div class="clear"></div>'
										. '</div>';
							
						}
						$tmp_users[$index]['html'] = $pick_inf_html;
						$top_index++;
					}
					$item_html = '<div class="soap_month" id="soap_month_'.($i+1).'" '.($first ? '' : 'style="display:none"').'>';
					$first = false;
					$item_html .= '<article>'
								. '<a class="ui-link"><h4 style="padding-bottom: 2px;" class="noImage"><strong>【'.$b_day.'至 '.$e_day.'】捡肥皂次数排行</strong></h4><h4 style="padding-bottom: 2px;" class="noImage"><strong>愿君多采撷，此物最相思。</strong></h4><h4 class="noImage" style="padding-bottom: 2px;"><strong>恭喜 “'.$tmp_users[1]['name'].'” 获得本月肥皂榜冠军！</strong></h4></a>'
								. '<hr/>'
								. '<div>'
									 . '<div style="float:left;width:74%;color:#cb5432;font-size:12px;">'
									    . '<div style="padding:20px;">冠军获得：'.($i==0?'小米官方提供<br />听歌神器小米活塞耳机一副':'《万万没想到》官方特供<br />王大锤U盘创意弹出式8G U盘一个').'</div>'
									  . '</div>'
									  . '<div style="float:left;width:25%;">'
									    . '<img src="images/'.($i==0?'prize_m_1_1.png':'prize_m_1.png').'" width="100%" />'
									  . '</div>'
								. '</div>'
								. '<div class="clear"></div><hr/>'
								. '<div>'
									 . '<div style="float:left;width:74%;color:#cb5432;font-size:12px;">'
									    . '<div style="padding:20px;">亚军获得：「有妖气」官方特供<br />《十万个冷笑话》大娃雨伞（晴雨两用）</div>'
									  . '</div>'
									  . '<div style="float:left;width:25%;">'
									    . '<img src="images/prize_m_2.png" width="100%" />'
									  . '</div>'
								. '</div>'
								. '<div class="clear"></div><hr/>'
								. '<div>'
									 . '<div style="float:left;width:74%;color:#cb5432;font-size:12px;">'
									    . '<div style="padding:20px;">季军获得：「有妖气」官方特供<br />《十万个冷笑话》哪吒胸肌鼠标垫</div>'
									  . '</div>'
									  . '<div style="float:left;width:25%;">'
									    . '<img src="images/prize_m_3.png" width="100%" />'
									  . '</div>'
								. '</div>'
								. '<div class="clear"></div><hr>'
								. '</article>';
					$item_html .= '<div style="position:relative;width:98%;margin:0px auto;">'
									. '<div class="soap_month_flagg item month-2" data-pindex="month_'.$top_index.'1">'
										. '<div class="targ">'
											. '<div class="m_user">'
												. '<img style="border-radius:50%;border:0px solid #eee;" src="'.$tmp_users[2]['avatar'].'" width="100%;"/><br />'
												. $tmp_users[2]['nums'] .'块'
											. '</div>'
											. '<div class="name">'.$tmp_users[2]['name'].'</div>'
										. '</div>'
									. '</div>'
									. '<div class="soap_month_flagg item month-1" data-pindex="month_'.$top_index.'2">'
										. '<div class="targ">'
											. '<div class="m_user">'
												. '<img style="border-radius:50%;border:0px solid #eee;" src="'.$tmp_users[1]['avatar'].'" width="100%;"/><br />'
												. $tmp_users[1]['nums'] .'块'
											. '</div>'
											. '<div class="name">'.$tmp_users[1]['name'].'</div>'
										. '</div>'
									. '</div>'
									. '<div class="soap_month_flagg item month-3" data-pindex="month_'.$top_index.'3">'
										. '<div class="targ">'
											. '<div class="m_user">'
												. '<img style="border-radius:50%;border:0px solid #eee;" src="'.$tmp_users[3]['avatar'].'" width="100%;"/><br />'
												. $tmp_users[3]['nums'] .' 块'
											. '</div>'
											. '<div class="name">'.$tmp_users[3]['name'].'</div>'
										. '</div>'
									. '</div>'
								. '</div>'
							. '</div>';
					$item_html .= '<div class="clear"></div>';
					$item_html .= '<a id="headxxxxx'.($i+1).'"></a>';
					$item_html .= '<div class="pick_info_month_item" id="pick_info_month_'.$top_index.'1" style="display: none;">'
									. '<div style="margin-top:10px;">'
										. '<div class="nums">'.$tmp_users[2]['nums'].'<span>块</span></div>'
										. '<div class="index">2</div>'
										. '<img src="'.$tmp_users[2]['avatar'].'" class="avatar">'
										. '<div style="position:relative;" class="name">'.$tmp_users[2]['name'].'</div>'
									. '</div>'
									. '<div class="clear"></div>'
									. $tmp_users[2]['html']
								. '</div>'
								. '<div class="pick_info_month_item" id="pick_info_month_'.$top_index.'2" style="display: none;">'
									. '<div style="margin-top:10px;">'
										. '<div class="nums">'.$tmp_users[1]['nums'].'<span>块</span></div>'
										. '<div class="index">1</div>'
										. '<img src="'.$tmp_users[1]['avatar'].'" class="avatar">'
										. '<div style="position:relative;" class="name">'.$tmp_users[1]['name'].'</div>'
									. '</div>'
									. '<div class="clear"></div>'
									. $tmp_users[1]['html']
								. '</div>'
								. '<div class="pick_info_month_item" id="pick_info_month_'.$top_index.'3" style="display: none;">'
									. '<div style="margin-top:10px;">'
										. '<div class="nums">'.$tmp_users[3]['nums'].'<span>块</span></div>'
										. '<div class="index">3</div>'
										. '<img src="'.$tmp_users[3]['avatar'].'" class="avatar">'
										. '<div style="position:relative;" class="name">'.$tmp_users[3]['name'].'</div>'
									. '</div>'
									. '<div class="clear"></div>'
									. $tmp_users[3]['html']
								. '</div>';
					$html .= $item_html;
				}
				$tpl = str_replace('{$month}', $html, $tpl);
			}
			file_put_contents($this->_soap_file, $tpl);
			$user_ids = array();
			foreach ($memArr as $k=>$v) {
				$user_ids[] = userIdKeyEncode($k); 
			}
			file_put_contents($this->_soap_user_file, "<?php return array('".implode("','",$user_ids)."');");
		} else {
			$this->error('没有数据，怎么发布？');
		}
		$this->success('发布成功！');
	}
	
	private function _getMovieInfo ($arr){
		$movie_id = array();
		if (!empty($arr)) {
			foreach ($arr as $val) {
				$movie_id[] = $val['m'];
			}
		}
		return $movie_id;
	}
}