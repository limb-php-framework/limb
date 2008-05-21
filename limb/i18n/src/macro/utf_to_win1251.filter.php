<?php
/**
 * @filter utf_to_win1251
 */
class UtfToWin1251 extends lmbMacroFunctionBasedFilter
{
  protected $function = 'lmb_utf8_to_win1251';
  protected $include_file = 'limb/i18n/utf8.inc.php';
}
?>