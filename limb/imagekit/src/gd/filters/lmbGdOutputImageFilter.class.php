<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package imagekit
 * @version $Id: lmbGdOutputImageFilter.class.php 6333 2007-09-24 16:38:22Z cmz $
 */
lmb_require(dirname(__FILE__).'/../../lmbAbstractImageFilter.class.php');

/**
 * Change output type
 * @package imagekit
 * @version $Id: lmbGdOutputImageFilter.class.php 6333 2007-09-24 16:38:22Z cmz $
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
?>