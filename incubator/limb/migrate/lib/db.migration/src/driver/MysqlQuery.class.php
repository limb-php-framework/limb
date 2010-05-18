<?php

class MysqlQuery {

  var $_res;
  var $_parent;
  var $_rowarray = array();
  var $_row = NULL;

  function __construct($stat, &$parent, $file=NULL, $line=NULL) {
    GLOBAL $database_profile_mode;

    $this->_parent = &$parent;
    if ( $this->_res = mysql_query($stat, $this->_parent->_con) ) {
      $this->currow = 0;
      $numrows = @mysql_num_rows($this->_res);
      $this->_parent->__error($stat);
    } else {
      $this->_parent->__error($stat, isset($file)?$file:NULL, isset($line)?$line:NULL);
      $numrows=0;
    }
  }

  function destroy() {
    if ( isset($this->_res) && $this->_res!="" ) mysql_free_result($this->_res);
  }

  function created() { return isset($this->_res); }

  function count() { return mysql_num_rows($this->_res); }

  function nextarray($type=MYSQL_BOTH) {
    if ( $this->_res ) $this->_rowarray = mysql_fetch_array($this->_res, $type);
    else $this->_rowarray=NULL;
    return isset($this->_rowarray) ? $this->_rowarray : NULL;
  }

  function next() {
    if ( $this->_res ) $this->_row = mysql_fetch_object($this->_res);
    else $this->_row=NULL;
    return isset($this->_row) ? $this->_row : NULL;
  }

}