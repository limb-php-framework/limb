<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUriExtractor.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/net/src/lmbUri.class.php');

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
      $uris[] = new lmbUri($matches[$i][$match_number]);

    return $uris;
  }
}

?>
