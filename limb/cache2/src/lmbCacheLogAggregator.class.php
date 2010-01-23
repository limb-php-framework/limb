<?php
lmb_require('limb/cache2/lmbCacheLog.class.php');
lmb_require('limb/core/src/exception/lmbException.class.php');

class lmbCacheLogAggregator
{
  protected $log_file;

  function __construct($log_file)
  {
    $this->log_file = $log_file;
  }

  function aggregate()
  {
    if(!rename($this->log_file, $new_name = tempnam(lmb_var_dir(), 'cache_log_aggregator')))
      throw new lmbException('Can\'t move file', array('source' => $this->log_file, 'destination' => $new_name));

    $cmd = "cat ".$new_name.' | awk \' {if($NF>1){data[$2"/"$1]++;}} END{for(i in data){print i"/"data[i];}}\'';
    $output = array();
    exec($cmd, $output);
    unlink($new_name);

    $info = array();

    foreach($output as $string)
    {
      list($cache_name, $result, $count) = explode('/', $string);

      if(!isset($info[$cache_name]))
        $info[$cache_name] = array();

      if(lmbCacheLog::PREFIX_HIT === $result)
      {
        $info[$cache_name]['hits_count'] = $count;
      }
      elseif (lmbCacheLog::PREFIX_MISS === $result)
      {
        $info[$cache_name]['misses_count'] = $count;
      }
      else
      {
        throw new lmbException('WTF?', array($string));
      }
    }

    return $info;
  }
}
