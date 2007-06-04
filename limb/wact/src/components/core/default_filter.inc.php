<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: default_filter.inc.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

function WactApplyDefault($value, $default)
{
  if (empty($value) && $value !== "0" && $value !== 0)
    return $default;
  else
    return $value;
}
?>