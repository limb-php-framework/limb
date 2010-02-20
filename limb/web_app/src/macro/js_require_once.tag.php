<?php

/**
 * @tag js:require_once
 * @aliases js_once
 * @req_attributes src
 * @forbid_end_tag
 */  

class lmbJsRequireOnceMacroTag extends lmbMacroTag
{  
  static protected $_writes = array();

  protected function _generateContent($code)
  {
    $file = $this->get('src');
    if(isset(self :: $_writes[$code->getClass()][$file]))
      return;
    self :: $_writes[$code->getClass()][$file] = 1;

    $code->writeHTML('<script type="text/javascript" src="' . lmbToolkit :: instance()->addVersionToUrl($file, $this->getBool('safe')) . '" ></script>');
  }
}
