<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: toolkit.inc.php 5024 2007-02-12 13:49:59Z pachanga $
 * @package    active_record
 */


lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/active_record/src/toolkit/lmbActiveRecordTools.class.php');
lmbToolkit :: merge(new lmbActiveRecordTools());

?>