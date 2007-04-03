<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: util.inc.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */

/**
* Replace the constants name with relative values in $string.
* Constants name must be enclosed in $ symbols.
*/
function lmb_replace_constants(&$string)
{
  $constPos = strpos($string, '%');
  while (is_integer($constPos))
  {
    $constant = substr($string, $constPos+1, strpos($string, '%', $constPos+1) - $constPos - 1);
    if (defined($constant))
      $string = str_replace("%$constant%", constant($constant), $string);
    else
      return $constant;

    $constPos = strpos($string, '%');
  }
  return true;
}

?>
