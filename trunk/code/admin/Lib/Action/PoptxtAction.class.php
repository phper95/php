<?php
// 用户反馈模块
class PoptxtAction extends CommonAction {
    function _filter(&$map){
//        $map['status'] = array('egt',0);
//        $map['account'] = array('like',"%".$_POST['name']."%");
    }
    
	public function index() {
		$this->display();
    }
    
    public function advList() {
    	$this->_getList('PoptxtAdv');
    }
    
    public function movieList() {
    	$this->_getList('PoptxtMovie');
    }
    
    /**
     * 根据类型展示弹幕
     * Enter description here ...
     */
    public function showInfo(){
    	$type = I('type');
    	$id = I('id');
    	if (empty($id)) {$this->error('参数错误');}
    	if ($type == 'movie') { // 展示影片的弹幕
    		$movie = D('Movie')->where(array('id'=>$id))->field('id,name,imgserver_use')->find();
    		$model = D('PoptxtMovie');
    		$map = array('movie_id' => $id);
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