<?php
// 用户反馈模块
class PoptxtAction extends CommonAction {
	private $_msg = array(
		1 => '该用户因发广告，已被封印',
		2 => '该用户因剧透，已被封印',
		3 => '该用户因辱骂，已被封印',
		4 => '该记录因违规，已被封印'
	);
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
	public function index() {
		$this->display();
    }
    
    public function advList() {
    	$alert = I('get.alert',false);
    	$this->_getList('PoptxtAdv',$alert);
    }
    
    public function movieList() {
    	$alert = I('get.alert',false);
    	$this->_getList('PoptxtMovie',$alert);
    }
    
    public function heXie() {
    	$id = I('get.id');
    	$reason = I('reason');
    	$type = I('get.type');
    	$hexie = I('get.hexie');
    	$rst = array('rst'=>0, 'msg'=>'');
    	$model_name = array(
    		'movie'=>array('poptxt'=>'PoptxtMovie','bk'=>'PoptxtMovieBk'), 
    		'adv'=>array('poptxt'=>'PoptxtAdv','bk'=>'PoptxtAdvBk')
    	);
    	if (empty($type) || empty($id) || empty($reason) || !isset($model_name[$type]) || !isset($this->_msg[$reason])) {
    		$rst['msg'] = '参数错误';
    	} else {
    		$tmp = $model_name[$type];
    		$poptxt = D($tmp['poptxt']);
    		$poptxt_bk = D($tmp['bk']);
    		$content = $this->_msg[$reason];
    		$rst['data']['ids'] = array();
    		if ($hexie == 'all') {
    			$map = array('id'=>$id);
    			$one = $poptxt->where($map)->field('user_id,movie_id')->find();
    			$map = array('user_id'=>$one['user_id'], 'movie_id'=>$one['movie_id']);
    			$ids = $poptxt->where($map)->getField('id', true);
    			$error = false;
    			foreach ($ids as $tmp_id) {
    				$msg = $this->_updateContent($poptxt, $poptxt_bk, $tmp_id, $content);
    				if (is_string($msg)){
    					$rst['msg'] = '更新ID '.$tmp_id.' 时出错 '.$msg;
    					$error = true; 
    					break;
    				} else {
    					if ($msg === true) {
    						$rst['data']['ids'][] = array('id'=>$tmp_id,'content'=>$content);
    					}
    				}
    			}
    			$rst['rst'] = $error ? 0 : 1;
    		} else {
	    		$msg = $this->_updateContent($poptxt, $poptxt_bk, $id, $content);
	    		if (is_string($msg)) {
	    			$rst['msg'] = $msg;
	    		} else {
	    			if ($msg === false) {
	    				$rst['msg'] = '已经替换过了！';
	    			} else {
		    			$rst['data']['ids'][] = array('id'=>$id,'content'=>$content);
		    			$rst['rst'] = 1;
	    			}
	    		}
    		}
    	}
    	$this->ajaxReturn($rst);
    }
    
 	
    /**
     * 修改内容
     * Enter description here ...
     * @param unknown_type $poptxt
     * @param unknown_type $poptxt_bk
     * @param unknown_type $id
     * @param unknown_type $content
     */
    public function _updateContent($poptxt, $poptxt_bk, $id, $content) {
    	$map = array('poptxt_id'=>$id);
    	$one = $poptxt_bk->where ($map)->find();
    	if (!empty($one)) {return false;}
		$map = array ('id' => $id );
		$tmp_content = $poptxt->where ( $map )->getField ( 'comment_content' );
		$data = array ('poptxt_id' => $id, 'comment_content' => $tmp_content );
		$poptxt_bk->create ( $data );
		if (false === $poptxt_bk->add ()) {
			return '备份信息出错';
		} else {
			$data = array ('id' => $id, 'comment_content' => $content );
			if (false === $poptxt->save ( $data )) {
				return '修改内容出错';
			} else {
				return true;
			}
		}
	}
    
    public function view(){
    	$type = I('type');
    	$id = I('id');
    	if (empty($id) || empty($type)) {$this->error('参数错误');}
    	if ($type == 'movie') { // 影片的弹幕
    		$poptxt = D('PoptxtMovie')->where(array('id'=>$id))->find();
    		$page_index = $poptxt['page_index'];
    		$img = D('MComment')->where(array('id'=>$page_index))->find();
    		$member = D('Member')->where(array('id'=>$poptxt['user_id']))->field('name,avatar')->find();
    		$movie = D('Movie')->where(array('id'=>$poptxt['movie_id']))->field('name,imgserver_use')->find();
    		$vo = $poptxt;
    		$vo['img'] = otherURL2ServerUrl($img['image'],$poptxt['movie_id'],$movie['imgserver_use']);
    		$vo['intro'] = $img['intro'];
    		$vo = array_merge($vo, $member);
    		$vo ['movie_name'] = $movie['name'];
    		$this->assign('vo', $vo);
    	}
    	$this->display();
    }
    
    /**
     * 根据类型展示弹幕
     * Enter description here ...
     */
    public function showInfo(){
    	$type = I('type');
    	$id = I('id');
    	$member_id = I('user_id',null);
    	if (empty($id)) {$this->error('参数错误');}
    	if ($type == 'movie') { // 展示影片的弹幕
    		$movie = D('Movie')->where(array('id'=>$id))->field('id,name,imgserver_use')->find();
    		$model = D('PoptxtMovie');
    		$map = array('movie_id' => $id);
    		if (!empty($member_id)){
    			$map['user_id'] = $member_id;
    		}
    		$list = $model->where($map)->select();
//			$list = $this->_getCommentList($model, $map, 'page_index', true);
    		if (!empty($list)) {
    			$poptxtArr = array();
    			foreach ($list as $item) {
    				$poptxtArr[$item['page_index']][] = $item;
    			}
    			$page_index_ids = $this->_getCommentList(array_keys($poptxtArr));
    			
    			$pages = D('MComment')->where(array('id'=>array('in', $page_index_ids)))->getField('id,image,pindex,intro');
    			
    			foreach ($pages as $page) {
    				$tmp[$page['id']] = $page['pindex'];
    			}
    			asort($tmp);
    			$xx = array();
    			foreach ($tmp as $key=>$v) {
    				$xx[$key] = $poptxtArr[$key];
    			}
    			
    			// 获取名字
    			$nameArr = array();
    			foreach ($xx as $key=>$v) {
    				foreach ($v as $val) 
    					$nameArr[$val['user_id']] = $val['user_id'];
    			}
    			if (!empty($nameArr)) {
    				$nameArr = D('Member')->where(array('id'=>array('in',$nameArr)))->getField('id,name,avatar');
    			}
    			
    			$this->assign('members', $nameArr);
    			$this->assign('images', $pages);
    			$this->assign('movie', $movie);
    			$this->assign('poptxt', $xx);
    		}
    	} else if ($type == 'adv') { // 展示广告的弹幕
    		$movie = D('Adv')->where(array('id'=>$id))->field('id,name,imgserver_use')->find();
    		$model = D('PoptxtAdv');
    		$map = array('adv_id' => $id);
    		if (!empty($member_id)){
    			$map['user_id'] = $member_id;
    		}
    		$list = $model->where($map)->select();
    		if (!empty($list)) {
    			$poptxtArr = array();
    			foreach ($list as $item) {
    				$poptxtArr[$item['page_index']][] = $item;
    			}
    			$page_index_ids = $this->_getCommentList(array_keys($poptxtArr));
    			
    			$pages = D('AdvComment')->where(array('id'=>array('in', $page_index_ids)))->getField('id,image,pindex,intro');
    			
    			foreach ($pages as $page) {
    				$tmp[$page['id']] = $page['pindex'];
    			}
    			asort($tmp);
    			$xx = array();
    			foreach ($tmp as $key=>$v) {
    				$xx[$key] = $poptxtArr[$key];
    			}
    			
    			// 获取名字
    			$nameArr = array();
    			foreach ($xx as $key=>$v) {
    				foreach ($v as $val) 
    					$nameArr[$val['user_id']] = $val['user_id'];
    			}
    			if (!empty($nameArr)) {
    				$nameArr = D('Member')->where(array('id'=>array('in',$nameArr)))->getField('id,name');
    			}
    			
    			$this->assign('members', $nameArr);
    			$this->assign('images', $pages);
    			$this->assign('movie', $movie);
    			$this->assign('poptxt', $xx);
    		}
    	} else {
    		{$this->error('参数错误');}
    	}
    	$this->display();
    }
    
    private function _getList($name, $alert=FALSE) {
    	$map = $this->_search($name);
		$s_time = $_REQUEST['s_time'];
		$e_time = $_REQUEST['e_time'];
		
		if ($alert) {
			$map['be_alert'] = array('GT',0);
		}
		
		if (!empty($s_time))
			$map['add_time'] = array('EGT',$s_time);
		if (!empty($e_time)){
			$e_time .= " 23:59:59";
			$map['add_time'] = empty($map['add_time']) ? array('ELT',$e_time) : array ('BETWEEN',array($s_time, $e_time));
		}

        $model = D($name);
        if (!empty($model)) {
           
           $this->_list($model, $map);
        }
        $this->display();
        return;
    }
    
	function _getCommentList($ids) {
		sort($ids);
        //取得满足条件的记录数
        $voList = array();
        $count = count($ids);
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            
            $lastRow = min(array($p->listRows+$p->firstRow, $count));
            for ($i=$p->firstRow; $i<$lastRow; $i++) {
            	$voList[] = $ids[$i];
            }

            //分页显示
            $page = $p->show();
            //列表排序显示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('sort', $sort);
            $this->assign("page", $page);
        }
        return $voList;
    }
    
    
    
}
?>