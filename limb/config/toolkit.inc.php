<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 4990 2007-02-08 15:35:31Z pachanga $
 * @package    config
 */
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/file_schema/toolkit.inc.php');
lmb_require('limb/config/src/lmbConfTools.class.php');
lmbToolkit :: merge(new lmbConfTools());

?>