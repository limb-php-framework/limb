<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/dbal/common.inc.php');
return lmbToolkit :: instance()->getDefaultDbConnection()->getType() != 'pgsql';

