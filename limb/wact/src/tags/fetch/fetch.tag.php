<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/tags/fetch/WactBaseFetchingTag.class.php');

/**
 * @tag fetch
 * @req_const_attributes using to
 * @package wact
 * @version $Id$
 */
class WactFetchTag extends WactBaseFetchingTag
{
  protected $runtimeComponentName = 'WactFetchComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/fetch/WactFetchComponent.class.php';

  function generateBeforeContent($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->setFetcherName("' . $this->getAttribute('using') .'");');

    $code->writePhp($this->getComponentRefCode() . '->setIncludePath("' . $this->getAttribute('include') .'");');

    if($this->hasAttribute('cache_dataset') && !$this->getBoolAttribute('cache_dataset'))
    {
      $code->writePhp($this->getComponentRefCode() . '->setCacheDataset(false);');
    }

    parent :: generateBeforeContent($code);
  }
}


