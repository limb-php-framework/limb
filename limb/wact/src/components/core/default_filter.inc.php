<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package wact
 * @version $Id: default_filter.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
function WactApplyDefault($value, $default)
{
  if (empty($value) && $value !== "0" && $value !== 0)
    return $default;
  else
    return $value;
}

