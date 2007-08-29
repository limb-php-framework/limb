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
 * class lmbOciBlob.
 *
 * @package dbal
 * @version $Id: lmbOciBlob.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbOciBlob extends lmbOciLob
{
  function getDescriptorType()
  {
    return OCI_D_LOB;
  }

  function getEmptyExpression()
  {
    return 'EMPTY_BLOB()';
  }

  function getNativeType()
  {
    return OCI_B_BLOB;
  }
}


