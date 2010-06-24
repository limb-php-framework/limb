<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/imagekit/tests/cases/lmbBaseImageContainerTest.class.php');

class lmbGdImageContainerTest extends lmbBaseImageContainerTest
{
  protected $driver = 'gd';
}