<?php

@define('LIMB_VAR_DIR', dirname(__FILE__) . '/../var');

class TaskmanTest extends UnitTestCase 
{  
  function testAcceptance()
  {
    $output = '';
    $logfile = LIMB_VAR_DIR.'/taskman.log';
    file_put_contents($logfile, '');
    
    $str = 'php '.dirname(__FILE__).'/example/build.php "build|remove_old" s -b -D LOG='.$logfile;
    exec($str, $output);
    
    $this->assertEqual('ssosursb', file_get_contents($logfile));
  }
}