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
* @tag message_box, messagebox
*/
class lmbMessageBoxTag extends WactFetchTag
{
  function preParse()
  {
    if($this->getBoolAttribute('errors'))
      $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbMessageBoxErrorsFetcher');
    elseif($this->getBoolAttribute('messages'))
      $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbMessageBoxMessagesFetcher');
    else
      $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbMessageBoxFetcher');


    return parent :: preParse();
  }

}

?>