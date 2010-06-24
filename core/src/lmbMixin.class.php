<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMixin.
 *
 * @package core
 * @version $Id$
 */
class lmbMixin
{
  protected $owner;

  function setOwner($owner)
  {
    $this->owner = $owner;
  }

}

