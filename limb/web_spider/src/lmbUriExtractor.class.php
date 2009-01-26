<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUri.class.php');

/**
 * class lmbUriExtractor.
 *
 * @package web_spider
 * @version $Id: lmbUriExtractor.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbUriExtractor
{
  protected function _defineUriRegex()
  {
    return '/(<a.*?href=(?:"|\'|)([^"\'>\s]+)(?:"|\'|).*?>)(.*?)<\/a>/s';
  }

  protected function _defineRegexMatchNumber()
  {
    return 2;
  }

  function &extract($content)
  {
    preg_match_all($this->_defineUriRegex(),
                   $content,
                   $matches,
                   PREG_SET_ORDER);

    $uris = array();

    $match_number = $this->_defineRegexMatchNumber();

    for ($i=0; $i < sizeof($matches); $i++)
    {
      try {	
        $uris[] = new lmbUri($matches[$i][$match_number]);
      }
      catch(lmbException $e) {};
    }	
    return $uris;
  }
}


