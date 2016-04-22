<?php
/**
 * Mysql类
 *
 * @author aiden
 * @copyright http://www.myzmh.com
 * @version 1.0
 */

class Mysql
{  	 
	/**
	 * 数据库连接字符串
	 * @var 
	 * @access private
	 */
    private $conn = '';        
	  
    /**
     * 执行SQL次数
     * @var init
     * @access private
     */
	private  $queryCount = 0;    
	  
	/**
	 * 数据库名称
	 * @var string
	 * @access private
	 */
	private  $_dbName = '';       
	
	/**
	 * 存储SQL语句
	 * @var array
	 * @access private
	 */
	private $sql = array();
	  
	/**
	 * 初始化
	 * @access public
	 * @param array $config 配置文件     
	 * @return void 
	 */
    public function __construct($config)
    { 
	    if (!is_array($config))  
	    {
	    	$config = array();
	    }

	     
		$this->open($config);
	}


	/**
	 * 连接数据库
	 *
	 * @access private
	 * @param array $config 配置文件
	 * @return void
	 */
	 private function open($config)
	 {
	  	  $fun  = '';
	  	  $this->db_name = $config['db_name'];
	      if (!isset($config['pcconnect']))
	          $fun = 'mysql_connect';
	      else 
	          $fun = 'mysql_pconnect';    

	      $this->conn = $fun($config['db_host'],$config['db_user'],$config['db_pass'], true);
       
		  if ($this->conn == false) 
		  {		     
			  $this->showError('数据库连接失败'.mysql_error());
		  }
		 
     	  if ( !empty($config['db_charset']) )
     	  {
 			  $this->query("SET NAMES '".$config['db_charset']."'", $this->conn);  //编码设置	  
     	  }

     	  $this->_selectDb();
	 }
  
	/**
	 * 选择数据库
	 *
	 * @access private
	 * @return void
	 */
	private function _selectDb()
	{
	    if (!mysql_select_db($this->db_name,$this->conn))  //选择数据库
		{
			 $this->showError('选择数据库失败，请检查数据库 '.$this->db_name.' 是否存在');
		}
	}
	  
    /**
     * 执行sql语句
     * @access public
     * @param string $sql SQL语句  
     * @return Object
     */
	public function query($sql)
	{		
		$startTime = microtime(TRUE);
		
		$this->queryCount++;
		if(!($query = @mysql_query($sql , $this->conn)))
		{
			var_dump( $query);
			$this->showError('MySQL Query Error</br>SQL:<font color=red>'.$sql.'</font></br>Error:'.mysql_error());
			return false;
		}

	    $endTime = microtime(TRUE);
	    $showTime = number_format(($endTime - $startTime), 5).'S ';  //执行时间
	    $sql = "( Time:$showTime)".$sql;		
		$this->sql[] = $sql;
		return $query;
	}
	
	
	/**
	 * 获取一条记录
	 * @param unknown $table
	 * @param string $where
	 * @param string $field
	 * @param unknown $order
	 * @return Ambigous <>|NULL
	 */
	public function getOne($table, $where='', $field='*', $order='') {
		$sql = "select $field from $table";
		$sql .= empty($where) ? '' : " where $where";
		$sql .= empty($order) ? '' : " order by $order";
		$sql .= ' limit 0,1';
		$list = $this->getArray($sql);
		if (!empty($list)) {return $list[0];}
		return $list; 
	}
	
	public function getList($table, $where='', $field='*', $order='') {
		$sql = "select $field from $table";
		$sql .= empty($where) ? '' : " where $where";
		$sql .= empty($order) ? '' : " order by $order";
		return $this->getArray($sql);
	}
	
	
	/**
	 * 查询多条记录
	 * @access public
	 * @param string $sql SQL语句
	 * @return array
	 */
	public function getArray($sql)
	{
		$array = '';
		$result = $this->query($sql);
		while ($row = $this->fetch_array($result)) 
		{
			 $array[] = $row;
		}
        $this->free_result($result);
        if (!is_array($array)) 
        {
        	return null;
        } 
        
        return $array;        
	}
	
	/**
	 * 查询一条记录
	 * @access public
	 * @param string $sql SQL语句
	 * @return array
	 */
	public function getRow($sql)
	{
		$result = $this->query($sql);
		$row = mysql_fetch_array($result);
		
		$this->free_result($result);
	    
		return $row;
	}

	/**
	 * 插入一条记录
	 * @access public
	 * @param  string $table 表名称
	 * @param array $rows 插入的数据
	 * @return bool 
	 */
	public function insert($table, $array)
	{
		 return $this->query("INSERT INTO `$table`(`".implode('`,`', array_keys($array))."`) VALUES('".implode("','", $array)."')");
	}
	
	/**
	 * 插入多条记录
	 * @access public
	 * @param  string $table 表名称
	 * @param array $rows 插入的数据
	 * @return bool 
	 */
	public function insertMore($table, $rows)
	{
		if (empty($table) || !is_array($rows))
		{
			return false;
		}
		$data = reset($rows); 
		if (empty($data))
		{
			return  false;
		}
		
		$sql = "INSERT INTO `$table`(`".implode('`,`', array_keys($data))."`) VALUES";
		foreach ($rows as $value)
		{
			$sql .= "('".implode("','", $value)."'),";
		}
		
		$sql = rtrim($sql, ',');
		return $this->query($sql);
	}
	
	/**
	 * 更新记录
	 * @access public
	 * @param string $tablename 表名称
	 * @param array $array 要更新的数据
	 * @param string $where 条件   
	 * @return bool 
	 */
	public function update($tablename, $array, $where='')
	{
		if (!is_array($array)){
			return false;
		}
		$sql = '';
		foreach($array as $k=>$v)
		{
			$sql .= ",`$k`='$v'";
		}
		if (!empty($where)){
		   $where = 'WHERE '.$where;	
		}
		$sql = substr($sql, 1);
		$sql = "UPDATE `$tablename` SET $sql $where";
	    $sql = trim($sql);

		return $this->query($sql);
	}
	
	/**
	 * 删除数据
	 *
	 * @param string $table 表名称
	 * @param string $where 条件
	 * @return void
	 */
	public function delete($table, $where='')
	{
		 if (!empty($where)){
		 	$where = 'WHERE '.$where;
		 }
		 $sql = "DELETE FROM `$table` $where";
		 $sql = trim($sql);
		 $this->query($sql);
	}

	/**
	 * 获取数据表结构
	 *
	 * @param tbl_name  表名称
	 */
	public function getTable($tbl_name)
	{
		return $this->getArray("DESCRIBE {$tbl_name}");
	}	
	
	/**
	 * 从结果集中得到一条记录
	 * @access public
	 * @param $query MYSQL资源
	 * @param $result_type 获得类型
	 * @return void 
	 */
    public function fetch_array($query, $result_type = MYSQL_ASSOC)
	{
		return mysql_fetch_array($query, $result_type);
	}

	/**
	 * 得到上一次受影响的记录数
	 * @access public
	 * @return int 
	 */
	public function affected_rows()
	{
		return mysql_affected_rows($this->conn);
	}
 	
	/**
	 * 返回执行SQL的次数
	 * @access public
	 * @return int 
	 */
	public function getQuery()
	{
		return $this->queryCount;
	}   
	
	/**
	 * 得到执行的SQL语句
	 * @access public
	 * @return array
	 */
	public function getSql()
	{
	   return $this->sql;	
	}
	
	/**
	 * 得到总记录数
	 * @access public
	 * @param  $query MYSQL资源  
	 * @return int 
	 */
	public function num_rows($query)
	{
		return mysql_num_rows($query);
	}

	/**
	 * 结果集中字段数
	 * @access public
	 * @param unknown_type $query
	 * @return int
	 */
	public function num_fields($query)
	{
		return mysql_num_fields($query);
	}

	/**
	 * 对特殊字符进行过滤
	 *
	 * @access public
	 * @param value 值
	 * @return string
	 */
	public function __val_escape($value) {
		if (!is_string($value))
		{
		  return $value;
		}
		    
		if( @get_magic_quotes_gpc())
		{
		   $value = stripslashes($value); 
		}
		return mysql_real_escape_string($value, $this->conn);
	}
	
	/**
	 * 返回上一次执行增加的ID
	 * @access public
	 * @return int 
	 */
	public function insert_id()
	{
		return mysql_insert_id($this->conn);
	}
    
	/**
	 * 关闭结果集
	 * @access public
	 * @param  $query MYSQL资源
	 * @return void
	 */
	public function free_result(&$query)
	{
		return mysql_free_result($query);
	}

	/**
	 * 
	 * 关闭数据库
	 * @access public
	 * @return void
	 */
	public function close()
	{
		if ($this->conn)
		{
			@mysql_close($this->conn);
		}	   
	}

	public function showError($msg) 
	{
// 		echo $msg;
	    OutPut::error('-6');
		exit;
	}
	    
}

?>