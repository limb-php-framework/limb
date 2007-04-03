<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/net/src/lmbNetTools.class.php');
lmbToolkit :: merge(new lmbNetTools());

?>