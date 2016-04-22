<?php

class CommonAction extends Action {
    function _initialize() {
    	define('NOW_DATE', date('Y-m-d H:i:s', NOW_TIME));
    	$user_id = userIdKeyDecode(I('userid'));
    	if (empty($user_id)) {
    		$this->error('error[0001]',C('DEFAULT_ERROR_URL'));
    	}
        if (ACTION_NAME != 'index' && MODULE_NAME != 'Index') {
        	if (session($user_id) != $user_id){
        		$this->error('error[0002]', C('DEFAULT_ERROR_URL'));
        	}
        }
    }
    
    protected function error($message='',$jumpUrl='',$ajax=false) {
    	if ($this->isAjax()) {
    		$rst = array();
    		$rst['rst'] = 1;
    		if (is_array($message)) {
    			$rst['msg'] = $message['msg'];
    			unset($message['msg']);
    			$rst['data'] = $message;
    		} else {
    			$rst['msg'] = $message;
    		}
    		$this->ajaxReturn($rst);
    	} else {
    		parent::error($message, $jumpUrl, $ajax);
    	}
    }
    
    
    protected function success($message='',$jumpUrl='',$ajax=false) {
    	if ($this->isAjax()) {
    		$rst = array();
    		$rst['rst'] = 0;
    		if (is_array($message)) {
    			$rst['msg'] = $message['msg'];
    			unset($message['msg']);
    			$rst['data'] = $message;
    		} else {
    			$rst['msg'] = $message;
    		}
    		$this->ajaxReturn($rst);
    	} else {
    		parent::success($message, $jumpUrl, $ajax);
    	}
    }
    
    
    protected function _fiter_time($arr) {
    	if (is_array($arr)) {
    		foreach ($arr as $post_key) {
    			if (isset($_POST[$post_key])) {
    				$_POST[$post_key] = $_POST[$post_key];
    			}
    		}
    	}
    }

    public function index() {
        
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
    
        $name = $this->getActionName();

        $model = D($name);
        if (!empty($model)) {
           
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }
    
	// 获取菜单
    public function menu() {
    }
    

    /**
      +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    function getReturnUrl() {
        return __URL__ . '?' . C('VAR_MODULE') . '=' . MODULE_NAME . '&' . C('VAR_ACTION') . '=' . C('DEFAULT_ACTION');
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $model = D($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }
}

