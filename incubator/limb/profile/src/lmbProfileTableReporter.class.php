<?php

lmb_require('limb/profile/src/lmbProfilePanelReporter.class.php');

class lmbProfileTableReporter extends lmbProfilePanelReporter
{
  protected function _getHeader()
  {
  	return '
  	  <style>
  	    div.lmb_profile {color: #000; font: 12px verdana, monospace}
  	    div.lmb_profile table { border-bottom:1px solid #000; }
  	    div.lmb_profile table.lmb_profile caption {
  	      font-size:16px;
  	      padding: 15px 0 0 15px;
  	      text-align:left
        }
  	    div.lmb_profile table.profile td, div.lmb_profile table.profile th {
  	      border-top:1px solid #000;
          border-right:1px solid #999;
          text-align: left;
          padding:5px;
        }
  	  </style>
  	  <div class="lmb_profile">
  	';
  }

  protected function _getFooter()
  {
    return '</div>';
  }

  protected function _getSectionHtml($name, $data)
  {
    $html = '<table class="lmb_profile"  cellpadding="0" cellspacing="0">';
    $html .= "<caption>$name</caption>";

    foreach($data as $key => $value)
    {
      $html .= "<tr>";
      $html .= "<th>" . $key . "</th>";
      $html .= "<td>" . $value . "</td>";
      $html .= "</tr>";
    }
    $html .= "</table>\n";
    return $html;
  }

  protected function _getSectionWithQueriesHtml($name, $queries, $row_callback)
  {
    if(!count($queries))
      return;

    $time = 0;
    foreach ($queries as $key => $query)
      $time += $query['time'];

    $ret = '<table class="lmb_profile">';
    $ret .= "<caption>$name (" . count($queries) . "): ". $time ."</caption>";

    foreach($queries as $key => $info)
    {
      $ret .=  "<tr>";
      $ret .=  "<th>" . $key . "</th>";
      $ret .= $this->$row_callback($key, $info);
      if(isset($info['trace']))
      {
        $hash = md5($name).$key;
        $ret .=  "<td><a href='#' onclick='jQuery(\"#trace_".$hash."\").toggle(); return false;'>TRACE</a><div id='lmb_trace_".$hash."' style='display:none;'>".nl2br($info['trace'])."</div></td>";
      }
      $ret .= "</tr>";
    }
    $ret .=  "</table>\n";
    return $ret;
  }
}