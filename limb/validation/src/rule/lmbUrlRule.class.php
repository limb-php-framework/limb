<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbDomainRule.class.php');

/**
 * Checks that field value is a valid Url.
 * @package validation
 * @version $Id: lmbUrlRule.class.php 6467 2007-10-29 15:24:16Z serega $
 */
class lmbUrlRule extends lmbDomainRule
{
  /**
  * @var array List of allowable schemes e.g. array('http', 'ftp');
  */
  protected $allowable_schemes =  array();

  /**
  * Constructor.
  * @param string Field name
  * @param array List of allowable schemes
  */
  function __construct($field_name, $allowable_schemes = array(), $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->allowable_schemes = $allowable_schemes ? $allowable_schemes : array();
  }

  function check($value)
  {
    $url = @parse_url($value);
    if (isset($url['scheme']) || isset($url['host']) &&
        ($url['scheme'] == 'http' || $url['scheme'] == 'ftp'))
    {
      parent::check($url['host']);
    }

    if (!sizeof($this->allowable_schemes))
      return;

    if (!isset($url['scheme']))
    {
      $this->error('Please specify a scheme for {Field}.');
      return;
    }

    if (!in_array($url['scheme'], $this->allowable_schemes))
      $this->error('{Field} may not use {scheme}.', array('scheme' => $url['scheme']));
  }
}

