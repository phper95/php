<?php
//电影 占坑 类
class Movie_Pit{
	private $_state_values = array(
		'APPLY' => 0, // 申请中
		'PITTING' => 1, // 正在填坑
		'APPLY_UNDO' => 2, // 申请弃坑
	
		'APPLY_FAIL' => 10, // 申请失败
		'APPLY_UNDO_SUCC' => 11, // 弃坑成功
		'APPLY_UNDO_FAIL' => 12, // 弃坑失败
		'DEADLINE' => 13, // 过期未处理
	
		'SUCC' => 20 // 如期交稿成功 
	);
	private $_state_names = array(
		'APPLY' => '申请中',
		'PITTING' => '正在填坑',
		'APPLY_UNDO' => '申请弃坑',
	
		'APPLY_FAIL' => '申请失败',
		'APPLY_UNDO_SUCC' => '弃坑成功',
		'APPLY_UNDO_FAIL' => '弃坑失败',
		'DEADLINE' => '过期未处理',
	
		'SUCC' => '如期交稿成功'
	);
	
	public function getStateList($arr= array()){
		$arr = empty($arr) ? array_keys($this->_state_values) : $arr;
		$rst = array();
		foreach ($this->_state_values as $key=>$v) {
			if (in_array($key, $arr)){
				$rst[$v] = $this->_state_names[$key];
			}
		}
		return $rst;
	}
	
	/**
	 * 获取可操作列表
	 * Enter description here ...
	 * @param unknown_type $state
	 */
	public function getOptByState($state){
		$sta = $this->_state_values;
		if ($state == $sta['APPLY']) { // 申请中。。。
			return $this->getStateList(array('APPLY','PITTING','APPLY_FAIL'));
		} else if ($state == $sta['PITTING']){ // 填坑中。。。
			return $this->getStateList(array('PITTING','APPLY_UNDO_SUCC','SUCC'));
		} else if ($state == $sta['APPLY_UNDO']) { // 申请弃坑
			return $this->getStateList(array('APPLY_UNDO','APPLY_UNDO_SUCC','APPLY_UNDO_FAIL'));
		} else {
			return NULL;
		}
	}
	
}