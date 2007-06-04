<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactBlockComponent.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* The block tag can be used to show or hide the contents of the block.
* The WactBlockComponent provides an API which allows the block to be shown
* or hidden at runtime.
*/
class WactBlockComponent extends WactRuntimeComponent
{
  protected $is_visible = TRUE;

  function isVisible()
  {
    return $this->is_visible;
  }

  function show()
  {
    $this->is_visible = TRUE;
  }

  function hide()
  {
    $this->is_visible = FALSE;
  }
}
?>