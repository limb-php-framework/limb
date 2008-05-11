<?php
/**
 * class lmbMacroTabTag.
 * @tag tab
 * @forbid_end_tag
 */
class lmbMacroTabTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml("\t");
  }
}
