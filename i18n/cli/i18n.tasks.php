<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/i18n/src/scanner/lmbTSDocument.class.php');
lmb_require('limb/i18n/src/scanner/lmbI18nScanner.class.php');

/**
 *
 * @desc scanning folder for {{i18n and {{__ tags and write messages into .ts file
 * @param path_to_ts_file folder1 [ folder2 folder3 ... ]
 */
function task_i18n_scan($args = array())
{
  if(count($args)<2)
  {
    taskman_msg("usage: " . basename(__FILE__) . " folder/for/scan path/to/ts/file.ts folder_for_scan1 folder2 fold3 ...");
    exit(1);
  } 
  
  $ts_file = array_shift($args);

  $scanner = new lmbI18nScanner($args);
  $scanner->scan();
  $scanner->searchMessages();
  _exclude_existing_messages($ts_file,$scanner);
  _write_new_messages_in_ts_file($ts_file, $scanner);
}

/**
 *
 * @deprecated 
 */
function  task_scan($args = array())
{
  task_i18n_scan($args);
}

function _exclude_existing_messages($file,$scanner)
{
  $doc = new DOMDocument;
  $doc->preserveWhiteSpace = false;
  $error = !$doc->load($file,LIBXML_NONET);
  if($error)
  {
    taskman_sysmsg("can't load XML-data from ".$file);
    exit(1);
  }
  $xpath = new DOMXPath($doc);
  $entries = $xpath->query('//TS/context/message/source');

  foreach ($entries as $entry)
  {
    $scanner->deleteMessage($entry->nodeValue);
  }
}

function _write_new_messages_in_ts_file($file,$scanner)
{  
  $doc = new lmbTSDocument;
  $doc->preserveWhiteSpace = false;
  $error = !$doc->load($file,LIBXML_NONET );
  if($error)
  {
    taskman_sysmsg("can't load XML-data from ".$file);
    exit(1);
  }
  foreach($scanner->getMessages() as $message)
  {
    $doc->addMessage($message);
  }

  if(false === $doc->save($file))
  {
    taskman_sysmsg("can't write in file: {$file}");
    exit(1);
  }
}