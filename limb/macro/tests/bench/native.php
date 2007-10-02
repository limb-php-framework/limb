<?php

include('start.inc.php');

$name = 'Bob';

for($i=0;$i<1000;$i++)
{
  ob_start();
  include(dirname(__FILE__) . '/tpl/native.phtml');
  $content = ob_get_contents();
  ob_end_clean();
}

include('end.inc.php');

