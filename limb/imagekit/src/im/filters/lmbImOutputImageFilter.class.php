<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageFilter.class.php');

/**
 * Change output type
 * @package imagekit
 * @version $Id$
 */
class lmbImOutputImageFilter extends lmbAbstractImageFilter
{

  function apply(lmbAbstractImageContainer $container)
  {
    $container->setOutputType($this->getType());
  }

  function getType()
  {
    return $this->getParam('type', '');
  }

}
