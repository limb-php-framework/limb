<?php
require_once (dirname(__FILE__) . '/MysqlQuery.class.php');

class MysqlConnection {

  var $_hostname = NULL;
  var $_username = NULL;
  var $_password = NULL;
  var $_database = NULL;
  var $_con = NULL;

  var $_laststatement = "";
  var $_lasterrno = 0;
  var $_lasterror = "";
  var $_lasterrorpos = "";

  function __construct($hostname = NULL, $username = NULL, $password = NULL) {
    $this->_hostname = isset($hostname) ? $hostname : NULL;
    $this->_username = isset($username) ? $username : NULL;
    $this->_password = isset($password) ? $password : NULL;
    $this->_con = NULL;
  }

  function connectionEqual(&$connection) {
    return $this->_con = $connection->_con;
  }

  function close() {
    if ( isset($this->_con) ) {
      mysql_close($this->_con);
    }
  }

  function open($persistent = FALSE) {
    static $connectionfunctions = array(1 => "mysql_pconnect", 0 => "mysql_connect");

    $this->_con = @$connectionfunctions[$persistent ? 1 : 0]($this->_hostname, $this->_username, $this->_password, true);
    
    return isset($this->_con) && is_resource($this->_con);
  }

  function query($stat, $file=NULL, $line=NULL) {
    $result=new MysqlQuery($stat, $this, isset($file)?$file:NULL, isset($line)?$line:NULL);
    if ( !isset($result) ) {
      return NULL;
    } else if ( $result->_res ) {
      return $result;
    } else {
      $result->destroy();
      return NULL;
    }
  }

  function splitSqlFile($sql) {
    $result = array();
    $sql = trim($sql);
    $sql_len = strlen($sql);
    $char         = '';
    $string_start = '';
    $is_string = FALSE;
    $time0        = time();
    $server_version = $this->serverVersionInteger();

    for ( $i = 0; $i < $sql_len; ++$i) {
      if ( $sql[$i] == ';' ) { // End Of Statement ...
        $result[] = substr($sql, 0, $i);
        $sql = ltrim(substr($sql, min($i + 1, $sql_len)));
        $sql_len = strlen($sql);
        if ( $sql_len == 0 ) {
          return $result;
        }
        $i = -1;
      } else if ( $sql[$i] == '#' || ($sql[$i] == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--') ) {
        // starting position of the comment depends on the comment type
        $start_of_comment = ($sql[$i] == '#' ? $i : $i-2);
        // search for new line i.e. \n or \r (Mac style)
        $end_of_comment = (strpos($sql, "\012", $i+2) !== FALSE ? strpos($sql, "\012", $i+2) : strpos($sql, "\015", $i+2) ) + 1;
        if (!$end_of_comment) {
          // no eol found after '#', add the parsed part to the returned
          // array if required and exit
          if ($start_of_comment > 0) {
            $result[]    = trim(substr($sql, 0, $start_of_comment));
          }
          return $result;
        } else {
          $sql          = substr($sql, 0, $start_of_comment) . ltrim(substr($sql, $end_of_comment));
          $sql_len      = strlen($sql);
          $i--;
        } // end if...else
      } else if ( $server_version < 32270 && ($sql[$i] == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*') ) {
        $sql[$i] = ' ';
      } else if ( in_array($sql[$i], array("\"", "'", "`")) ) { // Skipping String ...
        $string_start = $sql[$i];
        while (TRUE) {
          if ( !($i = strpos($sql, $string_start, $i+1)) ) {
            $result[] = $sql;
            return $result;
          } else if ( $string_start == '`' || $sql[$i-1] != '\\') {
            $string_start = '';
            $is_string = FALSE;
            break;
          } else {
            $j = 2;
            $escaped_backslash = FALSE;
            while ($i-$j > 0 && $sql[$i-$j] == '\\') {
              $escaped_backslash = !$escaped_backslash;
              $j++;
            }
            if ($escaped_backslash) {
              $string_start  = '';
              $is_string     = FALSE;
              break;
            } else {
              $i++;
            }
          }
        }
      }

      // Sending Keep-Alive Header ...
      $time1 = time();
      if ($time1 >= $time0 + 30) {
        $time0 = $time1;
        header('X-mysqldiff-keep-alive: Pong');
      }
    }

    // add any rest to the returned array
    if ( !empty($sql) && preg_match('@[^[:space:]]+@', $sql) ) {
      $result[] = $sql;
    }

    return $result;
  }


  function importSql($database, $file) {
    $password = $this->_password ? ' -p' . $this->_password :'';
    `mysql -h{$this->_hostname} -u{$this->_username}{$password} {$database} < $file`;
  }

  function listDatabases($numericindex = FALSE) {
    $result = array();
    if ( $res = mysql_list_dbs($this->_con) ) {
      while ( $row = mysql_fetch_object($res) ) {
        if ( $numericindex ) {
          $result[] = $row->Database;
        } else $result[$row->Database] = $row->Database;
      }
      mysql_free_result($res);
    }
    return $result;
  }

  function selectDatabase($name) {
    if ( !mysql_select_db($this->_database = $name, $this->_con) ) {
      $this->_database = NULL;
    }
    return isset($this->_database);
  }

  function canCreateTemporaryDatabase() {

  }

  function createTemporaryDatabase($name = NULL) {
    static $idx = 0;
    $tempname = isset($name) ? $name : "temp_mysqldiff_".time()."_".$idx;
    $idx++;
    mysql_query("CREATE DATABASE $tempname", $this->_con);
    echo mysql_error($this->_con);
    if ( mysql_errno($this->_con) == 0 ) {
      return $tempname;
    } else return "";
  }

  function dropDatabase($name) {
    mysql_query("DROP DATABASE $name", $this->_con);
    return mysql_errno($this->_con) == 0;
  }

  function fetchTablelist($db = NULL, $numericindex = FALSE, $extendeddisplay = FALSE) {
    $result = array();

    if ( $extendeddisplay ) {
      if ( $res = $this->query($stat = "SHOW TABLE STATUS FROM `$db`") ) {
        while ( $row = $res->next() ) {
          $result[$row->Name] = $row->Name." ($row->Type)";
        }
        $res->destroy();
      }
    } else {
      if ( !isset($db) && isset($this->_database) ) $db = $this->_database;
      if ( isset($db) && $res = mysql_list_tables($db, $this->_con) ) {
        while ( $row = mysql_fetch_row($res) ) {
          if ( $numericindex ) {
            $result[] = $row[0];
          } else $result[$row[0]] = $row[0];
        }
        mysql_free_result($res);
      }
    }
    return $result;
  }

  function fetchFields($table, $db) {
    $result=NULL;
    if ( $res = $this->query("SHOW FULL FIELDS FROM `$table` FROM `$db`") ) {
      while ( $row = $res->next() ) {
        $result[$row->Field] = array(
          "database" => $db,
          "name" => $row->Field,
          "type" => $row->Type,
          "null" => ( isset($row->Null) && $row->Null == "YES" ? 1 : 0 ),
          "default" => ( isset($row->Default) ? $row->Default : NULL ),
          "extra"=> ( isset($row->Extra) ? $row->Extra : NULL ),
        );
        if ( isset($row->Comment) ) $result[$row->Field]["comment"] = $row->Comment;
        if ( isset($row->Collation) && $row->Collation != "NULL" ) $result[$row->Field]["collate"] = $row->Collation;
      }
      $res->destroy();
    }
    return isset($result) ? $result : NULL;
  }

  function fetchIndexes($table, $db = NULL) {
    $result=NULL;
    if ( !isset($db) && isset($this->_database) ) $db = $this->database;

    if ( $res = $this->query("SHOW INDEX FROM `$table` FROM `$db`") ) {
      while ( $row = $res->next() ) {
        $result[$row->Key_name]["database"] = $db;
        $result[$row->Key_name]["name"] = $row->Key_name;
        $result[$row->Key_name]["unique"] = $row->Non_unique == 0 ? 1 : 0;
        $result[$row->Key_name]["fields"][$row->Column_name]["name"]=$row->Column_name;
        $result[$row->Key_name]["type"] = isset($row->Index_type) ? $row->Index_type : "BTREE";
        if ( isset($row->Sub_part) && $row->Sub_part != "" ) $result[$row->Key_name]["fields"][$row->Column_name]["sub"]=$row->Sub_part;
      }
      $res->destroy();
    }
    return isset($result) ? $result : NULL;
  }

  function fetchTables($db = NULL) {
    $result = array();
    if ( !isset($db) && isset($this->_database) ) $db = $this->_database;
    if ( isset($db) ) {

      if ( $res = $this->query("SHOW TABLE STATUS FROM `$db`") ) {
        while ( $row = $res->nextarray() ) {
          $indexes = $this->fetchIndexes($row[0], $db);
          $fields = $this->fetchFields($row[0], $db);
          $constraints = array();
          if ( $row["Engine"] == "InnoDB" ) {

            $cparts = explode("; ", $row["Comment"]);
            $comment = preg_match("/^InnoDB free:/i", $c = trim($cparts[0])) ? "" : $c;

            if ( $tabres = $this->query("SHOW CREATE TABLE `$db`.`" . $row["Name"] . "`") ) {
              $obj = $tabres->nextarray();
              if ( preg_match_all("/(CONSTRAINT `([0-9_]+)` )?(FOREIGN KEY) \(([^)]+)\) REFERENCES `(([A-Z0-9_$]+)(\.([A-Z0-9_$]+))?)` \(([^)]+)\)( ON (DELETE|UPDATE)( (CASCADE|SET NULL|NO ACTION|RESTRICT)))?/i", $obj["Create Table"], $matches, PREG_SET_ORDER) ) {
                foreach ( $matches AS $match ) {
                  $constraints[$match[4]] = array(
                      "name" => $match[4],
                      "id" => $match[2],
                      "engine" => $match[3],
                      "targetdb" => isset($match[8]) && trim($match[8]) != "" ? $match[6] : $db,
                      "targettable" => isset($match[8]) && trim($match[8]) != "" ? $match[8] : $match[6],
                      "targetcols" => $match[9],
                      "params" => isset($match[10]) ? $match[10] : NULL,
                    );
                }
              }
              $tabres->destroy();
            }
          } else $comment=trim($row["Comment"]);
          $result[$row["Name"]] = array(
            "database" => $db,
            "name" => $row["Name"],
            "engine" => $row["Engine"],
            "options" => $row["Create_options"],
            "auto_incr" => isset($row["Auto_increment"]) ? $row["Auto_increment"] : NULL,
            "comment"=>$comment,
            "fields"=>$fields,
            "idx"=>$indexes,
            "constraints"=>$constraints
          );
          if ( isset($row["Collation"]) ) {
            $result[$row["Name"]]["collate"] = $row["Collation"];
          } else $result[$row["Name"]]["collate"] = "";
        }
        $res->destroy();
      }
    }
    //print_r($result);
    return count($result) ? $result : NULL;
  }

  function serverVersion() {
    if ( preg_match("/^((\d+)\.(\d+)\.(\d+))/", mysql_get_server_info($this->_con), $matches) ) {
      return array("version"=>$matches[1], "major"=>(int)$matches[2], "minor"=>(int)$matches[3], "revision"=>(int)$matches[4]);
    } else return NULL;
  }

  function serverVersionCompare($version) {
    $info=$this->serverVersion();
    if ( preg_match("/^(\d+)(\.(\d+)(\.(\d+))?)?$/", $version, $matches) ) {
      $server=sprintf("%03d%04d%05d", $info["major"], $info["minor"], $info["revision"]);
      $version=sprintf("%03d%04d%05d", $matches[1], isset($matches[3])?$matches[3]:0, isset($matches[5])?$matches[5]:0);
      if ( $server > $version ) return 1;
      if ( $server < $version ) return -1;
      return 0;
    } else return FALSE;
  }

  function serverVersionString() {
    $version = $this->serverVersion();
    return $version["version"];
  }

  function serverVersionInteger() {
    if ( preg_match("/^((\d+)\.(\d+)\.(\d+))/", mysql_get_server_info($this->_con), $matches) ) {
      return (int)$matches[2] * 10000 + (int)$matches[3] * 100 + (int)$matches[4];
    } else return 0;
  }

  function error() {
    return "[$this->_lasterrno] $this->_lasterror<br />$this->_laststatement";
  }

  function __error($stat="", $file=NULL, $line=NULL) {
    GLOBAL $database_show_errors;

    $this->_laststatement=$stat;
    $this->_lasterrno=mysql_errno($this->_con);
    $this->_lasterror=mysql_error($this->_con);
    $this->_lasterrorpos=( isset($file) && $file!="" && isset($line) && $line!="" ? $file.":".$line : NULL );
  }

  function escapestring($str) {
    if ( version_compare(phpversion(), "4.3.0", ">=") ) {
      return mysql_real_escape_string($str, $this->_con);
    } else return mysql_escape_string($str);
  }

}