<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbCmsUserRole.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsUserRole extends lmbObject
{
  protected $id;
  protected $name;
  protected $short_name;

  function __construct($id, $name, $short_name)
  {
    $this->id = $id;
    $this->name = $name;
    $this->short_name = $short_name;
  }
}


