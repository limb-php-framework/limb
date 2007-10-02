<?php

include('start.inc.php');

for($i=0;$i<1000;$i++)
{
  $name = 'Bob';
  ob_start();
  include(dirname(__FILE__) . '/tpl/native.phtml');
  $content = ob_get_contents();
  ob_end_clean();
}

include('end.inc.php');

