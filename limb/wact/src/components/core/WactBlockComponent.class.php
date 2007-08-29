<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * The block tag can be used to show or hide the contents of the block.
 * The WactBlockComponent provides an API which allows the block to be shown
 * or hidden at runtime.
 * @package wact
 * @version $Id: WactBlockComponent.class.php 6243 2007-08-29 11:53:10Z pachanga $
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

