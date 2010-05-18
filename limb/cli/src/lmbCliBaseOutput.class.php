<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliOutputInterface.interface.php');

/**
 * class lmbCliBaseOutput
 *
 * @package cli
 * @version $Id$
 */
abstract class lmbCliBaseOutput implements lmbCliOutputInterface
{
  abstract function exception(lmbException $exception);
}


