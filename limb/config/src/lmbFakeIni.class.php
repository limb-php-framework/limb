<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedIni.class.php 5201 2007-03-07 08:06:56Z pachanga $
 * @package    config
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
