<?php

require_once(dirname(__FILE__) . '/DbDriver.class.php');

class MysqlDbDriver extends DbDriver
{
	function _connect_string($dsn)
	{
	  extract($dsn);
	  $password = ($password)? '-p' . $password : '';
	  return "mysql -h$host -u$user $password";
	}
	
	function _nondb_exec($dsn, $cmd)
	{
	  $dsn['database'] = '';
	  return $this->_exec($dsn, $cmd);
	}
	
	function _exec($dsn, $cmd)
	{
	  extract($dsn);
	  $shell_cmd = $this->_connect_string($dsn) . ' -e"' . $cmd . '" -N -B ' . $database . ' 2>&1';
	  exec($shell_cmd, $out, $ret);
	  $outstr = trim(implode("\n", $out));

	  if($ret)
	    throw new Exception("Shell command '$shell_cmd' executing error \n'$outstr'");
	
	  if(preg_match('~ERROR\s+\d+\s+\(\d+\)~', $outstr))
	    throw new Exception("MySQL command '$cmd' with error \n'$outstr'");
	
	  return $outstr;
	}
	
	function _load($dsn, $file)
	{
	  extract($dsn);
	  $cmd = $this->_connect_string($dsn) . " $database < $file 2>&1";
	
	  echo "Starting to load '$file' file to '$database' DB...";
	
	  exec($cmd, $out, $ret);
	  $outstr = trim(implode("\n", $out));
	
	  if($ret)
	    throw new Exception("Shell command '$cmd' executing error \n'$outstr'");
	
	  if(preg_match('~ERROR\s+\d+\s+\(\d+\)~', $outstr))
	    throw new Exception("MySQL specific error \n'$outstr'");
	
	  $this->_log("done\n");
	}
	
	function _db_exists($dsn)
	{
	  extract($dsn);
	  $res = $this->_nondb_exec($dsn, "SHOW DATABASES");
	  return strpos($res, $database) !== false;
	}
	
	function _table_exists($dsn, $table)
	{
	  extract($dsn);
	  $res = $this->_exec($dsn, "SHOW TABLES");
	  return strpos($res, $table) !== false;
	}
	
	function _get_tables($dsn)
	{
	  extract($dsn);
	  $password = ($password)? '-p' . $password : '';
	  $cmd = "mysql -NB -u$user $password -h$host -e\"SHOW TABLES\" $database";
	  $tables = array_filter(explode("\n", `$cmd`));
	  return $tables;
	}
	
	function _create_tmp_db($dsn)
	{
	  $dsn['database'] = $dsn['database'] . '_migration';
	  $this->_log("Creating tmp db '{$dsn['database']}'...");
	  $this->_nondb_exec($dsn, "DROP DATABASE IF EXISTS {$dsn['database']}");
	  $this->_nondb_exec($dsn, "CREATE DATABASE {$dsn['database']}");
	  $this->_log("done\n");
	  return $dsn['database'];
	}
	
	function _db_drop($dsn)
	{
	  extract($dsn);
	  $this->_log("Dropping database '$database'\n");
	  
	  $this->_exec($dsn, "DROP DATABASE $database");
	}
	
	function _dump_schema($dsn, $file)
	{
	  extract($dsn);
	  $password = ($password)? '-p' . $password : '';
	  $cmd = "mysqldump -u$user $password -h$host " .
	         "-d --default-character-set=$charset " .
	         "--quote-names --allow-keywords --add-drop-table " .
	         "--set-charset --result-file=$file " .
	         "$database ";
	
	  $this->_log("Starting to dump schema to '$file' file...");
	
	  system($cmd, $ret);

    // dumping shema_info table data to schema_info
     $cmd = "mysqldump -u$user $password -h$host " .
	         "-t --default-character-set=$charset " .
	         "--add-drop-table --create-options --quick " .
	         "--allow-keywords --max_allowed_packet=16M --quote-names " .
	         "--complete-insert --set-charset " .
	         "$database schema_info >> $file";

     system($cmd, $ret1);

	  if(!$ret and !$ret1)
	    $this->_log("done! (" . filesize($file) . " bytes)\n");
	  else
	    $this->_log("error!\n");
	}
	
	function _dump_data($dsn, $file)
	{
	  extract($dsn);
	  $password = ($password)? '-p' . $password : '';
	  $cmd = "mysqldump -u$user $password -h$host " .
	         "-t --default-character-set=$charset " .
	         "--add-drop-table --create-options --quick " .
	         "--allow-keywords --max_allowed_packet=16M --quote-names " .
	         "--complete-insert --set-charset --ignore-table=$database.schema_info --result-file=$file " .
	         "$database ";
	
	
	  $this->_log("Starting to dump to '$file' file...");
	
	  system($cmd, $ret);
	
	  if(!$ret)
	    $this->_log("done! (" . filesize($file) . " bytes)\n");
	  else
	   $this->_log("error!\n");
	}
	
	function _dump_load($dsn, $file)
	{
	  extract($dsn);
	  $password = ($password)? '-p' . $password : '';
	  $cmd = "mysql -u$user $password -h$host --default-character-set=$charset $database < $file";
	
	  $this->_log("Starting to load '$file' file to '$database' DB...");
	
	  system($cmd, $ret);
	
	  if(!$ret)
	    $this->_log("done! (" . filesize($file) . " bytes)\n");
	  else
	   $this->_log("error!\n");
	}
	
	function _copy_schema($dsn_src, $dsn_dst)
	{
	  extract($dsn_src, EXTR_PREFIX_ALL, 'src');
	  extract($dsn_dst, EXTR_PREFIX_ALL, 'dst');
	
	  $tables = $this->_get_tables($dsn_src);
	
	  $src_password = ($src_password)? '-p' . $src_password : '';
	  $dst_password = ($dst_password)? '-p' . $dst_password : '';
	
	  $this->_log("Starting to clone schema from '$src_database' DB to '$dst_database' DB...\n");
	
	  foreach($tables as $table)
	  {
	    $cmd = "mysql -NB -u$src_user $src_password -h$src_host -e\"SHOW CREATE TABLE $table\" $src_database";
	    list(,$create_schema) = explode("\t", `$cmd`, 2);
	
	    $create_schema = str_replace('\n', '', escapeshellarg(trim($create_schema)));
	    $cmd = "mysql -u$dst_user $dst_password -h$dst_host -e$create_schema $dst_database";
	    system($cmd, $ret);
	    if(!$ret)
	      $this->_log("'$table' copied\n");
	    else
	     $this->_log("error while copying '$table'\n");
	  }
	  $this->_log("done\n");
	}
	
	function _copy_schema_and_use_memory_engine($dsn_src, $dsn_dst)
	{
	  extract($dsn_src, EXTR_PREFIX_ALL, 'src');
	  extract($dsn_dst, EXTR_PREFIX_ALL, 'dst');
	
	  $tables = $this->_get_tables($dsn_src);
	
	  $src_password = ($src_password)? '-p' . $src_password : '';
	  $dst_password = ($dst_password)? '-p' . $dst_password : '';
	
	  $this->_log("Starting to clone schema from '$src_database' DB to '$dst_database' DB...\n");
	
	  foreach($tables as $table)
	  {
	    $cmd = "mysql -NB -u$src_user $src_password -h$src_host -e\"SHOW CREATE TABLE $table\" $src_database";
	    list(,$create_schema) = explode("\t", `$cmd`, 2);
	
	    $create_schema = str_replace('\n', '', escapeshellarg(trim($create_schema)));
	    $create_schema = preg_replace('/(.*)ENGINE=([^\s]*)(.*)/', '${1}ENGINE=MEMORY${3}', $create_schema);
	
	    $create_schema = str_replace(array(' longtext', ' blob', ' text'), ' varchar(255)', $create_schema);
	
	    $cmd = "mysql -u$dst_user $dst_password -h$dst_host -e$create_schema $dst_database";
	    system($cmd, $ret);
	    if(!$ret)
	      $this->_log("'$table' copied\n");
	    else
	      $this->_log("error while copying '$table'\n");
	  }
	  $this->_log("done\n");
	}
	
	
	function _db_cleanup($dsn)
	{
	  extract($dsn);
	
	  $tables = $this->_get_tables($dsn);
	  $this->_drop_tables($dsn, $tables);
	
	  $this->_log("Starting clean up of '$database' DB...\n");
	
	  $this->_log("done\n");
	}
	
	function _drop_tables($dsn, $tables)
	{
	  extract($dsn);
	
	  $password = ($password)? '-p' . $password : '';
	  foreach($tables as $table)
	  {
	    $cmd = "mysql -u$user $password -h$host -e\"DROP TABLE $table\" $database";
	    system($cmd, $ret);
	    if(!$ret)
	      $this->_log("'$table' removed\n");
	    else
	      $this->_log("error while removing '$table'\n");
	  }
	}
	
	function _truncate_tables($dsn, $tables)
	{
	  extract($dsn);
	
	  $password = ($password)? '-p' . $password : '';
	  foreach($tables as $table)
	  {
	    $cmd = "mysql -u$user $password -h$host -e\"TRUNCATE TABLE $table\" $database";
	    system($cmd, $ret);
	    if(!$ret)
	      echo "'$table' truncated\n";
	    else
	      echo "error while truncating '$table'\n";
	  }
	}
	
	function _get_migration_files_since($migrations_dir, $base_version)
	{
	  $files = array();
	  foreach(glob($migrations_dir . '/*') as $file)
	  {
	    list($version, ) = explode('_', basename($file));
	    $version = intval($version);
	    if($version > $base_version)
	      $files[$version] = $file;
	  }
	  ksort($files);
	  return $files;
	}
	
	function _get_last_migration_file($migrations_dir)
	{
	  $files = glob($migrations_dir . '/*');
	  krsort($files);
	  return reset($files);
	}
	
	function _migrate($dsn, $migrations_dir, $since = null)
	{
	  extract($dsn);
	
	  if(!$this->_db_exists($dsn))
	    return;
	
	  if(!$this->_table_exists($dsn, 'schema_info'))
	    $this->_exec($dsn, 'CREATE TABLE schema_info ("version" integer default 1);');
	
	  if(!$this->_exec($dsn, 'SELECT COUNT(*) as c FROM schema_info'))
	    $this->_exec($dsn, 'INSERT INTO schema_info VALUES (' . (int) $since . ');');

	  if(is_null($since))
	    $since = (int) $this->_exec($dsn, 'SELECT version FROM schema_info');
	
	  $this->_log("Searching for dumps since version '$since' in '$migrations_dir'\n");
	  foreach($this->_get_migration_files_since($migrations_dir, $since) as $version => $file)
	  {
	    $this->_load($dsn, $file);
	    $this->_exec($dsn, "UPDATE schema_info SET version=$version;");
	  }
	}

	function _get_schema_version($dsn)
	{
	  extract($dsn);
	
	  if(!$this->_table_exists($dsn, 'schema_info'))
	    return null;
	
	  return (int)$this->_exec($dsn, 'SELECT version FROM schema_info');
	}

	function _set_schema_version($dsn, $since = 1)
	{
	  extract($dsn);
	
	  $this->_log('Setting schema version ' . (int) $since . PHP_EOL);
      if(!$this->_table_exists($dsn, 'schema_info'))
        $this->_exec($dsn, 'CREATE TABLE schema_info ("version" integer default 1);');
  
      if((int)$this->_exec($dsn, 'SELECT COUNT(*) as c FROM schema_info'))
        $this->_exec($dsn, 'UPDATE schema_info SET version = ' . (int) $since . ';');
      else
        $this->_exec($dsn, 'INSERT INTO schema_info VALUES (' . (int) $since . ');');

      return (int)$this->_exec($dsn, 'SELECT version FROM schema_info');
	}

	function _copy_tables_contents($dsn_src, $dsn_dst, $tables)
	{
	  extract($dsn_src, EXTR_PREFIX_ALL, 'src');
	  extract($dsn_dst, EXTR_PREFIX_ALL, 'dst');
	
	  $this->_log("\nStarting to copy tables contents from '$src_database' DB to '$dst_database' DB...\n");
	
	  $conn = mysql_connect($src_host, $src_user, $src_password);
	
	  mysql_query("set character_set_client='utf8'", $conn);
	  mysql_query("set character_set_results='utf8'", $conn);
	  mysql_query("set collation_connection='utf8_general_ci'", $conn);
	
	  mysql_select_db($src_database, $conn);
	
	  $dump = array();
	  foreach($tables as $table)
	  {
	    $sql = "SELECT * FROM " . $table . ";";
	    $result = mysql_query($sql, $conn);
	    while($record = mysql_fetch_assoc($result))
	    {
	      $dump[$table][] = $record;
	    }
	  }
	
	  mysql_close($conn);
	
	  $conn = mysql_connect($dst_host, $dst_user, $dst_password);
	
	  mysql_query("set character_set_client='utf8'", $conn);
	  mysql_query("set character_set_results='utf8'", $conn);
	  mysql_query("set collation_connection='utf8_general_ci'", $conn);
	
	  mysql_select_db($dst_database, $conn);
	
	  foreach($dump as $table => $records)
	  {
	    $sql = "INSERT INTO " . $table . " VALUES (";
	    foreach($records as $record)
	    {
	      foreach($record as $field)
	        $sql .= "'" . substr($field, 0, 255) . "',";
	
	      $sql = preg_replace('/,$/', '', $sql);
	      $sql .= "),(";
	    }
	    $sql = preg_replace('/,\($/', ';', $sql);
	
	    if(mysql_query($sql, $conn))
	      $this->_log("'" . $table . "' copied content\n");
	  }
	
	  mysql_close($conn);
	
	  $this->_log("done\n");
	}

  function _diff($dsn, $schema, $data, $migrations_dir, $since = null)
  {
    require_once (dirname(__FILE__) . '/MysqlConnection.class.php');
    require_once (dirname(__FILE__) . '/Mysql.functions.php');
 
    extract($dsn);

	if(preg_match('~INSERT\s+INTO\s+.*schema_info\D+(\d+)~i', file_get_contents($data), $m))
	  $since = $m[1];
	else
	  $since = -1;
	
	//collecting all not applied migrations
	$migrations = array();
	foreach(glob($migrations_dir . '/*.sql') as $migration)
	{
	  list($version,) = explode('_', basename($migration));
	  if($since < intval($version))
	    $migrations[] = $migration;
	}
	asort($migrations);
	
	$working_db = array(
	  'hostname' => $host,
	  'username' => $user,
	  'password' => $password,
	  'database' => $database
	);
	
	$conn = new MysqlConnection($host, $user, $password);
	$conn->open();
	$tmp_db = $conn->createTemporaryDatabase();
	
	$repos_db = $working_db;
	$repos_db['database'] = $tmp_db;
	
	$conn->importSql($tmp_db, $schema);
	
	foreach($migrations as $migration)
	  $conn->importSql($tmp_db, $migration);
	
	$diff = generateScript($repos_db, $working_db);
	
	$conn->dropDatabase($tmp_db);
	$conn->close();
	
	return $diff;
  }
  
  function _create_migration($dsn, $name, $schema, $data, $migrations_dir, $since = 0)
  {
	extract($dsn);

	$this->_log("===== Migrating production DB to apply all migrations =====\n");
	$this->_migrate($dsn, $migrations_dir, null);
	
	$diff = $this->_diff($dsn, $schema, $data, $migrations_dir);
	
	if($diff)
	{
	  $last = $this->_get_last_migration_file($migrations_dir);
	  if(is_file($last) and file_get_contents($last) == $diff)
	  {
	    $this->_log("The last migration file '$last' is identical to the new migration, skipped\n");
	    return;
	  }
	
	  $stamp = time();
	  $file = "$migrations_dir/{$stamp}_{$name}.sql";
	
	  $this->_log("Writing new migration to file '$file'...");
	  file_put_contents($file, $diff);
	  $this->_log("done! (" . strlen($diff). " bytes)\n");
	
	  if(!$this->_test_migration($dsn, $schema, $data, $migrations_dir))
	    $this->_log("\nWARNING: migration has errors, please correct them before committing! Try dry-running it with mysql_migrate.php --dry-run\n");
	
	  $this->_log("Updating version info...");
	  $this->_exec($dsn, "UPDATE schema_info SET version = $stamp;");
	  $this->_log("done!\n");
	}
	else
	  $this->_log("There haven't been any changes according to the latest dump\n");
  }
}