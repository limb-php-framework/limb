<?php
/**
 * class lmbMacroCurlyBraceOpensTag.
 * @tag curly_brace_opens
 * @aliases cbo
 * @forbid_end_tag
 */
class lmbMacroCurlyBraceOpensTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml("{");
  }
}

/**
 * class lmbMacroCurlyBraceClosesTag.
 * @tag curly_brace_closes
 * @aliases cbc
 * @forbid_end_tag
 */
class lmbMacroCurlyBraceClosesTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $code->writeHtml("}");
  }
}
