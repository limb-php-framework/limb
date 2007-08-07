<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/imagekit/src/lmbImageGd.class.php');
lmb_require(dirname(__FILE__) . '/../lmbImageLibraryTestBase.class.php');

class lmbGdLibraryTest extends lmbImageLibraryTestBase
{
  var $rotated_size = 4479;
  var $hflipped_size = 4011;
  var $wflipped_size = 3932;
  var $cutted_size1 = 1403;
  var $cutted_size2 = 4722;
  var $cutted_size3 = 1243;
  var $cutted_size4 = 1931;

  function __construct()
  {
    $this->library = new lmbImageGd();
    parent :: __construct();
  }
}

