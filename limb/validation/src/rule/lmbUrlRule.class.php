<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');  

/**
 * Checks that field value is a valid url.
 * @package validation
 * @version $Id: lmbUrlRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbUrlRule extends lmbDomainRule
{
  /**
  * @var pattern to match an url
  */
   protected $pattern = '#^((?<protocol>https?|ftp)://)?(?<domain>[-A-Z0-9.]+)(?<file>/[-A-Z0-9+&@\#/%=~_|!:,.;]*)?(?<parameters>\?[-A-Z0-9+&@\#/%=~_|!:,.;]*)?$#i';
   
  function check($value)
  {
    if(!preg_match($this->pattern, $value, $matches))
      return $this->error('{Field} is not an url.');

    parent::check($matches['domain']);
  }
}
