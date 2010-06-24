<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

/**
 * interface lmbDbManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbDbManipulationStatement.interface.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface lmbDbManipulationStatement extends lmbDbStatement
{
  function getAffectedRowCount();
}


