<?php
/**
 * class lmbLogBaseWriter.
 *
 * @package log
 * @version $Id$
 */
lmb_require('limb/log/src/lmbLogWriter.interface.php');

abstract class lmbLogBaseWriter implements lmbLogWriter
{
    /**
     * @param lmbLogEntry $entry
     * @return string
     */
    protected function _getDelimiter($entry)
    {
      $time = strftime("%b %d %Y %H:%M:%S", $entry->getTime());
      $delimiter_len = (int) ((80 - strlen($time)) / 2) - 2;
      return str_repeat('=', $delimiter_len)."[{$time}]".str_repeat('=', $delimiter_len);
    }

    /**
     * @param lmbLogEntry $entry
     * @return string
     */
    protected function _getDefaultEntryString($entry)
    {
      $log_message = $entry->getLevelForHuman().' '.$entry->getMessage().PHP_EOL;
      if($entry->getParams())
        $log_message .= 'Additional params: '.strstr(print_r($entry->getParams(), true), PHP_EOL);
      $log_message .= 'Backtrace: '.PHP_EOL.$entry->getBacktrace()->toString();
      return $log_message;
    }
}