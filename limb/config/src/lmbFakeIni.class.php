<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */
lmb_require('limb/config/src/lmbIni.class.php');

class lmbFakeIni extends lmbIni
{
  function __construct($contents)
  {
    $this->_parseLines(explode("\n", $contents));
  }
}

?>
