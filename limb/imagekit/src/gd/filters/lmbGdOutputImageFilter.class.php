<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageFilter.class.php');

/**
 * Change output type
 * @package imagekit
 * @version $Id: lmbGdOutputImageFilter.class.php 7071 2008-06-25 14:33:29Z korchasa $
 */
class lmbGdOutputImageFilter extends lmbAbstractImageFilter {

  function apply(lmbAbstractImageContainer $container)
  {
    $container->setOutputType($this->getType());
  }

  function getType()
  {
    return $this->getParam('type', '');
  }

}
