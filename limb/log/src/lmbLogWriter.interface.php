<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogEntry.class.php');
lmb_require('limb/net/src/lmbUri.class.php');

interface lmbLogWriter {
    function __construct(lmbUri $dsn);
	function write(lmbLogEntry $entry);
}