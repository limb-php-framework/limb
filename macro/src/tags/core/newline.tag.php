<?php
/**
 * class lmbMacroNewlineTag.
 * @tag newline
 * @aliases nl
 * @forbid_end_tag
 */
class lmbMacroNewlineTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml("\n");
  }
}
