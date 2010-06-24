<?php

require_once(dirname(__FILE__) . '/profile.inc.php');

$name = 'Bob';

profile_start();

for($i=0;$i<1000;$i++)
{
  ob_start();
  include(dirname(__FILE__) . '/tpl/native.phtml');
  $content = ob_get_contents();
  ob_end_clean();
}

profile_end("running $i iterations of include");
