<?php
/**
 * class lmbMacroNospaceTag.
 * @tag nospace
 * @aliases -
 * @forbid_end_tag
 */
class lmbMacroNospaceTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml("");
  }
}
