<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/wact/src/tags/fetch/fetch.tag.php');

/**
 * @tag active_record:FETCH,ar:FETCH
 * @req_const_attributes class_path
 * @package web_app
 * @version $Id: active_record_fetch.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbActiveRecordFetchTag extends WactFetchTag
{
  var $runtimeComponentName = 'lmbActiveRecordFetchComponent';
  var $runtimeIncludeFile = 'limb/web_app/src/wact/fetch/lmbActiveRecordFetchComponent.class.php';

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


