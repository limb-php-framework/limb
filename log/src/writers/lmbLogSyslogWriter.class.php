<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/writers/lmbLogBaseWriter.class.php');

/**
 * class lmbLogSyslogWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogSyslogWriter extends lmbLogBaseWriter
{
  const DELIMITER = '|||';

  function __construct(lmbUri $dsn)
  {
    openlog('LIMB', LOG_ODELAY | LOG_PID, LOG_USER);
    parent::__construct($dsn);
  }

  function _write(lmbLogEntry $entry)
  {
    $message = str_replace("\n", self::DELIMITER ,$entry->asText());
    syslog($entry->getLevel(), $message);
  }

  function __destruct()
  {
    closelog();
  }
}
