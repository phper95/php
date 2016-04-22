<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends CommonAction {
	private $t_size = 0;
    private $_t_info = array();
    		
    public function index(){
    	$df = disk_free_space("/");
    	$this->assign('df_size',$df);
    	if ($df < 5000000000) { //如果小于5G，则预警
    		$this->assign('df_warning', true);
    	}
		$this->display();
    }
    
    public function diskManage() {
    	$log_path = '../../logger';
    	$do = I('do', null);
    	if (isset($do)) {
    		$this->_readDir($log_path, true);
    		$this->success('清理成功，共清理 '.getFomartFileSize($this->t_size).'空间', U('Index/index'));
    	} else {
			$this->_readDir($log_path);
			$this->assign('t_size',$this->t_size);
			$this->assign('t_info',$this->_t_info);
			$this->display();
    	}
	}
	
	private function _readDir($dir, $del=false){
		//先删除目录下的文件：
		$dh = opendir ( $dir );
		while ( $file = readdir ( $dh ) ) {
			if ($file != "." && $file != "..") {
				$fullpath = $dir . "/" . $file;
				if (! is_dir ( $fullpath )) {
					$dir = dirname($fullpath);
					$f_size = filesize($fullpath);
					if (is_dir($dir.'/logger_manager_deal')) {
						$this->t_size += $f_size;
						if ($del) {
							@unlink($fullpath);
						}
						$this->_t_info['ok'][] = $fullpath.' ===> '.getFomartFileSize($f_size);
					} else {
						$this->_t_info['no'][] = $fullpath.' ===> '.getFomartFileSize($f_size);
					}
				} else {
					if (strstr($fullpath, 'logger_manager_deal') || strstr($fullpath, 'dwmynar_users')){
					} else {
						$this->_readDir($fullpath, $del);
					}
				}
			}
		}
	}
}