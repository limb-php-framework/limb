<?php
/**
 * class lmbMacroSpaceTag.
 * @tag space
 * @aliases sp
 * @forbid_end_tag
 */
class lmbMacroSpaceTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml(" ");
  }
}
