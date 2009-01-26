<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/i18n/common.inc.php');
lmb_require('limb/i18n/src/translation/lmbDictionaryUpdater.class.php');
lmb_require('limb/i18n/src/translation/lmbQtDictionaryBackend.class.php');

/**
 * class I18nCliCmd.
 *
 * @package i18n
 * @version $Id$
 */
class I18nCliCmd extends lmbCliBaseCmd
{
  function execute($argv)
  {
    $this->help($argv);
  }

  function updateTranslations($argv)
  {
    $input = new lmbCliInput('t|test');
    $input->read($argv, false);

    $dry_run = $input->isOptionPresent('t');

    $input_dir = realpath($input->getArgument(0, '.'));

    if(!$input_dir)
      $this->_error('Input directory is not valid');

    $output_dir = realpath($input->getArgument(1, $input_dir . '/i18n/translations'));

    if(!$output_dir)
      $this->_error('Output directory is not valid');

    $qt = new lmbQtDictionaryBackend();
    $qt->setSearchPath($output_dir);

    $util = new lmbDictionaryUpdater($qt, $this->output);

    if($dry_run)
      $util->dryrun($input_dir);
    else
      $util->updateTranslations($input_dir);

    return 0;
  }

  function ut($argv)
  {
    return $this->updateTranslations($argv);
  }

  function help($argv)
  {
    $txt = <<<EOD
Usage:
  i18n update-tranlsations(ut) [-t|--test] [<src_dir>] [<dictionary_dir>]

  Updates all translation dictionaries with new untranslated entries(currently only Qt
  dictionaries supported). Parses PHP and WACT templates sources in <src_dir>(current dir .
  by default) and updates dictionaries in <dictionary_dir>(./i18n/translations by default)

Options:
  -t, --test    performs 'dry-run' without updating dictionaries, useful for previewing updates

EOD;

    echo $txt;
  }
}


