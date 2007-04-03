<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5427 2007-03-29 14:54:01Z pachanga $
 * @package    dbal
 */
lmb_require('limb/config/toolkit.inc.php');

lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/dbal/src/toolkit/lmbDbTools.class.php');
lmbToolkit :: merge(new lmbDbTools());

?>