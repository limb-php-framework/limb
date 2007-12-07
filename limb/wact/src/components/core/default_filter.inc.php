<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package wact
 * @version $Id: default_filter.inc.php 6598 2007-12-07 08:01:45Z pachanga $
 */
function WactApplyDefault($value, $default)
{
  if (empty($value) && $value !== "0" && $value !== 0)
    return $default;
  else
    return $value;
}

