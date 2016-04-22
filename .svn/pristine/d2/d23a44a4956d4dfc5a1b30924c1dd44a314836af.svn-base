<?php
    class SingleFileSender{
        
        private $headers=array();
        private $boundary="";
        private $postData="";
        private $_content = '';
        
        /**
         * @param {Array} $url参数集
         *                 {String} path 
         *                 {String} host 
         **/
        private $urlParams=array();
        
        function __construct($url){
            $this->updateURLParams($url);
            $this->initHeaders();
        }
        
        /**
         * 设置发送的header
         * @param {String} $key
         * @param {String} $value
         */
        public function setHeader($key="",$value=""){
              $this->headers[$key]=$value;
        }
        
        /**
         * POST发送数据
         * @param {Array} $datas
         *                     array(
                                array(
                                      "postName"=>"pic",
                                      "fileName"=>"/data/a.text",
                                      "file"=>"file content",
                                    "type"=>"text/plain"
                                      ),
                                  array(
                                      "name"=>"text1",
                                      "value"=>"text1's content"
                                      )
                              ));
         **/
        public function post($datas=array()){
            $ret="";
            $this->updateBoundary();
            $postData=$this->getPostData($datas);

            $this->headers["Content-Type"]="multipart/form-data; boundary=".$this->boundary;
            $this->headers["Content-Length"]=strlen($postData);
            
            $sendContent = $this->getHeaderStr($this->headers)."\r\n".$postData;
            
            //echo $sendContent;
            
            $fp = fsockopen($this->urlParams["host"], 80, $errno, $errstr, 30);
            
            if (!$fp) {
                $ret="$errstr ($errno)<br/>\n";
            } else {
                fwrite($fp, $sendContent);
                while (!feof($fp)) {
                    $buffer = trim(fgets($fp, 1024));
                    $ret .= $buffer;
                    if (strstr($buffer, '@@@')) {
                    	$this->_content = substr($buffer, 0, (strlen($buffer)-3));
                    	break;
                    }
                }
                fclose($fp);
            }
            return !empty($ret);
        }
        
        public function getContent (){
        	return $this->_content;
        }
        
        /**
         * 初始化header
         *
         */
        private function initHeaders(){
            $this->headers["Accept"]="*/*";
            $this->headers["Connection"]="Keep-Alive";
            $this->headers["Host"]=$this->urlParams["host"];
        }
        
        /**
         * 更新boundary
         */
        private function updateBoundary(){
            $this->boundary="BOUNDARY".microtime(true)*10000;
        }
        
        /**
         * 获取要post的数据
         * @param {Array} $datas
         * @return {String} $ret
         */
        private function getPostData($datas=array()){
//            $ret="";
//            $fileData=array_shift($datas);
//            
//            $ret="--".$this->boundary."\r\n".
//                    'Content-Disposition: form-data; name="'.$fileData["postName"].'"; filename="'.$fileData["fileName"]."\"\r\n".
//                    "Content-Type: ".$fileData["type"]."\r\n\r\n".
//                    $fileData["file"]."\r\n";
//            
//            foreach($datas as $k => $v){
//                $ret.="--".$this->boundary."\r\n".
//                    'Content-Disposition: form-data; name="'.$v["name"]."\"\r\n\r\n".
//                    $v["value"]."\r\n";
//            }
//            
//            $ret.="--".$this->boundary."--\r\n";
            
        	$ret="";
            foreach($datas as $k => $v){
            	if (count($v) > 2) {
            		$ret.="--".$this->boundary."\r\n".
	                    'Content-Disposition: form-data; name="'.$v["postName"].'"; filename="'.$v["fileName"]."\"\r\n".
	                    "Content-Type: ".$v["type"]."\r\n\r\n".
	                    $v["file"]."\r\n";
            	} else {
            		$ret.="--".$this->boundary."\r\n".
	                    'Content-Disposition: form-data; name="'.$v["name"]."\"\r\n\r\n".
	                    $v["value"]."\r\n";
            	}
            }
            
            $ret.="--".$this->boundary."--\r\n";
        	
            return $ret;
        }
        
        /**
         * 更新URL参数
         * @param {String} $url
         */
        private function updateURLParams($url){
            if(preg_match('/^http\:\/\/([^\/]+)\/?(.*)/',$url,$rets)){
                $rets[1] && $this->urlParams["host"]=$rets[1];
                $rets[2] && $this->urlParams["path"]=$rets[2];
            }
        }
        
        /**
         * 将header数组转化为String
         * @param {Array} $headerArr
         * @return {String} $ret
         */
        private function getHeaderStr($headerArr=array()){
            $ret="POST /".$this->urlParams["path"]." HTTP/1.1\r\n";
            
            foreach($headerArr as $k=>$v){
                $ret.="$k: $v\r\n";
            }
            
            return $ret;
        }
    }
?>