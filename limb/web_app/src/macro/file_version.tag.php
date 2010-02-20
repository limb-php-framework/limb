<?php

/**
 * @tag file:version
 * @req_attributes src
 * @forbid_end_tag
 */  

class lmbFileVersionMacroTag extends lmbMacroTag
{  
  protected function _generateContent($code)
  {
    $code->writeHTML(lmbToolkit :: instance()->addVersionToUrl($this->get('src'), $this->getBool('safe')));
  }
}
