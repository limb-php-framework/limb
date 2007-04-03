<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciClob.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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
