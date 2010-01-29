<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogWriter.interface.php');

/**
 * class lmbLogEchoWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogSyslogWriter implements lmbLogWriter
{
  const DELIMITER = '|||';

  function __construct(lmbUri $dsn)
  {
    openlog('LIMB', LOG_ODELAY | LOG_PID, LOG_USER);
  }

  function write(lmbLogEntry $entry)
  {
    $message = $entry->getLevelForHuman().': '.str_replace("\n", self::DELIMITER ,$entry->getMessage());
    syslog($entry->getLevel(), $message);
  }

  function __destruct()
  {
    closelog();
  }
}
