<?php
// 用户反馈模块
class CommentAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
	public function index() {
		$this->display();
    }
    
    public function advList() {
    	$this->_getList('CommentAdv');
    }
    
    public function movieList() {
    	$this->_getList('CommentMovie');
    }
    
    private function _getList($name) {
    	$map = $this->_search($name);
		$s_time = $_REQUEST['s_time'];
		$e_time = $_REQUEST['e_time'];
		
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
    
    
    /**
     * 根据类型展示评论
     * Enter description here ...
     */
    public function showInfo(){
    	$type = I('type');
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    	if ($type == 'movie') { // 展示影片的评论
    		$movie = D('Movie')->where(array('id'=>$id))->field('id,name,imgserver_use')->find();
    		$model = D('CommentMovie');
    		$map = array('movie_id'=>$id);
    		$list = $this->_selfList($model, $map);
    		
	    	// 获取名字
    		$nameArr = array();
    		foreach ($list as $key=>$val) {
    			$nameArr[$val['user_id']] = $val['user_id'];
    		}
    		if (!empty($nameArr)) {
    			$nameArr = D('Member')->where(array('id'=>array('in',$nameArr)))->getField('id,name,avatar');
    		}
    		
    		$this->assign('members', $nameArr);
    		$this->assign('obj',$movie);
    	} elseif ($type == 'adv') { // 广告的评论
    		$adv = D('Adv')->where(array('id'=>$id))->field('id,name,imgserver_use')->find();
    		$model = D('CommentAdv');
    		$map = array('adv_id'=>$id);
    		$list = $this->_selfList($model, $map);
    		
	    	// 获取名字
    		$nameArr = array();
    		foreach ($list as $key=>$val) {
    			$nameArr[$val['user_id']] = $val['user_id'];
    		}
    		if (!empty($nameArr)) {
    			$nameArr = D('Member')->where(array('id'=>array('in',$nameArr)))->getField('id,name,avatar');
    		}
    		
    		$this->assign('members', $nameArr);
    		$this->assign('obj',$adv);
    	} else {
    		$this->error('参数错误');
    	}
    	$this->display();
    }
    
    public function _before_add(){
    	$test_member_ids = C('TEST_USERS');
    	if (!empty($test_member_ids)) {
    		$nameArr = D('Member')->where(array('id'=>array('in',$test_member_ids)))->getField('id,name');
    		$this->assign('testMembers', $nameArr);
    	}
    	$reply_id = I('reply_id'); // 回复的ID
    	$type = I('type');
    	if (is_numeric($reply_id) && $reply_id>0) {
    		$map = array('id'=>$reply_id);
    		$content = array();
    		if ($type == 'movie') {
    			$content = D('CommentMovie')->where($map)->find();
    		}else if ($type == 'adv'){
    			$content = D('CommentAdv')->where($map)->find();
    		}
    		$this->assign('content', $content);
    	}
    }
    
    /**
     * 添加评论，通过接口上抛评论
     * Enter description here ...
     */
    public function doAdd(){
    	$type = I('type');
    	$id = I('id');
    	$reply_id = I('reply_id'); // 回复的ID
    	$ry_userid = I('ry_userid'); // 回复的用户ID
    	$user_id = I('user_id');  // 发评论的人的ID
    	$content = I('content');  // 评论内容
    	$user_id = is_numeric($user_id) ? abs($user_id) : 0;
    	if (empty($id) || empty($content) || empty($user_id)) {$this->error('参数错误');}
    	
    	$reply_id = is_numeric($reply_id) ? abs($reply_id) : 0;
    	$ry_userid = (is_numeric($ry_userid) && $ry_userid != 0) ? userIdKeyEncode(abs($ry_userid)) : 0; // 加密被回复人ID
    	
    	$user_id = userIdKeyEncode($user_id); // 加密评论人ID
    	
    	$data = array('pub_platform' => 'admin', 'pub_channel'=>'admin', 'ver'=>0);
    	$data['userid'] = $user_id;
    	$data['content'] = $content;
    	$data['reply_id'] = $reply_id;
    	$data['ry_userid'] = $ry_userid;
    	$api = C('POST_API');
    	if (empty($api)) {$this->error('系统错误咧。');}
    	if ($type == 'movie') {
    		$data['movieid'] = $id;
    		$url = $api['movie_comment']; 
    	} elseif ($type == 'adv') {
    		$data['advid'] = $id;
    		$url = $api['adv_comment'];
    	} else {
    		$this->error('参数错误');
    	}
    	
    	$request = getHttpClient($url, $data);
    	if (empty($request)) {
    		$this->error('接口挂掉了');
    	} else {
    		$rst = $request->getContent();
    		$result = json_decode($rst, true);
    		if (!is_array($result)) {
    			$this->error('接口挂掉了返回结果如下'.$rst);
    		} else {
    			if ($result['status'] == 1 && $result['sendok'] == 1) {
//    				$this->display('index'); 
	    			$backurl = I('backurl');
		    		if (!empty($backurl)) {
		    			$this->assign('jumpUrl', $backurl);
		    		} else {
		    			$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
		    		}
    				$this->success('发布成功');
    			} else {
    				$this->error('接口返回失败'.$rst);
    			}
    		}
    	}
    }
    
    
	/**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _selfList($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        $order = !empty($sortBy) ? $sortBy : $model->getPk();
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        $sort = $asc ? 'asc' : 'desc';
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
        $voList = array();
        if ($count > 0) {
            import("@.ORG.Util.Page");
            //创建分页对象
            if (!empty($_REQUEST ['listRows'])) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page($count, $listRows);
            
            //分页查询数据
            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();

            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter .= "$key=" . urlencode($val) . "&";
                }
            }
            //分页显示
            $page = $p->show();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        return $voList;
    }
}
?>