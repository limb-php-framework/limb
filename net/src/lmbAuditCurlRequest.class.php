<?php
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/core/src/lmbArrayHelper.class.php');
lmb_require('limb/net/src/lmbCurlRequest.class.php');

class lmbAuditCurlRequest extends lmbCurlRequest
{
  static protected $_stats = array();

  protected function _exec()
  {
    $url = $this->_url;
    if(isset($this->opts[CURLOPT_POSTFIELDS]))
      $url .= " ( " . $this->opts[CURLOPT_POSTFIELDS] . " ) ";

    $info = array('query' => $url);
    $info['trace'] = $this->getTrace();

    $start_time = microtime(true);

    $res = parent :: _exec();

    $info['time'] = round(microtime(true) - $start_time, 6);
    self :: $_stats[] = $info;

    return $res;
  }

  function getTrace()
  {
    $trace_length = 8;
    $offset = 4; // getting rid of useless trace elements

    $trace = new lmbBacktrace($trace_length, $offset);
    return $trace->toString();
  }

  static function getStats()
  {
    return self :: $_stats;
  }
}
