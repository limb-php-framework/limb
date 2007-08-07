<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/wact/src/tags/fetch/fetch.tag.php');

/**
 * @tag flash_box, flashbox
 * @package web_app
 * @version $Id$
 */
class lmbFlashBoxTag extends WactFetchTag
{
  function preParse()
  {
    if($this->getBoolAttribute('errors'))
      $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbFlashBoxErrorsFetcher');
    elseif($this->getBoolAttribute('messages'))
      $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbFlashBoxMessagesFetcher');
    else
      $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbFlashBoxFetcher');

    return parent :: preParse();
  }

}


