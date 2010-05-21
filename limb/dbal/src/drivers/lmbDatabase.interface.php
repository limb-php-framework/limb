<?php

interface lmbDatabase 
{
	function cleanup();
  function dumpSchema($file);
  function dumpData($file);
  function load($file);  
  /*function drop($tables);
  function flush($tables);*/
}

?>