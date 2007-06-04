<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciClob.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require(dirname(__FILE__) . '/lmbOciLob.class.php');

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

?>
