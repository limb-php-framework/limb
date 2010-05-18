<?php

lmb_require('limb/profile/src/lmbProfileBaseReporter.class.php');

class lmbProfilePanelReporter extends lmbProfileBaseReporter
{
	protected function _getHeader()
	{
		$header =<<<EOD
<style>
div.lmb_profile {
  position:absolute;
  top:0px;
  right:0px;
  padding:0px;
  margin:0px;
  background-color:#666;
  color:#FFF;
  z-index:1000000;
  font: 15px Arial, sans-serif;
}
div.lmb_profile ul { padding:0px; margin:0px; text-align:right; }
  div.lmb_profile ul li { border-right:1px solid #CCC; display: inline; padding:0px 5px }
    div.lmb_profile table, .lmb_trace { display:none; }
      div.lmb_profile td, div.lmb_profile th {
        padding: 5px;
        text-align:left;
        border-bottom:1px solid #000;
        border-right:1px solid #999;
        font-size:11px;
    }
div.lmb_profile #lmb_section_content { background-color:#EEE; color:#333;}

</style>

<script type="text/javascript">
function showSection(hash)
{
  closeSections();
  var content = document.getElementById(hash).cloneNode(true);
  content.style.display = "block";
  var section = document.getElementById("lmb_section_content");
  section.appendChild(content);
  return false;
}
function closeSections()
{
  var section = document.getElementById("lmb_section_content");
  if(0 != section.childNodes.length)
    section.removeChild(section.childNodes[0]);
  return false;
}
function showTrace(hash)
{
  jQuery("#lmb_section_content #" + hash).toggle();
  return false;
}
</script>

<div class="lmb_profile">
<ul>
EOD;
      return $header;
	}

  protected function _getFooter()
  {
    $footer =<<<EOD
    <li onclick="return closeSections();">X</li>
  </ul>
  <div id="lmb_section_content"></div>
</div>
EOD;
    return $footer;
  }

  protected function _getRowForSqlQuery($key, $info)
  {
    $ret =  "<td>" . round($info['time'], 6) . "</td>\n";
    $ret .=  "<td>" . $info['query'] . "</td>\n";
    if(isset($info['result']))
      $ret .=  "<td>" . $info['result'] . "</td>\n";
    return $ret;
  }

  function _getPHPVariables()
  {
    $result = array();
    foreach($_SERVER as $key => $value)
      $result['$_SERVER["'. $key .'"]'] = $value;
    foreach($_REQUEST as $key => $value)
      $result['$_REQUEST["'. $key .'"]'] = $value;
    foreach($_ENV as $key => $value)
      $result['$_ENV["'. $key .'"]'] = $value;
    return $result;
  }

  protected function _getSectionHtml($name, $data)
  {
  	$hash = md5($name);
    $html = "<li onclick='return showSection(\"lmb_section_$hash\")'>";
    $html .= $name."<table id='lmb_section_$hash' class='lmb_profile' border='1'>\n";

    foreach($data as $key => $value)
    {
      $html .= "<tr>".PHP_EOL;
      $html .= "<th>" . htmlspecialchars($key) . "</th>".PHP_EOL;
      $html .= "<td><pre>" . htmlspecialchars(var_export($value, true)) . "&nbsp;</pre></td>".PHP_EOL;
      $html .= "</tr>".PHP_EOL;
    }
    $html .= "</table></li>".PHP_EOL;
    return $html;
  }

  protected function _getSectionWithQueriesHtml($name, $queries, $row_callback)
  {
  	if(!count($queries))
  	  return '';

    $time = 0;
    foreach ($queries as $key => $query)
      $time += $query['time'];

    $section_name = $name.": ".count($queries)." / ".round($time, 3)." s";
    $section_hash = md5($name);

    $ret = "<li onclick='return showSection(\"lmb_section_$section_hash\")'>\n";
    $ret .= $section_name."<table id='lmb_section_$section_hash' border='1'>\n";

    foreach($queries as $key => $info)
    {
      $ret .=  "<tr>\n";
      $ret .=  "<th>" . ($key + 1) . "</th>\n";
      $ret .= $this->$row_callback($key, $info);
      if(isset($info['trace']))
      {
      	$hash = md5($name).$key;
        $ret .= "<td>\n";
        $ret .= "<a href='#' onclick='return showTrace(\"lmb_trace_$hash\")'>TRACE</a>\n";
        $ret .= "<div id='lmb_trace_".$hash."' class='lmb_trace'>".nl2br($info['trace'])."</div>\n";
        $ret .= "</td>\n";
      }
      $ret .= "</tr>\n";
    }
    $ret .=  "</table></li>\n";
    return $ret;
  }

  function getReport()
  {
    $html = $this->_getHeader();

    $html .= $this->_getSectionHtml(
	    'Main: '.round($this->script_time, 3).' s',
        array(
        'time' => $this->script_time,
        'memory' => $this->script_memory,
        'peak memory' => $this->script_peak_memory,
      )
    );

    $html .= $this->_getSectionWithQueriesHtml('DB', $this->sql_queries, '_getRowForSqlQuery');

    $html .= $this->_getSectionWithQueriesHtml('Cache', $this->cache_queries, '_getRowForSqlQuery');

    $vars = $this->_getPHPVariables();
    ksort($vars);
    $html .= $this->_getSectionHtml('Env', $vars);

    $html .= $this->_getFooter();

    return $html;
	}
}