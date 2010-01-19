<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package dbal
 * @version $Id: common.inc.php 8048 2010-01-19 22:12:02Z korchasa $
 */
require_once('limb/core/common.inc.php');
lmb_require('limb/dbal/toolkit.inc.php');
lmb_package_require('toolkit');

lmb_require('limb/dbal/src/exception/lmbDbException.class.php');
lmb_require('limb/dbal/src/lmbDBAL.class.php');

lmb_package_register('dbal', dirname(__FILE__));
