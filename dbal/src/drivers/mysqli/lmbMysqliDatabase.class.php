<?php

lmb_require('limb/dbal/src/drivers/lmbDatabase.interface.php');

class lmbMysqliDatabase extends lmbMysqliDbInfo implements lmbDatabase 
{
	var $dsn;
	
	function __construct($connection, $name, $isExisting = false)
	{
		parent::__construct($connection, $name, $isExisting);
		$this->dsn = new lmbDbDSN($this->connection->getDsnString());
	}
		
	function cleanup()
	{
		foreach($this->getTables() as $table)
			$this->connection->execute('DROP TABLE `'.$table->getName().'`');
			
	  $this->tables = array();
	}
	
	protected function _getMysqlCmdOptions()
	{	
		$password = ($this->dsn->password) ? '-p' . $this->dsn->password : '';	
		return "-u{$this->dsn->user} {$password} -h{$this->dsn->host}"
         ." --default-character-set={$this->dsn->extra['charset']}";
	}
		
	function dumpSchema($file, $tables = array())
	{
    $cmd = "mysqldump ".$this->_getMysqlCmdOptions()
      ." --allow-keywords --add-drop-table  --set-charset  --quote-names"         
      ." --result-file={$file} -d"
      ." ".$this->dsn->database." ".implode('', $tables);
  
    system($cmd, $ret);

    if(!$ret)
      return filesize($file);
    else
      throw new lmbException('Error on schema dump creation!',
        array('cmd' => $cmd, 'file' => $file, 'tables' => $tables)
      );
	}
	
  function dumpData($file, $tables = array())
  {
    $cmd = "mysqldump ".$this->_getMysqlCmdOptions()
           ." --allow-keywords --add-drop-table  --set-charset  --quote-names"
           ." --result-file={$file} -t"
           ." --create-options --quick --max_allowed_packet=16M"
           ." --complete-insert"
           ." {$this->dsn->database} " . implode('', $tables);
  
    system($cmd, $ret);
  
    if(!$ret)
      return filesize($file);
    else
      throw new lmbException('Error on data dump creation!',
        array('cmd' => $cmd, 'file' => $file, 'tables' => $tables)
      );
  }
	
	function load($file)
	{
		$dsn = new lmbDbDSN($this->connection->getDsnString());
		$cmd = "mysql ".$this->_getMysqlCmdOptions()." {$this->dsn->database} < {$file} 2>&1";
		
	  exec($cmd, $out, $ret);
	  $outstr = trim(implode("\n", $out));
	
	  if($ret)
	    throw new Exception("Shell command '$cmd' executing error \n'$outstr'");
	
	  if(preg_match('~ERROR\s+\d+\s+\(\d+\)~', $outstr))
	    throw new Exception("MySQL specific error \n'$outstr'");

	  $this->loadTables($force = true);
	}
}

?>