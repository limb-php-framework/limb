<?php
/**
 * class lmbMacroNospaceTag.
 * @tag nospace
 * @restrict_self_nesting
 */
class lmbMacroNospaceTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    lmbMacroTextNode :: setTrim(true);
    parent :: _generateContent($code);
    lmbMacroTextNode :: setTrim(false);
  }
}
