<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5161 2007-02-28 08:26:24Z pachanga $
 * @package    view
 */
lmb_require('limb/file_schema/toolkit.inc.php');
lmb_require('limb/config/toolkit.inc.php');

lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/view/src/toolkit/lmbViewTools.class.php');
lmbToolkit :: merge(new lmbViewTools());

?>