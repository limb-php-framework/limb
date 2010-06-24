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
 * class lmbPHPView.
 *
 * @package view
 * @version $Id$
 */
class lmbPHPView extends lmbView
{  
  function render()
  {
    extract($this->getVariables());
    ob_start();
    include($this->getTemplate());
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
  }
}

