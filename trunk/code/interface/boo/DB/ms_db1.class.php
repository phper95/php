<?php
/*
 * DB类，连接远程服务器sqlserver数据库
 * 用户名密码已经设定
 */
class MssqlDB{
// 	private $_server = "58.61.160.95";
// 	private $_server = "localhost";
	private $_server = "121.199.9.233";
	private $_database = 'API';
	private $_user = 'sa';
	private $_password = 'admin!@#sa';
	private $_connect = NULL;
	protected $queryID = NULL; 
	protected $numRows = 0;
	protected $queryStr = '';
	protected $lastInsID = NULL;
	
	/**
     * 架构函数 读取数据库配置信息
     * @access public
     */
    public function __construct(){
        if ( !function_exists('mssql_connect') ) {
        	throw new Exception('扩展不存在:mssql');
        }
        $this->_connect = mssql_connect($this->_server,$this->_user,$this->_password);
        mssql_select_db($this->_database,$this->_connect);
    }
    
    public function setDb($db){
    	$db = empty($db)?$db:$this->_database;
    	mssql_select_db($db,$this->_connect);
    }
    
    public function resetDb(){
    	mssql_select_db($this->_database,$this->_connect);
    }
    
    public function query($str){
        if ( !$this->_connect || empty($str) ) return false;
        //释放前次的查询结果
        if ( $this->queryID ) $this->free();
        $this->queryStr = $str;
        $this->queryID = mssql_query($str,$this->_connect);
        if ( false === $this->queryID ) {
            $this->error();
            return false;
        } else {
            $this->numRows = mssql_num_rows($this->queryID);
            return $this->getAll();
        }
    }
    
    
    /**
     * 执行sql语句，返回执行结果 
     * @param string $str
     */
    public function exec($str){
    	if ( !$this->_connect || empty($str) ) return false;
        $this->queryStr = $str;
        //释放前次的查询结果
        if ( $this->queryID ) $this->free();
        $result	=	mssql_query($str, $this->_connect);
        if ( false === $result ) {
            $this->error();
            return false;
        } else {
            $this->numRows = mssql_rows_affected($this->_connect);
            $this->lastInsID = $this->mssql_insert_id();
            return $this->numRows;
        }
    }
    
	/**
     * 关闭数据库
     * @access public
     */
    public function close() {
        if ($this->_connect){
            mssql_close($this->_connect);
        }
        $this->_connect = NULL;
    }
    
	/**
     * 释放查询结果
     * @access public
     */
    public function free() {
        mssql_free_result($this->queryID);
        $this->queryID = null;
    }
    
	/**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
        $this->error = mssql_get_last_message();
        if('' != $this->queryStr){
            $this->error .= "\n [ SQL语句 ] : ".$this->queryStr;
        }
        return $this->error;
    }
    
	/**
     * 获得所有的查询数据
     * @access private
     * @return array
     */
    private function getAll() {
        //返回数据集
        $result = array();
        if($this->numRows >0) {
            while($row = mssql_fetch_assoc($this->queryID))
                $result[]   =   $row;
        }
        return $result;
    }
    
	/**
     * 用于获取最后插入的ID
     * @access public
     * @return integer
     */
    public function mssql_insert_id() {
        $query  =   "SELECT @@IDENTITY as last_insert_id";
        $result =   mssql_query($query);
        list($last_insert_id)   =   mssql_fetch_row($result);
        mssql_free_result($result);
        return $last_insert_id;
    }
    
    
}