<?php
require_once (dirname(__FILE__) . '/MysqlConnection.class.php');
require_once (dirname(__FILE__) . '/MysqlCommandBuilder.class.php');

function sameConnection(&$con1, &$con2) {
  return $con1->_con == $con2->_con;
}
function generateScript($cfg_source, $cfg_target) {
  $result = '';
  $syntax = false;
  $html = false;

  $sourcehost = $cfg_source["hostname"];
  $targethost = $cfg_target["hostname"];

  // Flags for temporary databases ...
  $s_temp = $t_temp = FALSE;

  $scon = new MysqlConnection($sourcehost, $cfg_source["username"], $cfg_source["password"]);
  if ( $scon->open() ) {

    $s_db = $cfg_source["database"];
    if ( isset($s_db) && !empty($s_db) && $scon->selectDatabase($s_db) ) {

      $tcon = new MysqlConnection($targethost,  $cfg_target["username"], $cfg_target["password"]);
      if ( $tcon->open() ) {
          $t_db = $cfg_target["database"];

        if ( isset($t_db) && !empty($t_db) && $tcon->selectDatabase($t_db) ) {

          $builder = new MysqlCommandBuilder($tcon, $syntax, $html);

          $s_tab = $scon->fetchTables($s_db);
          $t_tab = $tcon->fetchTables($t_db);

          if ( is_array($t_tab) ) foreach ( $t_tab AS $key=>$value ) {
            if ( !isset($s_tab[$key]) ) {
              $item = $builder->createTable($t_tab[$key]);
              $result .= $item == "" ? "" : $item."\n\n";
            }
          }
          if ( is_array($s_tab) ) foreach ( $s_tab AS $key=>$value ) {
            if ( isset($t_tab[$key]) ) {
              $item = $builder->alterTable($s_tab[$key], $t_tab[$key]);
              $result .= $item == "" ? "" : $item."\n\n";
            } else {
              $item = $builder->dropTable($s_tab[$key]);
              $result .= $item == "" ? "" : $item."\n\n";
            }
          }

          if ( is_array($s_tab) ) foreach ( $s_tab AS $key=>$value ) {
            if ( isset($t_tab[$key]) ) {
              $item = $builder->alterTableContraints($s_tab[$key], $t_tab[$key]);
              $result .= $item == "" ? "" : $item."\n\n";
            }
          }

        } else $result .= $tcon->error()."\n";

        $tcon->close();
      } else $result .= $tcon->error()."\n";
    } else $result .= $scon->error()."\n";

    $scon->close();
  } else $result .= $scon->error()."\n";

  if($result)
  {
    return "\n".$builder->setMySqlVariable("FOREIGN_KEY_CHECKS", 0)."\n\n" .
           $result .
           $builder->setMySqlVariable("FOREIGN_KEY_CHECKS", 1)."\n";
  }
}