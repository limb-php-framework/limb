<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: .ignore.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once('limb/dbal/common.inc.php');
return lmbToolkit :: instance()->getDefaultDbConnection()->getType() != 'pgsql';
?>
