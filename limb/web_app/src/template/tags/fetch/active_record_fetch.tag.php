<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: active_record_fetch.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
require_once('limb/wact/src/tags/fetch/fetch.tag.php');

/**
* @tag active_record:FETCH,ar:FETCH
* @req_const_attributes class_path
*/
class lmbActiveRecordFetchTag extends WactFetchTag
{
  var $runtimeComponentName = 'lmbActiveRecordFetchComponent';
  var $runtimeIncludeFile = 'limb/web_app/src/template/components/lmbActiveRecordFetchComponent.class.php';

  function preParse()
  {
    //attribute USING for this tag is an alias for CLASS_PATH attribute for simplicity
    if($using = $this->getAttribute('using'))
    {
      $this->setAttribute('class_path', $using);
      $this->removeAttribute('using');
    }
    $this->setAttribute('using', 'limb/web_app/src/fetcher/lmbActiveRecordFetcher');

    return parent :: preParse();
  }

  function generateBeforeContent($code)
  {
    parent :: generateBeforeContent($code);

    $code->writePhp($this->getComponentRefCode() .
                      '->setAdditionalParam("class_path", "' . $this->getAttribute('class_path') . '");');

    if($find = $this->getAttribute('find'))
      $code->writePhp($this->getComponentRefCode() .
                      '->setAdditionalParam("find", "' . $find . '");');
  }
}

?>