<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbUriFilter.
 *
 * @package web_spider
 * @version $Id: lmbUriFilter.class.php 7903 2009-04-26 18:36:36Z slevin $
 */
class lmbUriFilter
{
  protected $allowed_protocols = array();
  protected $allowed_hosts = array();

  protected $allowed_path_regexes = array();
  protected $disallowed_path_regexes = array();

  function allowProtocol($protocol)
  {
    $this->allowed_protocols[] = strtolower($protocol);
  }

  function allowHost($host)
  {
    $this->allowed_hosts[] = strtolower($host);
  }

  function allowPathRegex($regex)
  {
    $this->allowed_path_regexes[] = $regex;
  }

  function disallowPathRegex($regex)
  {
    $this->disallowed_path_regexes[] = $regex;
  }

  function canPass($uri)
  {
    if(!in_array($uri->getProtocol(), $this->allowed_protocols))
      return false;

    if(!in_array($uri->getHost(), $this->allowed_hosts))
      return false;

    if(!sizeof($this->allowed_path_regexes))
      return false;

    foreach($this->disallowed_path_regexes as $regex)
      if(preg_match($regex, $uri->getPath()))
        return false;

    foreach($this->allowed_path_regexes as $regex)
      if(!preg_match($regex, $uri->getPath()))
        return false;

    return true;
  }
}


