<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

/**
 * interface lmbDbQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbDbQueryStatement.interface.php 6221 2007-08-07 07:24:35Z pachanga $
 */
interface lmbDbQueryStatement extends lmbDbStatement
{
  function getOneRecord();
  function getOneValue();
  function getOneColumnAsArray();
  function getRecordSet();
}


