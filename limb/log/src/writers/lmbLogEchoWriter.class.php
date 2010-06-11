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
 * class lmbLogEchoWriter.
 *
 * @package log
 * @version $Id$
 */
class lmbLogEchoWriter extends lmbLogBaseWriter
{
  function __construct(lmbUri $dsn)
  {

  }

  function write(lmbLogEntry $entry)
  {
    echo $this->_getDelimiter($entry).PHP_EOL.$this->_getDefaultEntryString($entry);
  }
}
