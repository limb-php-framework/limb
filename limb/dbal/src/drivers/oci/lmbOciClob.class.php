<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require(dirname(__FILE__) . '/lmbOciLob.class.php');

/**
 * class lmbOciClob.
 *
 * @package dbal
 * @version $Id: lmbOciClob.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbOciClob extends lmbOciLob
{
  function getDescriptorType()
  {
    return OCI_D_LOB;
  }

  function getEmptyExpression()
  {
    return 'EMPTY_CLOB()';
  }

  function getNativeType()
  {
    return OCI_B_CLOB;
  }
}


