<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: active_record_fetch.tag.php 5421 2007-03-29 12:49:10Z serega $
 * @package    web_app
 */
require_once('limb/wact/src/tags/fetch/fetch.tag.php');

/**
* @tag flash_box, flashbox
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

?>