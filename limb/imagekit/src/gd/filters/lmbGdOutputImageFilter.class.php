<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../lmbAbstractImageFilter.class.php');

/**
 * Change output type
 * @package imagekit
 * @version $Id: lmbGdOutputImageFilter.class.php 6963 2008-04-28 04:04:31Z svk $
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
