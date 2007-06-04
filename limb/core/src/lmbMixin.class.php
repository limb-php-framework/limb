<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */

class lmbMixin
{
  protected $owner;

  function setOwner($owner)
  {
    $this->owner = $owner;
  }

}
?>
