<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/view/src/lmbView.class.php');

/**
 * class lmbDummyView.
 *
 * @package view
 * @version $Id$
 */
class lmbDummyView extends lmbView
{
  static function locateTemplateByAlias($alias)
  {
    return $alias;
  }

  function render()
  {
    return '';
  }
}

