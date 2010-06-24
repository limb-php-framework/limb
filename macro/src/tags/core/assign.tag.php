<?php
/**
 * class lmbMacroCopyTag.
 * @tag assign
 * @req_attributes var, value
 * @forbid_end_tag
 */
class lmbMacroAssignTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writePHP($this->get('var') . " = ".$this->get('value').";\n");
  }
}
