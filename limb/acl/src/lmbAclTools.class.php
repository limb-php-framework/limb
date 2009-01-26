<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/acl/src/lmbAcl.class.php');

/**
 * class lmbFsTools.
 *
 * @package fs
 * @version $Id$
 */
class lmbAclTools extends lmbAbstractTools
{
  protected $acl = null;

  function getAcl()
  {
    if(is_null($this->acl))
      $this->acl = new lmbAcl();

    return $this->acl;
  }

  function setAcl($acl)
  {
    $this->acl = $acl;
  }
}
