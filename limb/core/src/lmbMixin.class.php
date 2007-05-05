<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    core
 */

class lmbMixin
{
  protected $owner;

  function __construct($owner = null)
  {
    $this->owner = $owner;
  }

  function setOwner($owner)
  {
    $this->owner = $owner;
  }

}
?>
