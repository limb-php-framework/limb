<?php
class MysqlCommandBuilder {

  var $_highlight = FALSE;
  var $_html = FALSE;
  var $_con = NULL;
  var $_renamed = array();
  var $_options = array('engine' => true, 'charset' => true, 'backticks' => true, 'comment' => false);

  var $_translates = array(
    "signs"=>array("translate" => "<span class=\"signs\">\\1</span>", "items"=>array("/([\.,\(\)-])/im"),),
    "num"=>array("translate"=>"\\1<span class=\"num\">\\2</span>\\3", "items"=>array("/(\b)(\d+)(\b)/im"),),
  );
  var $_reservedwords = array(
    "ADD", "ACTION", "ALL", "ALTER", "ANALYZE", "AND", "AS", "ASC", "ASENSITIVE", "AUTO_INCREMENT",
    "BDB", "BEFORE", "BERKELEYDB", "BETWEEN", "BIGINT", "BINARY", "BIT", "BLOB", "BOTH", "BTREE", "BY",
    "CALL", "CASCADE", "CASE", "CHANGE", "CHAR", "CHARACTER", "CHECK", "COLLATE", "COLUMN", "COLUMNS", "CONNECTION", "CONSTRAINT", "CREATE", "CROSS", "CURRENT_DATE", "CURRENT_TIME", "CURRENT_TIMESTAMP", "CURSOR",
    "DATE", "DATABASE", "DATABASES", "DAY_HOUR", "DAY_MINUTE", "DAY_SECOND", "DEC", "DECIMAL", "DECLARE", "DEFAULT", "DELAYED", "DELETE", "DESC", "DESCRIBE", "DISTINCT", "DISTINCTROW", "DIV", "DOUBLE", "DROP",
    "ENUM", "ELSE", "ELSEIF", "ENCLOSED", "ERRORS", "ESCAPED", "EXISTS", "EXPLAIN",
    "FALSE", "FIELDS", "FLOAT", "FOR", "FORCE", "FOREIGN", "FROM", "FULLTEXT",
    "GRANT", "GROUP",
    "HASH", "HAVING", "HIGH_PRIORITY", "HOUR_MINUTE", "HOUR_SECOND",
    "IF", "IGNORE", "IN", "INDEX", "INFILE", "INNER", "INNODB", "INOUT", "INSENSITIVE", "INSERT", "INT", "INTEGER", "INTERVAL", "INTO", "IS", "ITERATE",
    "JOIN",
    "KEY", "KEYS", "KILL",
    "LEADING", "LEAVE", "LEFT", "LIKE", "LIMIT", "LINES", "LOAD", "LOCALTIME", "LOCALTIMESTAMP", "LOCK", "LONG", "LONGBLOB", "LONGTEXT", "LOOP", "LOW_PRIORITY",
    "MASTER_SERVER_ID", "MATCH", "MEDIUMBLOB", "MEDIUMINT", "MEDIUMTEXT", "MIDDLEINT", "MINUTE_SECOND", "MOD", "MRG_MYISAM",
    "NATURAL", "NO", "NOT", "NULL", "NUMERIC",
    "ON", "OPTIMIZE", "OPTION", "OPTIONALLY", "OR", "ORDER", "OUT", "OUTER", "OUTFILE",
    "PRECISION", "PRIMARY", "PRIVILEGES", "PROCEDURE", "PURGE",
    "READ", "REAL", "REFERENCES", "REGEXP", "RENAME", "REPEAT", "REPLACE", "REQUIRE", "RESTRICT", "RETURN", "RETURNS", "REVOKE", "RIGHT", "RLIKE", "RTREE",
    "SELECT", "SENSITIVE", "SEPARATOR", "SET", "SHOW", "SMALLINT", "SOME", "SONAME", "SPATIAL", "SPECIFIC", "SQL_BIG_RESULT", "SQL_CALC_FOUND_ROWS", "SQL_SMALL_RESULT", "SSL", "STARTING", "STRAIGHT_JOIN", "STRIPED",
    "TABLE", "TABLES", "TERMINATED", "TEXT", "THEN", "TIME", "TIMESTAMP", "TINYBLOB", "TINYINT", "TINYTEXT", "TO", "TRAILING", "TRUE", "TYPES",
    "UNION", "UNIQUE", "UNLOCK", "UNSIGNED", "UNTIL", "UPDATE", "USAGE", "USE", "USER_RESOURCES", "USING",
    "VALUES", "VARBINARY", "VARCHAR", "VARCHARACTER", "VARYING",
    "WARNINGS", "WHEN", "WHERE", "WHILE", "WITH", "WRITE",
    "XOR",
    "YEAR_MONTH",
    "ZEROFILL",
  );
  var $_resources = array(
    "fieldformat_changed_single" => "",
    "fieldformat_changed_multiple" => "",
    "fieldformat_changeinfo" => "",
    "fieldformat_modification_needed" => "",
  );

  function __construct($con, $highlight = FALSE, $html = FALSE) {
    $this->_highlight = $highlight;
    $this->_html = $html;
    $this->_con = $con;
  }

  /*
    "Public" Methods ...
  */
  function addOption($option, $value) {
    $this->_options[$option] = $value;
  }

  function addOptions($options) {
    if ( isset($options) && is_array($options) ) {
      foreach ( $options as $option => $value ) {
        $this->addOption($option, $value);
      }
    }
  }

  function addRenamed(&$renamed) {
    $this->_renamed = &$renamed;
  }

  function addResource($id, $text) {
    $this->_resources[$id] = $text;
  }

  function alterTableContraints($source, $target) {

    $altering = $result = "";
    $altered = 0;

    // Doing handling of foreign key constraints ...
    if ( $this->getOption("cfk_back") && isset($source["constraints"]) ) foreach ( $source["constraints"] AS $vk=>$vf ) {
      if ( !isset($target["constraints"][$vk]) ) {
        if ( $this->getOption("short") ) {
          $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_constraintString($vf, $target["database"], 1, $this->_con->serverVersionString());
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_constraintString($vf, $target["database"], 1, $this->_con->serverVersionString()).$this->_translate(";")."\n";
        $altered++;
      }
    }
    if ( $this->getOption("cfk_back") && isset($target["constraints"]) ) foreach ( $target["constraints"] AS $vk=>$vf ) {
      if ( !isset($source["constraints"][$vk]) ) {
        if ( $this->getOption("short") ) {
          $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_constraintString($vf, $target["database"], 0);
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_constraintString($vf, $target["database"], 0).$this->_translate(";")."\n";
        $altered++;
      }
    }

    if ( $altering != "" ) {
      $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])."\n$altering;\n";
    }
    return $result;
  }

  function alterTable($source, $target) {
    $altering = $result = "";
    $altered=0;
    $alteredfields=NULL;

    $lastfield=NULL;
    // Checking attributes ...
    $added_fields = array();
    foreach ( $target["fields"] AS $vk=>$vf ) {
      if ( !isset($source["fields"][$vk]) ) {
        if ( isset($this->_renamed[$target["name"]]) && in_array($vk, $this->_renamed[$target["name"]]) ) {
          if ( $this->getOption("short") ) {
            $altering.=($altering==""?"":",\n")."    ".( $this->_html ? "<a class=\"script\" href=\"[save]?sc=removerenamed&amp;table=".urlencode($target["name"])."&amp;field=".urlencode(array_search($vk, $this->_renamed[$target["name"]])).( ini_get("session.use_cookies") ? "" : ( (boolean)ini_get("session.use_trans_sid") ? "" : "&amp;".session_name()."=".session_id() ) )."\">" : "" ).$this->_highlightString("CHANGE").( $this->_html ? "</a>" : "" )." ".$this->_objectName(array_search($vk, $this->_renamed[$target["name"]]))." ".$this->_fieldString($target["fields"][$vk]);
          } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".( $this->_html ? "<a class=\"script\" href=\"[save]?sc=removerenamed&amp;table=".urlencode($target["name"])."&amp;field=".urlencode(array_search($vk, $this->_renamed[$target["name"]])).( ini_get("session.use_cookies") ? "" : ( (boolean)ini_get("session.use_trans_sid") ? "" : "&amp;".session_name()."=".session_id() ) )."\">" : "" ).$this->_highlightString("CHANGE").( $this->_html ? "</a>" : "" )." ".$this->_objectName($vk)." ".$this->_fieldString($target["fields"][$vk]).";\n";
        } else {
          $added_fields[] = $vk;
          if ( $this->getOption("short") ) {
            $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_highlightString("ADD")." ".$this->_fieldString($target["fields"][$vk]).( isset($lastfield) ? " ".$this->_highlightString("AFTER")." $lastfield" : " ".$this->_highlightString("FIRST") );
          } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("ADD")." ".$this->_fieldString($target["fields"][$vk]).( isset($lastfield) ? " ".$this->_highlightString("AFTER")." $lastfield" : " ".$this->_highlightString("FIRST") ).$this->_translate(";")."\n";
        }
        $altered++;
      }
      $lastfield=$target["fields"][$vk]["name"];
    }

    foreach ( $source["fields"] AS $vk=>$vf ) {
      if ( isset($target["fields"][$vk]) ) {
        if ( $vf["type"]==$target["fields"][$vk]["type"] && $vf["null"]==$target["fields"][$vk]["null"] && $vf["extra"]==$target["fields"][$vk]["extra"] && $vf["default"]!=$target["fields"][$vk]["default"] ) {
          if ( $this->getOption("short") ) {
            $altering.=($altering==""?"":",\n")."    ".$this->_highlightString("ALTER")." ".$this->_objectName($target["fields"][$vk]["name"])." ".( isset($target["fields"][$vk]["default"]) ? $this->_highlightString("SET DEFAULT")." ".( is_numeric($target["fields"][$vk]["default"]) ? $target["fields"][$vk]["default"] : "'".$target["fields"][$vk]["default"]."'" ) : $this->_highlightString("DROP DEFAULT") );
            $alterfields[]=array( "name"=>$target["name"].".".$target["fields"][$vk]["name"], "from"=>$this->_fieldString($source["fields"][$vk], FALSE), "to"=>$this->_fieldString($target["fields"][$vk], FALSE) );
          } else {
            $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("ALTER")." ".$this->_objectName($target["fields"][$vk]["name"])." ".( isset($target["fields"][$vk]["default"]) ? $this->_highlightString("SET DEFAULT")." ".( is_numeric($target["fields"][$vk]["default"]) ? $target["fields"][$vk]["default"] : "'".$target["fields"][$vk]["default"]."'" ) : " ".$this->_highlightString("DROP DEFAULT") );
            $result .= "#\n#  Fieldformat of '".$target["name"].".$vk' changed from '".$this->_fieldString($source["fields"][$vk], FALSE)." to ".$this->_fieldString($target["fields"][$vk], FALSE).". Possibly data modifications needed!\n#\n\n";
          }
        } else if ( $vf["type"] != $target["fields"][$vk]["type"] || $vf["null"] != $target["fields"][$vk]["null"] || $vf["default"] != $target["fields"][$vk]["default"] || $vf["extra"] != $target["fields"][$vk]["extra"] || ( $this->_con->serverVersionCompare("4.1.0") >= 0 && ( $vf["comment"] != $target["fields"][$vk]["comment"] || (isset($vf["collation"])?$vf["collation"]:NULL) != (isset($target["fields"][$vk]["collation"])?$target["fields"][$vk]["collation"]:NULL)) ) ) {
          if ( $this->getOption("short") ) {
            $altering.=($altering==""?"":",\n")."    ".$this->_highlightString("MODIFY")." ".$this->_fieldString($target["fields"][$vk]);
            $alteredfields[]=array( "name"=>$target["name"].".".$target["fields"][$vk]["name"], "from"=>$this->_fieldString($source["fields"][$vk], FALSE), "to"=>$this->_fieldString($target["fields"][$vk], FALSE) );
          } else {
            $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("MODIFY")." ".$this->_fieldString($target["fields"][$vk]).";\n";
            $result .= "#\n#  Fieldformat of '".$target["name"].".$vk' changed from '".$this->_fieldString($source["fields"][$vk], FALSE)." to ".$this->_fieldString($target["fields"][$vk], FALSE).". Possibly data modifications needed!\n#\n\n";
          }
          $altered++;
        }
      } else {
        if ( !isset($this->_renamed[$target["name"]][$vk]) ) {
          $addedfieldnames = "";
          foreach ( $added_fields AS $addfld ) {
            $addedfieldnames .= ( $addedfieldnames=="" ? "" : "&amp;" )."fields[]=".urlencode($addfld);
          }
          if ( $this->getOption("short") ) {
            $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".( $add=(isset($added_fields) && count($added_fields)) && $this->_html ? "<a class=\"script\" href=\"[script]?sc=field&amp;table=".urlencode($target["name"])."&amp;field=".urlencode($vk)."&amp;$addedfieldnames".( ini_get("session.use_cookies") ? "" : ( (boolean)ini_get("session.use_trans_sid") ? "" : "&amp;".session_name()."=".session_id() ) )."\">" : "" ).$this->_highlightString("DROP").( $add && $this->_html ? "</a>" : "" )." ".$this->_objectName($vk);
          } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".( $add=(isset($added_fields) && count($added_fields)) && $this->_html ? "<a class=\"script\" href=\"[script]?sc=field&amp;table=".urlencode($target["name"])."&amp;field=".urlencode($vk)."&amp;$addedfieldnames".( ini_get("session.use_cookies") ? "" : ( (boolean)ini_get("session.use_trans_sid") ? "" : "&amp;".session_name()."=".session_id() ) )."\">" : "" ).$this->_highlightString("DROP").( $add && $this->_html ? "</a>" : "" )." ".$this->_objectName($vk).$this->_translate(";")."\n";
        }
        $altered++;
      }
    }

    // Checking keys ...
    if ( isset($source["idx"]) ) foreach ( $source["idx"] AS $vk=>$vf ) {
      if ( isset($target["idx"][$vk] ) ) {
        if ( $this->_fieldsdiff($vf["fields"], $target["idx"][$vk]["fields"]) ) {
          if ( $this->getOption("short") ) {
            $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_highlightString("DROP")." ".( $vf["unique"] && $vk=="PRIMARY" ? $this->_highlightString("PRIMARY KEY") : $this->_highlightString("INDEX")." ".$this->_objectName($vk) ).$this->_translate(",")."\n    ".$this->_highlightString("ADD")." ".$this->_indexString($target["idx"][$vk]);
          } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("DROP")." ".( $vf["unique"] && $vk=="PRIMARY" ? $this->_highlightString("PRIMARY KEY") : $this->_highlightString("INDEX")." $vk" ).$this->_translate(";\n").$this->_highlightString("ALTER TABLE")." ".$target["name"]." ".$this->_highlightString("ADD")." ".$this->_indexString($target["idx"][$vk]).$this->_translate(";")."\n";
        }
      } else {
        if ( $this->getOption("comment") ) {
          $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_highlightString("DROP")." ".( $vf["unique"] && $vk=="PRIMARY" ? $this->_highlightString("PRIMARY KEY") : $this->_highlightString("INDEX")." ".$this->_objectName($vk) );
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("DROP")." ".( $vf["unique"] && $vk=="PRIMARY" ? $this->_highlightString("PRIMARY KEY") : $this->_highlightString("INDEX")." $vk" ).$this->_translate(";")."\n";
        $altered++;
      }
    }
    if ( isset($target["idx"]) ) foreach ( $target["idx"] AS $vk=>$vf ) {
      if ( !isset($source["idx"][$vk]) ) {
        if ( $this->getOption("short") ) {
          $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_highlightString("ADD")." ".$this->_indexString($vf);
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("ADD")." ".$this->_indexString($vf).$this->_translate(";")."\n";
        $altered++;
      }
    }


    // Doing handling of foreign key constraints ...
    if ( !$this->getOption("cfk_back") && isset($source["constraints"]) ) foreach ( $source["constraints"] AS $vk=>$vf ) {
      if ( !isset($target["constraints"][$vk]) ) {
        if ( $this->getOption("short") ) {
          $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_constraintString($vf, $target["database"], 1, $this->_con->serverVersionString());
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_constraintString($vf, $target["database"], 1, $this->_con->serverVersionString()).$this->_translate(";")."\n";
        $altered++;
      }
    }
    if ( !$this->getOption("cfk_back") && isset($target["constraints"]) ) foreach ( $target["constraints"] AS $vk=>$vf ) {
      if ( !isset($source["constraints"][$vk]) ) {
        if ( $this->getOption("short") ) {
          $altering.=($altering==""?"":$this->_translate(",")."\n")."    ".$this->_constraintString($vf, $target["database"], 0);
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_constraintString($vf, $target["database"], 0).$this->_translate(";")."\n";
        $altered++;
      }
    }

    // Charset ...
    if ( $this->getOption("charset") ) {
      if ( $source["collate"]!=$target["collate"] ) {
        $charsetinfo = explode("_", $target["collate"]);

        $charset = $this->_highlightString("DEFAULT CHARSET").$this->_translate("=").$this->_highlightString($charsetinfo[0], "const")." ".$this->_highlightString("COLLATE").$this->_translate("=").$this->_highlightString($target["collate"], "const");

        if ( $this->getOption("short") ) {
          $altering.=($altering==""?"":$this->_translate(", ")).$charset;
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$charset.$this->_translate(";")."\n";
      }
    }

    // table options ...
    $tableoptions = "";
    if ( $this->getOption("engine") ) {
      if ( $source["engine"]!=$target["engine"] ) {
        if ( $this->getOption("short") ) {
          $tableoptions .= ($tableoptions == "" ? ( $altering == "" ? "    " : "" ) : $this->_translate(" ")).$this->_highlightString("ENGINE").$this->_highlightstring("=", "signs").$this->_highlightstring($target["engine"], "const");
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("ENGINE").$this->_highlightstring("=", "signs").$this->_highlightstring($target["engine"], "const").$this->_translate(";")."\n";
        $altered++;
      }
    }

    if ( $this->getOption("options") ) {
      if ( $source["options"]!=$target["options"] ) {
        if ( $this->getOption("short") ) {
          $tableoptions .= ($tableoptions == "" ? ( $altering == "" ? "    " : "" ) : $this->_translate(" ")).$target["options"];
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString($target["options"], "values").$this->_translate(";")."\n";
        $altered++;
      }
    }
    if ( $this->getOption("auto_incr") ) {
      if ( $source["auto_incr"] != $target["auto_incr"] ) {
        if ( $this->getOption("short") ) {
          $tableoptions .= ($tableoptions == "" ? ( $altering == "" ? "    " : "" ) : $this->_translate(" ")).$this->_highlightString("AUTO_INCREMENT").$this->_highlightstring("=", "signs").$this->_con->escapestring($target["auto_incr"]);
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("AUTO_INCREMENT").$this->_highlightstring("=", "signs").$this->_con->escapestring($target["auto_incr"]).$this->_highlightstring(";", "signs")."\n";
        $altered++;
      }
    }
    if ( $this->getOption("comment") ) {
      if ( $source["comment"]!=$target["comment"] ) {
        if ( $this->getOption("short") ) {
          $tableoptions .= ($tableoptions == "" ? ( $altering == "" ? "    " : "" ) : $this->_translate(" ")).$this->_highlightString("COMMENT").$this->_highlightstring("=", "signs")."'".$this->_con->escapestring($target["comment"]).$this->_translate("'");
        } else $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])." ".$this->_highlightString("COMMENT").$this->_highlightstring("=", "signs")."'".$this->_con->escapestring($target["comment"]).$this->_translate("';")."\n";
        $altered++;
      }
    }
    if ( $tableoptions != "" ) {
      if ( $this->getOption("short") ) {
        $altering .= ( $altering=="" ? "":$this->_translate(",\n    ")).$tableoptions;
      }
    }

    // the end ...
    if ( $altering != "" ) {
      $result .= $this->_highlightString("ALTER TABLE")." ".$this->_objectName($target["name"])."\n$altering;\n";
      if ( isset($alteredfields) && $this->getOption("changes") ) {
        $result .= "#\n";
        $result .= "#  ".$this->_resources["fieldformat_changed".( count($alteredfields)==1 ? "_single" : "_multiple" )]."\n";
        foreach ( $alteredfields AS $val ) {
          $result .= "#    ".sprintf($this->_resources["fieldformat_changeinfo"], $val["name"], $val["from"], $val["to"])."\n";
        }
        $result .= "#  ".$this->_resources["fieldformat_modification_needed"]."\n";
        $result .= "#";
      }
    }
    return $result;
  }

  function createTable($table) {
    $item = "";
    $item .= $this->_highlightString("CREATE TABLE")." ".$this->_objectName($table["name"])." ".$this->_translate("(")."\n";
    $idx = 1; $max = count($table["fields"]);
    foreach ( $table["fields"] AS $vf ) {
      $item .= "    ".$this->_fieldString($vf).( $idx<$max || count($table["idx"]) ? $this->_translate(",") : "" )."\n";
      $idx++;
    }
    $idx=1; $max = count($table["idx"]);
    if ( isset($table["idx"]) ) foreach ( $table["idx"] AS $vx ) {
      $item .= "    ".$this->_indexString($vx).( $idx < $max || ( isset($table["constraints"]) && count($table["constraints"]) ) ? "," : "" )."\n";
      $idx++;
    }

    // Doing handling of foreign key constraints ...
    if ( isset($table["constraints"]) ) {
      $idx = 1; $max = count($table["constraints"]);
      foreach ( $table["constraints"] AS $vk=>$vf ) {
        $item .= "    ".$this->_constraintString($vf, $table["database"], 2).( $idx < $max ? "," : "" )."\n";
        $idx++;
      }
    }

    $item.=$this->_translate(")");
    if ( $this->getOption("engine") ) {
      if ( isset($table["engine"]) && $table["engine"] != "" ) {
        $item .= " ".$this->_highlightString("ENGINE").$this->_highlightstring("=", "signs").$this->_highlightstring($table["engine"], "const");
      }
    }
    if ( $this->getOption("options") ) {
      if ( isset($table["options"]) && $table["options"] != "" ) {
        $item .= " ".$table["options"];
      }
    }
    if ( $this->getOption("charset") ) {
      if ( isset($table["collate"]) && $table["collate"] != "" ) {
        $charsetinfo = explode("_", $table["collate"]);

        $charset = $this->_highlightString("DEFAULT CHARSET").$this->_translate("=").$this->_highlightString($charsetinfo[0], "const")." ".$this->_highlightString("COLLATE").$this->_translate("=").$this->_highlightString($table["collate"], "const");

        $item .= " ".$charset;
      }
    }
    if ( $this->getOption("comment") ) {
      if ( isset($table["comment"]) && $table["comment"] != "" ) {
        $item .= " ".$this->_highlightString("COMMENT").$this->_highlightstring("=", "signs")."'".$this->_con->escapestring($table["comment"]).$this->_translate("'");
      }
    }
    $item .= $this->_translate(";");
    return $item;
  }

  function dropTable($table) {
    return $this->_highlightString("DROP TABLE")." ".$this->_objectName($table["name"]).$this->_translate(";");
  }

  function getOption($option) {
    if ( isset($this->_options[$option]) ) {
      return $this->_options[$option];
    } else return NULL;
  }

  function insertRecord($table, $data) {
    return $this->_insertreplaceRecord($table, $data, "INSERT");
  }

  function replaceRecord($table, $data) {
    return $this->_insertreplaceRecord($table, $data, "REPLACE");
  }

  function setMySqlVariable($variable, $value) {
    return $this->_highlightString("SET ", "dml").$this->_highlightString($variable, "obj").$this->_highlightString(" = ", "signs").( is_numeric($value) ? $this->_highlightString($value, "num") : $this->_highlightString("'$value'", CMDBH_VALUE) ).$this->_highlightString(";", "signs");
  }

  /*
    "Private" methods ...
  */
  function _alternateNullDefault($type) {
    if ( strtolower(substr($type, 0, 4))=="int(" || strtolower(substr($type, 0, 8))=="bigint(" || strtolower(substr($type, 0, 8))=="smallint(" || strtolower(substr($type, 0, 8))=="tinyint(" || strtolower(substr($type, 0, 10))=="mediumint(" ) {
      $result="0";
    } else if ( strtolower(substr($type, 0, 8))=="datetime" ) {
      $result="0000-00-00 00:00:00";
    } else if ( strtolower(substr($type, 0, 4))=="date" ) {
      $result="0000-00-00";
    } else if ( strtolower(substr($type, 0, 4))=="time" ) {
      $result="00:00:00";
    } else {
      $result="''";
    }
    return $result;
  }

  function _constraintString($idx, $targetdb, $what = 0, $serverversion = NULL) {
    if ( $what == 0 ) {
      $result = $this->_highlightString("ADD CONSTRAINT")." ".$this->_highlightString($idx["type"]) . $this->_translate(" (") . $idx["name"] . $this->_translate(") ") . $this->_highlightString("REFERENCES") . " " . $this->_objectName( ( $targetdb != $idx["targetdb"] ? $idx["targetdb"] . "." : "" ) . $idx["targettable"]) . $this->_translate(" (") . $idx["targetcols"] . $this->_translate(")").( isset($idx["params"]) && trim($idx["params"]) != "" ? $this->_highlightString($idx["params"]) : "" );
    } else if ( $what == 1 && isset($serverversion) && $this->_con->serverVersionCompare("4.0.13") >= 0 ) {
      $result = $this->_highlightString("DROP ".$idx["type"])." ".$idx["id"];
    } else if ( $what == 2 ) {
      $result = $this->_highlightString("CONSTRAINT")." ".$this->_highlightString($idx["type"]) . $this->_translate(" (") . $idx["name"] . $this->_translate(") ") . $this->_highlightString("REFERENCES") . " " . $this->_objectName( ( $targetdb != $idx["targetdb"] ? $idx["targetdb"] . "." : "" ) . $idx["targettable"]) . $this->_translate(" (") . $idx["targetcols"] . $this->_translate(")").( isset($idx["params"]) && trim($idx["params"]) != "" ? $this->_highlightString($idx["params"]) : "" );
    } else $result = "";
    return $result;
  }

  function _fieldsDiff($f1, $f2) {
    if ( count($f1) != count($f2) ) return TRUE;
    foreach ($f1 AS $key=>$value) {
      if ( !isset($f2[$key]) || $value["name"]!=$f2[$key]["name"] ) return TRUE;
    }
    return FALSE;
  }

  function _fieldString($field, $withname=TRUE) {
    $result = "";
    if ( $withname ) $result .= $this->_objectName($field["name"])." ";
    $result .= $this->_typeString($field["type"]);
    $result .= " ".$this->_highlightString(( $field["null"] ? "" : "NOT " )."NULL", "const");

    if(!isset($field["extra"]) || strstr($field["extra"], "auto_increment") === false)
    {
      $result .= " ".$this->_highlightString("DEFAULT", "ddl");
      if ( isset($field["default"]) ) {
        $result .= " ".$this->_highlightstring("'".$field["default"]."'", "values");
      } else {
        $result .= " ".($field["null"] ? $this->_highlightString("NULL", "const") : $this->_highlightstring($this->_alternateNullDefault($field["type"]), "values"));
      }
    }

    if ( isset($field["comment"]) && $this->_con->serverVersionCompare("4.1.0") >= 0 ) {
      $result .= " ".$this->_highlightString("COMMENT")." ".$this->_highlightString("'".$field["comment"]."'", "values");
    }
    if ( $this->getOption("charset") && isset($field["collate"]) && $this->_con->serverVersionCompare("4.1.0") >= 0 ) {
      $result .= " ".$this->_highlightString("COLLATE")." ".$this->_highlightString($field["collate"], "const");
    }
    if ( isset($field["extra"]) && $field["extra"]!="" ) {
      $result .= " ".$field["extra"];
    }

    return $result;
  }

  function _highlightString($what, $kind = "ddl") {
    if ( $this->_highlight ) {
      return "<span class=\"$kind\">$what</span>";
    } else return $what;
  }

  function _indexNull($idx, $table="b") {
    $fields="";
    if ( isset($idx["fields"]) && is_array($idx["fields"]) ) foreach ( $idx["fields"] AS $key=>$value ) {
      $fields.=( $fields=="" ? "" : " AND " )."$table.$key IS NULL";
    }
    return $fields;
  }

  function _indexOn($idx, $tableA="a", $tableB="b") {
    $fields="";
    if ( isset($idx["fields"]) && is_array($idx["fields"]) ) foreach ( $idx["fields"] AS $key=>$value ) {
      $fields.=( $fields=="" ? "" : " AND " )."$tableA.$key=$tableB.$key";
    }
    return $fields;
  }

  function _indexString($idx) {
    $result = ( $idx["type"] == "FULLTEXT" ? $this->_highlightString("FULLTEXT INDEX") . ( isset($idx["name"]) ? " ".$this->_objectName($idx["name"]) : "" ) : ( $idx["unique"] ? ( $idx["name"]=="PRIMARY" ? $this->_highlightString("PRIMARY KEY") : $this->_highlightString("UNIQUE")." ".$this->_objectName($idx["name"]) ) : $this->_highlightString("INDEX")." ".$this->_objectName($idx["name"]) ) )." (";
    $i = 1; $im = count($idx["fields"]);
    foreach ( $idx["fields"] AS $vf ) {
      $result .= $this->_objectName($vf["name"]).( isset($vf["sub"]) ? "(".$vf["sub"].")" : "" ).( $i<$im ? ", " : "" );
      $i++;
    }
    $result .= ")";
    return $result;
  }

  function _insertreplaceRecord($table, $data, $what = "INSERT") {
    $values = $fields = "";
    foreach ( $data AS $fieldname => $fieldvalue ) {
      $fields .= ($fields==""?"":",").$this->_objectName($fieldname);
      $values .= ($values==""?"":",")."'".$this->_con->escapestring($fieldvalue)."'";
    }
    return $this->_highlightString("$what INTO", "dml")." ".$this->_objectName($table)." ".$this->_translate("(").$fields.$this->_translate(") ").$this->_highlightString("VALUES", "dml").$this->_translate(" (").$values.$this->_translate(");");
  }

  function _objectName($name) {
    return $this->_highlightString($this->getOption("backticks") || preg_match("/[^a-z0-9_$]/i", $name) || in_array(strtoupper($name), $this->_reservedwords) ? "`".$name."`" : $name, "obj");
  }

  function _translate($item) {
    if ( $this->_highlight ) foreach ( $this->_translates AS $types ) {
      foreach ( $types["items"] AS $items ) {
        $item=preg_replace($items, $types["translate"], $item);
      }
    }
    return str_replace("  ", "&nbsp;&nbsp;", $item);
  }

  function _typeString($type) {
    if ( $this->_highlight ) $type = preg_replace(array("/([(])(\\d+)([)])/", "/([(])(([']\\w+['])([,]\\s*(([']\\w+['])))*)([)])/"), array("<span class=\"signs\">$1</span><span class=\"num\">$2</span><span class=\"signs\">$3</span>", "<span class=\"signs\">$1</span><span class=\"values\">$2</span><span class=\"signs\">$7</span>"), $type);
    return $this->_highlightString($type, "type");
  }
}